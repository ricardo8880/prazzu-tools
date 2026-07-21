<?php

declare(strict_types=1);

namespace App\Tools\FederalPaymentGuideGenerator\Application\Actions;

final class PrepareGuideExport
{
    /**
     * @param array<string, mixed> $input
     * @param array<string, mixed> $result
     * @return array{
     *     filename:string,
     *     payload:array{input:array<string,mixed>,result:array<string,mixed>},
     *     headers:list<string>,
     *     rows:list<list<string|int>>,
     *     subtitle:string,
     *     summary:string
     * }
     */
    public function execute(array $input, array $result): array
    {
        $guide = is_array($result['guide'] ?? null) ? $result['guide'] : [];
        $dates = is_array($result['dates'] ?? null) ? $result['dates'] : [];
        $amounts = is_array($result['amounts'] ?? null) ? $result['amounts'] : [];
        $calculation = is_array($result['calculation'] ?? null) ? $result['calculation'] : [];
        $warnings = is_array($result['warnings'] ?? null) ? $result['warnings'] : [];

        $type = strtoupper((string) ($guide['type'] ?? $input['guide_type'] ?? 'guia'));
        $code = (string) ($guide['code'] ?? $input['revenue_code'] ?? 'sem-codigo');
        $paymentDate = (string) ($dates['payment_date'] ?? $input['payment_date'] ?? date('Y-m-d'));

        $rows = [
            ['Guia', $type],
            ['Código de receita', $code],
            ['Descrição', (string) ($guide['description'] ?? '')],
            ['Periodicidade', (string) ($guide['periodicity'] ?? '')],
            ['Referência oficial', (string) ($guide['official_reference'] ?? '')],
            ['Vencimento', (string) ($dates['due_date'] ?? $input['due_date'] ?? '')],
            ['Pagamento previsto', $paymentDate],
            ['Principal', (string) ($amounts['principal'] ?? '')],
            ['Multa', (string) ($amounts['penalty'] ?? '')],
            ['Juros', (string) ($amounts['interest'] ?? '')],
            ['Total estimado', (string) ($amounts['total'] ?? '')],
            ['Dias corridos de atraso', (int) ($calculation['calendar_days_late'] ?? 0)],
            ['Percentual de multa', (string) ($calculation['penalty_percent'] ?? '0').' %'],
            ['Percentual de juros', (string) ($calculation['interest_percent'] ?? '0').' %'],
        ];

        foreach ($warnings as $index => $warning) {
            $rows[] = ['Alerta '.($index + 1), (string) $warning];
        }

        return [
            'filename' => sprintf('darf-gps-%s-%s-%s', strtolower($type), $code, $paymentDate),
            'payload' => ['input' => $input, 'result' => $result],
            'headers' => ['Campo', 'Valor'],
            'rows' => $rows,
            'subtitle' => sprintf('%s %s · pagamento previsto em %s', $type, $code, $paymentDate),
            'summary' => (string) ($amounts['total'] ?? '—'),
        ];
    }
}
