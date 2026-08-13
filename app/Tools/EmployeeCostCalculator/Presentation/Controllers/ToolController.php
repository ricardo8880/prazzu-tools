<?php

declare(strict_types=1);

namespace App\Tools\EmployeeCostCalculator\Presentation\Controllers;

use App\Core\Access\Services\ToolFeatureRequestAuthorizer;
use App\Core\Access\Services\ToolPersistenceAuthorizer;
use App\Core\Dates\ReferenceDate;
use App\Core\Export\Contracts\PdfExporter;
use App\Core\Export\Data\PdfDocument;
use App\Core\Export\Services\TabularExportService;
use App\Core\Export\Services\ToolResultExportFactory;
use App\Core\ToolProfiles\Services\ToolProfileManager;
use App\Core\Tools\History\Contracts\ToolRunRecorder;
use App\Core\Tools\History\Data\RuleVersion;
use App\Core\Tools\History\Data\ToolRunHandle;
use App\Http\Controllers\Controller;
use App\Tools\EmployeeCostCalculator\Application\Actions\CalculateEmployeeBatch;
use App\Tools\EmployeeCostCalculator\Application\Actions\CalculateTool;
use App\Tools\EmployeeCostCalculator\Application\Actions\CompareCostScenarios;
use App\Tools\EmployeeCostCalculator\Application\Actions\CompareEmploymentModels;
use App\Tools\EmployeeCostCalculator\Application\Actions\ManageEmployeeCostHistory;
use App\Tools\EmployeeCostCalculator\Application\Actions\PreviewEmployeeImport;
use App\Tools\EmployeeCostCalculator\Application\Actions\ProcessEmployeeImport;
use App\Tools\EmployeeCostCalculator\Application\Actions\ShowToolPage;
use App\Tools\EmployeeCostCalculator\Presentation\Requests\CalculateBatchRequest;
use App\Tools\EmployeeCostCalculator\Presentation\Requests\CompareEmploymentModelsRequest;
use App\Tools\EmployeeCostCalculator\Presentation\Requests\CompareScenariosRequest;
use App\Tools\EmployeeCostCalculator\Presentation\Requests\ExecuteToolRequest;
use App\Tools\EmployeeCostCalculator\Presentation\Requests\PreviewEmployeeImportRequest;
use App\Tools\EmployeeCostCalculator\Presentation\Requests\ProcessEmployeeImportRequest;
use App\Tools\EmployeeCostCalculator\Presentation\Requests\StoreCompanyProfileRequest;
use App\Tools\EmployeeCostCalculator\Presentation\Requests\StoreEmployeeProfileRequest;
use App\Tools\EmployeeCostCalculator\Tool;
use Illuminate\Contracts\View\View;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Validation\ValidationException;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\StreamedResponse;
use Throwable;

final class ToolController extends Controller
{
    public function index(Request $request, ShowToolPage $page, ToolProfileManager $profiles): View
    {
        return view('tools-custo-funcionario-clt::index', [
            ...$page->execute(),
            ...$this->profileData($request, $profiles),
        ]);
    }

    public function calculate(
        ExecuteToolRequest $request,
        CalculateTool $action,
        ShowToolPage $page,
        ToolRunRecorder $recorder,
        ToolPersistenceAuthorizer $persistence,
        ToolProfileManager $profiles,
        Tool $module,
    ): View {
        $input = $request->validated();
        $this->assertCompanyOwnership($request, $profiles, $input);
        $run = $this->startRun($request, $recorder, $persistence, $module, $input);

        try {
            $result = $action->execute($input);
            $this->succeed($recorder, $run, [
                'calculation_type' => 'single',
                'result' => $result->toArray(),
            ]);
        } catch (Throwable $exception) {
            $this->fail($recorder, $run, 'employee_cost.calculation_failed');
            throw $exception;
        }

        $request->flash();

        return view('tools-custo-funcionario-clt::index', [
            ...$page->execute(),
            ...$this->profileData($request, $profiles),
            'result' => $result,
            'calculationInput' => $input,
            'historySaved' => $run !== null,
        ]);
    }

    public function printCurrent(
        ExecuteToolRequest $request,
        CalculateTool $action,
        ToolProfileManager $profiles,
        ToolFeatureRequestAuthorizer $features,
        Tool $module,
    ): View {
        $input = $request->validated();
        $features->requireIf(! empty($input['company_profile_id']), $module, 'branded_report', $request);

        Log::info('Employee cost printable report requested.', [
            'tool' => Tool::SLUG,
            'has_company_profile' => ! empty($input['company_profile_id']),
            'user_id' => $request->user()?->getAuthIdentifier(),
        ]);

        try {
            $company = $this->companyForReport($request, $profiles, $input);
            $result = $action->execute($input)->toArray();

            return view('exports.printable-document', [
                'title' => 'Relatório de Custo de Funcionário CLT',
                'contentView' => 'tools-custo-funcionario-clt::report',
                'contentData' => ['input' => $input, 'result' => $result, 'company' => $company],
                'generatedAt' => now()->format('d/m/Y H:i'),
                'summaryLabel' => 'Custo anual projetado',
                'summaryValue' => $result['annual_cost'] ?? null,
            ]);
        } catch (Throwable $exception) {
            Log::error('Employee cost printable report failed.', [
                'tool' => Tool::SLUG,
                'exception' => $exception::class,
                'message' => $exception->getMessage(),
            ]);

            throw $exception;
        }
    }

