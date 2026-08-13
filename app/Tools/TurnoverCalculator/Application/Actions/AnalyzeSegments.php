<?php

declare(strict_types=1);

namespace App\Tools\TurnoverCalculator\Application\Actions;

use Illuminate\Validation\ValidationException;

final readonly class AnalyzeSegments
{
    public const FEATURE = 'segmented_analysis';

    public function __construct(private CalculateTool $calculate) {}

    /**
     * @return list<array{segment:string, admissions:int, terminations:int, average_headcount:int, result:mixed}>
     */
    public function execute(string $rows): array
    {
        $lines = preg_split('/\R+/', trim($rows), -1, PREG_SPLIT_NO_EMPTY) ?: [];
        if (count($lines) < 2 || count($lines) > 12) {
            throw ValidationException::withMessages([
                'segments' => 'Informe de 2 a 12 períodos ou segmentos, um por linha.',
            ]);
        }

        $analysis = [];
        foreach ($lines as $index => $line) {
            $parts = array_map('trim', explode('|', $line));
            if (count($parts) !== 4 || $parts[0] === '' || ! ctype_digit($parts[1]) || ! ctype_digit($parts[2]) || ! ctype_digit($parts[3]) || (int) $parts[3] < 1) {
                throw ValidationException::withMessages([
                    'segments' => 'Linha '.($index + 1).' inválida. Use: nome|admissões|desligamentos|quadro_médio.',
                ]);
            }

            $data = [
                'admissions' => (int) $parts[1],
                'terminations' => (int) $parts[2],
                'average_headcount' => (int) $parts[3],
            ];

            if ($data['admissions'] > 10_000_000 || $data['terminations'] > 10_000_000 || $data['average_headcount'] > 10_000_000) {
                throw ValidationException::withMessages(['segments' => 'Os valores de cada linha devem respeitar o limite de 10.000.000.']);
            }

            $analysis[] = [
                'segment' => $parts[0],
                ...$data,
                'result' => $this->calculate->execute($data),
            ];
        }

        return $analysis;
    }
}
