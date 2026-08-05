<?php

declare(strict_types=1);

namespace App\Tools\EmployeeCostCalculator\Tests\Feature;

use App\Core\Access\Enums\CommercialAccessMode;
use App\Core\Access\Enums\SubscriptionPlan;
use App\Core\Imports\Contracts\ImportDatasetStore;
use App\Core\Imports\Data\TabularDataset;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use PHPUnit\Framework\Attributes\DataProvider;
use Tests\TestCase;

final class ImportFeatureAccessTest extends TestCase
{
    use RefreshDatabase;

    #[DataProvider('importFormats')]
    public function test_each_enabled_format_requires_plus_for_preview_and_processing_in_monetized_mode(
        string $format,
    ): void {
        config()->set('access.commercial_mode', CommercialAccessMode::Monetized->value);
        $user = User::factory()->create(['subscription_plan' => SubscriptionPlan::Free]);

        $this->actingAs($user)
            ->post(route('tools.custo-funcionario-clt.import.preview'), [
                'import_file' => $this->uploadedFile($format),
            ])
            ->assertRedirect(route('tools.custo-funcionario-clt.index'))
            ->assertHeader('X-Prazzu-Access-Reason', 'feature.plus_required');

        $this->post(route('tools.custo-funcionario-clt.import.process'), $this->processPayload(
            $this->storeDataset($user, $format),
        ))
            ->assertRedirect(route('tools.custo-funcionario-clt.index'))
            ->assertHeader('X-Prazzu-Access-Reason', 'feature.plus_required');
    }

    #[DataProvider('isolatedFeatureFlags')]
    public function test_disabled_format_is_blocked_while_plus_can_use_the_other_active_format(
        string $disabledFormat,
        string $activeFormat,
    ): void {
        config()->set('access.commercial_mode', CommercialAccessMode::Monetized->value);
        config()->set(
            "features.tools.custo-funcionario-clt.features.{$disabledFormat}_import.enabled",
            false,
        );
        $user = User::factory()->create(['subscription_plan' => SubscriptionPlan::Plus]);

        $this->actingAs($user)
            ->post(route('tools.custo-funcionario-clt.import.preview'), [
                'import_file' => $this->uploadedFile($disabledFormat),
            ])
            ->assertServiceUnavailable();

        $this->post(route('tools.custo-funcionario-clt.import.process'), $this->processPayload(
            $this->storeDataset($user, $disabledFormat),
        ))->assertServiceUnavailable();

        $preview = $this->post(route('tools.custo-funcionario-clt.import.preview'), [
            'import_file' => $this->uploadedFile($activeFormat),
        ])->assertOk();

        preg_match('/name="import_token" value="([^"]+)"/', (string) $preview->getContent(), $matches);
        self::assertNotEmpty($matches[1] ?? null);

        $this->post(
            route('tools.custo-funcionario-clt.import.process'),
            $this->processPayload((string) $matches[1]),
        )
            ->assertOk()
            ->assertSee('1 linha(s) pronta(s) para cálculo.');
    }

    /** @return array<string, array{string}> */
    public static function importFormats(): array
    {
        return [
            'CSV usa csv_import' => ['csv'],
            'XLSX usa xlsx_import' => ['xlsx'],
        ];
    }

    /** @return array<string, array{string, string}> */
    public static function isolatedFeatureFlags(): array
    {
        return [
            'csv_import desativado' => ['csv', 'xlsx'],
            'xlsx_import desativado' => ['xlsx', 'csv'],
        ];
    }

    private function uploadedFile(string $format): UploadedFile
    {
        if ($format === 'csv') {
            return UploadedFile::fake()->createWithContent(
                'funcionarios.csv',
                "Nome;Salario\nAna Lima;5000,00\n",
            );
        }

        $spreadsheet = new Spreadsheet;
        $spreadsheet->getActiveSheet()->fromArray([
            ['Nome', 'Salario'],
            ['Ana Lima', '5000,00'],
        ]);
        $path = tempnam(sys_get_temp_dir(), 'prazzu-xlsx-test-');
        self::assertNotFalse($path);
        (new Xlsx($spreadsheet))->save($path);
        $content = file_get_contents($path) ?: '';
        $spreadsheet->disconnectWorksheets();
        @unlink($path);

        return UploadedFile::fake()->createWithContent('funcionarios.xlsx', $content);
    }

    private function storeDataset(User $user, string $format): string
    {
        return app(ImportDatasetStore::class)->put(
            new TabularDataset(
                headers: ['Nome', 'Salario'],
                rows: [['Nome' => 'Ana Lima', 'Salario' => '5000,00']],
                originalName: "funcionarios.{$format}",
                format: $format,
            ),
            'employee-cost:user:'.$user->getAuthIdentifier(),
        );
    }

    /** @return array<string, string> */
    private function processPayload(string $token): array
    {
        return [
            'import_token' => $token,
            'name_column' => 'Nome',
            'salary_column' => 'Salario',
        ];
    }
}