    public function downloadPdf(
        ExecuteToolRequest $request,
        CalculateTool $action,
        PdfExporter $exporter,
        ToolResultExportFactory $documents,
    ): Response {
        $input = $request->validated();
        $result = $action->execute($input);

        return $exporter->download($documents->pdf(
            title: 'Custo de Funcionário CLT',
            filename: 'custo-funcionario-clt-'.now()->format('Y-m-d'),
            result: $result,
            input: $input,
        ));
    }

    public function exportCurrent(
        ExecuteToolRequest $request,
        CalculateTool $action,
        string $format,
        TabularExportService $export,
    ): Response|StreamedResponse {
        $input = $request->validated();
        $result = $action->execute($input)->toArray();
        $rows = $this->singleExportRows($input, $result);
        $filename = 'custo-funcionario-clt-'.now()->format('Y-m-d');

        return $format === 'xlsx'
            ? $export->xlsx($filename.'.xlsx', ['Campo', 'Valor'], $rows, 'Custo CLT')
            : $export->csv($filename.'.csv', ['Campo', 'Valor'], $rows);
    }

    public function calculateBatch(
        CalculateBatchRequest $request,
        CalculateEmployeeBatch $action,
        ShowToolPage $page,
        ToolRunRecorder $recorder,
        ToolPersistenceAuthorizer $persistence,
        ToolProfileManager $profiles,
        Tool $module,
    ): View {
        $input = $request->validated();
        $this->assertCompanyOwnership($request, $profiles, $input);
        $run = $this->startRun($request, $recorder, $persistence, $module, $input);

        try {
            $result = $action->execute($input);
            $this->succeed($recorder, $run, [
                'calculation_type' => 'batch',
                'batch' => $result,
            ]);
        } catch (Throwable $exception) {
            $this->fail($recorder, $run, 'employee_cost.batch_failed');
            throw $exception;
        }

        $request->flash();

        return view('tools-custo-funcionario-clt::index', [
            ...$page->execute(),
            ...$this->profileData($request, $profiles),
            'batchResult' => $result,
            'batchInput' => $input,
            'historySaved' => $run !== null,
        ]);
    }

    public function exportBatch(
        CalculateBatchRequest $request,
        CalculateEmployeeBatch $action,
        string $format,
        TabularExportService $export,
    ): Response|StreamedResponse {
        $result = $action->execute($request->validated());
        $rows = array_map(static fn (array $row): array => [
            $row['name'],
            $row['department'],
            $row['role'],
            $row['monthly_cost'],
            $row['annual_cost'],
            $row['hourly_cost'],
        ], $result['employees']);
        $filename = 'custo-clt-consolidado-'.now()->format('Y-m-d');
        $headers = ['Funcionário', 'Departamento', 'Cargo', 'Custo mensal', 'Custo anual', 'Custo por hora'];

        return $format === 'xlsx'
            ? $export->xlsx($filename.'.xlsx', $headers, $rows, 'Folha consolidada')
            : $export->csv($filename.'.csv', $headers, $rows);
    }

    public function printBatch(
        CalculateBatchRequest $request,
        CalculateEmployeeBatch $action,
        ToolProfileManager $profiles,
    ): View {
        $input = $request->validated();
        $result = $action->execute($input);

        return view('exports.printable-document', [
            'title' => 'Relatório Consolidado de Custos CLT',
            'contentView' => 'tools-custo-funcionario-clt::report-batch',
            'contentData' => [
                'batch' => $result,
                'company' => $this->companyForReport($request, $profiles, $input),
            ],
            'generatedAt' => now()->format('d/m/Y H:i'),
        ]);
    }

    public function compareScenarios(
        CompareScenariosRequest $request,
        CompareCostScenarios $action,
        ShowToolPage $page,
        ToolProfileManager $profiles,
    ): View {
        return view('tools-custo-funcionario-clt::index', [
            ...$page->execute(),
            ...$this->profileData($request, $profiles),
            'scenarioResult' => $action->execute($request->validated()),
        ]);
    }

