<?php

declare(strict_types=1);

namespace App\Tools\AssetDepreciationCalculator\Presentation\Controllers;

use App\Core\Access\Services\ToolFeatureRequestAuthorizer;
use App\Core\Export\Contracts\PdfExporter;
use App\Core\Export\Contracts\SpreadsheetExporter;
use App\Core\Export\Services\ToolResultExportFactory;
use App\Core\Money\Money;
use App\Http\Controllers\Controller;
use App\Tools\AssetDepreciationCalculator\Application\Actions\CalculateTool;
use App\Tools\AssetDepreciationCalculator\Application\Actions\ShowToolPage;
use App\Tools\AssetDepreciationCalculator\Application\Data\CalculationInput;
use App\Tools\AssetDepreciationCalculator\Infrastructure\Models\RegisteredAsset;
use App\Tools\AssetDepreciationCalculator\Infrastructure\Repositories\RegisteredAssetRepository;
use App\Tools\AssetDepreciationCalculator\Presentation\Requests\ExecuteToolRequest;
use App\Tools\AssetDepreciationCalculator\Presentation\Requests\StoreAssetRequest;
use App\Tools\AssetDepreciationCalculator\Tool;
use Illuminate\Contracts\View\View;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

final class ToolController extends Controller
{
    public function index(Request $request, ShowToolPage $page, ToolFeatureRequestAuthorizer $features, Tool $module, RegisteredAssetRepository $assets): View
    {
        return view('tools-calculadora-depreciacao-ativos::index', [
            ...$page->execute(),
            'plusEnabled' => $features->plusEnabled($module, $request),
            'portfolioProjectionAllowed' => $features->allows($module, 'portfolio_projection', $request),
            'registeredAssets' => $request->user() ? $assets->forUser((int) $request->user()->getAuthIdentifier()) : collect(),
        ]);
    }

    public function calculate(ExecuteToolRequest $request, CalculateTool $action, ToolFeatureRequestAuthorizer $features, Tool $module, ShowToolPage $page, RegisteredAssetRepository $assets): View
    {
        $data = $request->validated();
        $features->requireIf(($data['method'] ?? 'linear') !== 'linear', $module, 'methods', $request);
        $hasAdditionalAssets = count($data['registered_asset_ids'] ?? []) > 0;
        foreach (($data['assets'] ?? []) as $asset) {
            $value = trim((string) ($asset['value'] ?? ''));
            if ($value !== '' && Money::fromDecimal($value)->minorAmount() > 0) {
                $hasAdditionalAssets = true;
            }
            if (($asset['method'] ?? 'linear') !== 'linear' && $value !== '' && Money::fromDecimal($value)->minorAmount() > 0) {
                $features->require($module, 'methods', $request);
            }
        }
        $features->requireIf($hasAdditionalAssets, $module, 'multiple_assets', $request);
        $userId = $request->user() ? (int) $request->user()->getAuthIdentifier() : null;
        $input = $this->input($data, $assets, $userId);
        $result = $action->execute($input);
        $request->flash();

        return view('tools-calculadora-depreciacao-ativos::index', [
            ...$page->execute(),
            'result' => $result,
            'calculationInput' => $data,
            'plusEnabled' => $features->plusEnabled($module, $request),
            'portfolioProjectionAllowed' => $features->allows($module, 'portfolio_projection', $request),
            'registeredAssets' => $userId !== null ? $assets->forUser($userId) : collect(),
        ]);
    }

    public function storeAsset(StoreAssetRequest $request, RegisteredAssetRepository $assets): RedirectResponse
    {
        $data = $request->validated();
        $assets->createForUser((int) $request->user()->getAuthIdentifier(), [
            'name' => trim((string) $data['name']),
            'value_minor' => Money::fromDecimal((string) $data['value'])->minorAmount(),
            'useful_life_years' => (int) $data['useful_life_years'],
            'method' => (string) $data['method'],
        ]);

        return back()->with('asset_registry_success', 'Ativo salvo no cadastro patrimonial da calculadora.');
    }

    public function destroyAsset(Request $request, RegisteredAsset $asset, RegisteredAssetRepository $assets): RedirectResponse
    {
        abort_unless(
            $request->user() && $assets->deleteForUser($asset, (int) $request->user()->getAuthIdentifier()),
            404,
        );

        return back()->with('asset_registry_success', 'Ativo removido do cadastro.');
    }

    public function exportCurrent(
        ExecuteToolRequest $request,
        CalculateTool $action,
        ToolResultExportFactory $documents,
        PdfExporter $pdf,
        SpreadsheetExporter $spreadsheet,
        RegisteredAssetRepository $assets,
        string $format,
    ): Response {
        abort_unless(in_array($format, ['pdf', 'xlsx'], true), 404);
        $userId = $request->user() ? (int) $request->user()->getAuthIdentifier() : null;
        $input = $this->input($request->validated(), $assets, $userId);
        $result = $action->execute($input);
        $filename = 'depreciacao-ativos-'.now()->format('Y-m-d');

        return $format === 'pdf'
            ? $pdf->download($documents->pdf('Calculadora de Depreciação de Ativos', $filename, $result, $input->toArray()))
            : $spreadsheet->download($documents->spreadsheet($filename, $result, $input->toArray()));
    }

    private function input(array $data, RegisteredAssetRepository $registeredAssets, ?int $userId): CalculationInput
    {
        $assets = [[
            'name' => trim((string) $data['asset_name']),
            'value' => (string) $data['asset_value'],
            'residual_value' => (string) ($data['residual_value'] ?? '0'),
            'useful_life_years' => (int) $data['useful_life_years'],
            'method' => (string) $data['method'],
        ]];

        foreach (($data['assets'] ?? []) as $asset) {
            $name = trim((string) ($asset['name'] ?? ''));
            $value = trim((string) ($asset['value'] ?? ''));
            if ($name === '' && ($value === '' || $value === '0' || $value === '0,00')) {
                continue;
            }

            $assets[] = [
                'name' => $name !== '' ? $name : 'Ativo adicional',
                'value' => $value !== '' ? $value : '0',
                'residual_value' => (string) (($asset['residual_value'] ?? '') ?: '0'),
                'useful_life_years' => (int) ($asset['useful_life_years'] ?? 1),
                'method' => (string) ($asset['method'] ?? 'linear'),
            ];
        }

        foreach (($data['registered_asset_ids'] ?? []) as $id) {
            if ($userId === null) {
                break;
            }
            $asset = $registeredAssets->findForUser((int) $id, $userId);
            if (! $asset) {
                continue;
            }
            $assets[] = [
                'name' => $asset->name,
                'value' => $this->decimalFromMinor((int) $asset->value_minor),
                'residual_value' => '0',
                'useful_life_years' => (int) $asset->useful_life_years,
                'method' => (string) $asset->method,
            ];
        }

        return new CalculationInput($assets);
    }

    private function decimalFromMinor(int $minor): string
    {
        $sign = $minor < 0 ? '-' : '';
        $absolute = abs($minor);

        return $sign.intdiv($absolute, 100).'.'.str_pad((string) ($absolute % 100), 2, '0', STR_PAD_LEFT);
    }
}
