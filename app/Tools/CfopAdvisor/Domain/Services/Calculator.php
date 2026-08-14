<?php

declare(strict_types=1);

namespace App\Tools\CfopAdvisor\Domain\Services;

use App\Core\Tax\Fiscal\CfopCatalog;
use App\Core\Tools\Calculation\Data\ToolCalculationResult;
use App\Core\Tools\Calculation\Data\ToolCalculationSummaryItem;
use App\Core\Tools\Calculation\Data\ToolCalculationWarning;
use App\Core\Tools\Calculation\Enums\ToolCalculationWarningLevel;
use App\Core\Tools\Contracts\ToolCalculationInput;
use App\Core\Tools\Contracts\ToolCalculator;
use App\Tools\CfopAdvisor\Application\Data\CalculationInput;
use InvalidArgumentException;

final readonly class Calculator implements ToolCalculator
{
    public function __construct(private CfopCatalog $catalog) {}

    public function calculate(ToolCalculationInput $input): ToolCalculationResult
    {
        if (! $input instanceof CalculationInput) {
            throw new InvalidArgumentException('Entrada inválida.');
        }

        $code = str_replace('.', '', trim($input->cfop));
        if (! $this->catalog->isStructurallyValid($code)) {
            throw new InvalidArgumentException('CFOP fora dos grupos válidos.');
        }

        $entry = $this->catalog->find($code);
        $group = $this->catalog->groupLabel($code) ?? 'Grupo não identificado';
        $direction = in_array($code[0], ['1', '2', '3'], true) ? 'Entrada' : 'Saída';
        $warnings = [];
        if ($entry === null) {
            $warnings[] = new ToolCalculationWarning('catalog', 'A estrutura do CFOP é válida, mas este código não está no recorte de descrições rápidas desta versão. Confirme a descrição exata no Anexo II vigente do Convênio SINIEF s/nº/1970 no CONFAZ.', ToolCalculationWarningLevel::Info);
        }

        return new ToolCalculationResult('consultor-validador-cfop', '1.0.0', [
            new ToolCalculationSummaryItem('cfop', 'CFOP', $code[0].'.'.substr($code, 1)),
            new ToolCalculationSummaryItem('direction', 'Direção', $direction),
            new ToolCalculationSummaryItem('scope', 'Abrangência', $group),
        ], [
            'code' => $code,
            'description' => $entry['description'] ?? null,
            'scope' => $entry['scope'] ?? $group,
            'official_source' => 'CONFAZ — Convênio SINIEF s/nº, de 15/12/1970, Anexo II, com alterações vigentes',
        ], $warnings);
    }
}