    public function compareEmploymentModels(
        CompareEmploymentModelsRequest $request,
        CompareEmploymentModels $action,
        ShowToolPage $page,
        ToolProfileManager $profiles,
    ): View {
        return view('tools-custo-funcionario-clt::index', [
            ...$page->execute(),
            ...$this->profileData($request, $profiles),
            'employmentComparison' => $action->execute($request->validated()),
        ]);
    }

    public function importTemplate(string $format, TabularExportService $export): Response|StreamedResponse
    {
        $headers = ['Nome', 'Departamento', 'Cargo', 'Salário', 'Média variável', 'Benefícios', 'Regime', 'RAT', 'Terceiros', 'Horas mensais'];
        $rows = [['Funcionário exemplo', 'Administrativo', 'Analista', '5000,00', '0,00', '800,00', 'general', '1', '5.8', '220']];

        return $format === 'xlsx'
            ? $export->xlsx('modelo-funcionarios-custo-clt.xlsx', $headers, $rows, 'Funcionários')
            : $export->csv('modelo-funcionarios-custo-clt.csv', $headers, $rows);
    }

    public function previewImport(
        PreviewEmployeeImportRequest $request,
        PreviewEmployeeImport $action,
        ShowToolPage $page,
        ToolProfileManager $profiles,
    ): View {
        try {
            $preview = $action->execute($request->file('import_file'), $this->importOwnerKey($request));
        } catch (Throwable $exception) {
            throw ValidationException::withMessages(['import_file' => $exception->getMessage()]);
        }

        return view('tools-custo-funcionario-clt::index', [
            ...$page->execute(),
            ...$this->profileData($request, $profiles),
            'importPreview' => $preview,
        ]);
    }

    public function processImport(
        ProcessEmployeeImportRequest $request,
        ProcessEmployeeImport $action,
        ShowToolPage $page,
        ToolProfileManager $profiles,
    ): View {
        try {
            $result = $action->execute($request->validated(), $this->importOwnerKey($request));
        } catch (Throwable $exception) {
            throw ValidationException::withMessages(['import_token' => $exception->getMessage()]);
        }

        return view('tools-custo-funcionario-clt::index', [
            ...$page->execute(),
            ...$this->profileData($request, $profiles),
            'importResult' => $result,
        ]);
    }

    public function storeCompany(StoreCompanyProfileRequest $request, ToolProfileManager $profiles): RedirectResponse
    {
        $profiles->storeCompany((int) $request->user()->getAuthIdentifier(), $request->validated());

        return redirect()->route('tools.custo-funcionario-clt.index')
            ->with('workspace_message', 'Empresa salva para reutilização.');
    }

    public function destroyCompany(Request $request, int $company, ToolProfileManager $profiles): RedirectResponse
    {
        $profiles->deleteCompanyOwned($company, (int) $request->user()->getAuthIdentifier());

        return redirect()->route('tools.custo-funcionario-clt.index')
            ->with('workspace_message', 'Empresa removida.');
    }

    public function storeEmployee(StoreEmployeeProfileRequest $request, ToolProfileManager $profiles): RedirectResponse
    {
        $data = $request->validated();
        $profiles->storeEmployee((int) $request->user()->getAuthIdentifier(), [
            ...$data,
            'defaults' => [
                'salary' => $data['salary'],
                'variable_pay' => $data['variable_pay'],
                'benefits' => $data['benefits'],
                'regime' => $data['regime'],
                'rat' => $data['rat'],
                'third_parties' => $data['third_parties'],
                'monthly_hours' => $data['monthly_hours'],
            ],
        ]);

        return redirect()->route('tools.custo-funcionario-clt.index')
            ->with('workspace_message', 'Funcionário salvo para reutilização.');
    }

    public function destroyEmployee(Request $request, int $employee, ToolProfileManager $profiles): RedirectResponse
    {
        $profiles->deleteEmployeeOwned($employee, (int) $request->user()->getAuthIdentifier());

        return redirect()->route('tools.custo-funcionario-clt.index')
            ->with('workspace_message', 'Funcionário removido.');
    }

    public function history(Request $request, ManageEmployeeCostHistory $history): View
    {
        return view('tools-custo-funcionario-clt::history.index', [
            'runs' => $history->paginate(
                (int) $request->user()->getAuthIdentifier(),
                $request->filled('from') ? $request->string('from')->toString() : null,
                $request->filled('to') ? $request->string('to')->toString() : null,
                max(1, $request->integer('page', 1)),
            ),
        ]);
    }

    public function showHistory(Request $request, string $run, ManageEmployeeCostHistory $history): View
    {
        return view('tools-custo-funcionario-clt::history.show', [
            'run' => $history->find($run, (int) $request->user()->getAuthIdentifier()),
        ]);
    }

    public function repeatHistory(Request $request, string $run, ManageEmployeeCostHistory $history): RedirectResponse
    {
        return redirect()->route('tools.custo-funcionario-clt.index')
            ->withInput($history->repeat($run, (int) $request->user()->getAuthIdentifier()))
            ->with('workspace_message', 'Dados carregados do histórico. Revise e calcule novamente.');
    }

    public function destroyHistory(Request $request, string $run, ManageEmployeeCostHistory $history): RedirectResponse
    {
        $history->delete($run, (int) $request->user()->getAuthIdentifier());

        return redirect()->route('tools.custo-funcionario-clt.history.index')
            ->with('workspace_message', 'Cálculo excluído do histórico.');
    }

    public function printHistory(
        Request $request,
        string $run,
        ManageEmployeeCostHistory $history,
        PdfExporter $pdf,
    ): Response {
        $entry = $history->find($run, (int) $request->user()->getAuthIdentifier());
        $result = $entry->result['result'] ?? $entry->result;

        return $pdf->download(new PdfDocument(
            filename: 'custo-funcionario-clt-historico-'.now()->format('Y-m-d'),
            view: 'exports.printable-document',
            data: [
                'title' => 'Relatório de Custo de Funcionário CLT',
                'contentView' => 'tools-custo-funcionario-clt::report',
                'contentData' => ['input' => $entry->input, 'result' => $result, 'company' => null],
                'generatedAt' => now()->format('d/m/Y H:i'),
                'summaryLabel' => 'Custo anual projetado',
                'summaryValue' => $result['annual_cost'] ?? null,
            ],
        ));
    }

    /** @param array<string, mixed> $input */
    private function startRun(
        Request $request,
        ToolRunRecorder $recorder,
        ToolPersistenceAuthorizer $persistence,
        Tool $module,
        array $input,
    ): ?ToolRunHandle {
        if (! $persistence->allowsHistory($module, $request->user())) {
            return null;
        }

        return $recorder->start(
            module: $module,
            ruleVersion: new RuleVersion('1.1.0'),
            referenceDate: ReferenceDate::fromDateTime(now()),
            input: $input,
            userId: (int) $request->user()->getAuthIdentifier(),
        );
    }

    /** @param array<string, mixed> $result */
    private function succeed(ToolRunRecorder $recorder, ?ToolRunHandle $run, array $result): void
    {
        if ($run !== null) {
            $recorder->succeed($run, $result);
        }
    }

    private function fail(ToolRunRecorder $recorder, ?ToolRunHandle $run, string $code): void
    {
        if ($run !== null) {
            $recorder->fail($run, $code);
        }
    }

    /** @return array<string, mixed> */
    private function profileData(Request $request, ToolProfileManager $profiles): array
    {
        if ($request->user() === null) {
            return ['companies' => collect(), 'employees' => collect()];
        }

        $userId = (int) $request->user()->getAuthIdentifier();

        return [
            'companies' => $profiles->companies($userId),
            'employees' => $profiles->employees($userId),
        ];
    }

    /** @param array<string, mixed> $input */
    private function assertCompanyOwnership(Request $request, ToolProfileManager $profiles, array $input): void
    {
        if (empty($input['company_profile_id'])) {
            return;
        }

        abort_if($request->user() === null, 403);
        $profiles->assertCompanyOwned((int) $input['company_profile_id'], (int) $request->user()->getAuthIdentifier());
    }

    /** @param array<string, mixed> $input */
    private function companyForReport(Request $request, ToolProfileManager $profiles, array $input): ?array
    {
        if ($request->user() === null || empty($input['company_profile_id'])) {
            return null;
        }

        $company = $profiles->findCompanyOwned(
            (int) $input['company_profile_id'],
            (int) $request->user()->getAuthIdentifier(),
        );

        return $company->only([
            'name',
            'legal_name',
            'document',
            'office_name',
            'accountant_name',
            'accountant_registration',
        ]);
    }

    /** @param array<string, mixed> $input @param array<string, mixed> $result */
    private function singleExportRows(array $input, array $result): array
    {
        $rows = [
            ['Ferramenta', 'Calculadora de Custo de Funcionário CLT'],
            ['Data de emissão', now()->format('d/m/Y H:i')],
            ['Funcionário', $input['employee_name'] ?? ''],
            ['Departamento', $input['department'] ?? ''],
        ];

        foreach ($result['summary'] ?? [] as $item) {
            $rows[] = [$item['label'], $item['value']];
        }
        foreach ($result['calculation_memory']['steps'] ?? [] as $step) {
            $rows[] = ['Memória — '.($step['label'] ?? $step['key'] ?? 'Etapa'), $step['result'] ?? ''];
        }

        return $rows;
    }

    private function importOwnerKey(Request $request): string
    {
        return $request->user() !== null
            ? 'employee-cost:user:'.$request->user()->getAuthIdentifier()
            : 'employee-cost:ip:'.($request->ip() ?? 'unknown');
    }
}
