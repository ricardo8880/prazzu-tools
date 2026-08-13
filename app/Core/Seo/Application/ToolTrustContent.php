<?php

namespace App\Core\Seo\Application;

use Illuminate\Support\Str;

final class ToolTrustContent
{
    /** @param array<string, mixed> $tool
     *  @return array{precheck:string,transparency:string,continuity:string,version:string}
     */
    public function for(array $tool): array
    {
        $category = (string) ($tool['declared_category'] ?? $tool['category'] ?? 'outros');
        $essentialLabels = collect((array) ($tool['essential_features'] ?? []))
            ->pluck('name')
            ->filter(static fn (mixed $label): bool => is_string($label) && trim($label) !== '')
            ->map(static fn (string $label): string => Str::lower(Str::ascii($label)))
            ->implode(' ');

        $hasExplicitMemory = Str::contains($essentialLabels, [
            'memoria',
            'premissa',
            'fonte',
            'normativ',
            'formula',
            'metodo',
        ]);

        return [
            'precheck' => $this->precheckFor($category),
            'transparency' => $hasExplicitMemory
                ? 'A ferramenta declara memória, método, premissas ou fontes como parte do resultado Essencial. Confira esses detalhes e os avisos apresentados antes de usar o valor em uma decisão.'
                : 'Confira os dados informados, os avisos e as premissas exibidas no resultado. O Prazzu Tools não completa silenciosamente informações que dependem do seu caso concreto.',
            'continuity' => (bool) ($tool['supports_history'] ?? false)
                ? 'Você pode usar a ferramenta sem cadastro. Quando entrar na sua conta, o histórico disponível nesta ferramenta serve apenas para continuidade e não altera a qualidade do cálculo.'
                : 'Você pode usar a ferramenta sem cadastro. A conta não desbloqueia uma fórmula melhor nem corrige o resultado; ela existe apenas para recursos de continuidade quando a ferramenta os oferece.',
            'version' => (string) ($tool['version'] ?? '1.0.0'),
        ];
    }

    private function precheckFor(string $category): string
    {
        return match ($category) {
            'fiscal' => 'Confirme competência, enquadramento, incidência, bases, alíquotas e regras do ente competente quando esses dados dependerem da operação. Não trate uma estimativa parametrizada como apuração oficial automática.',
            'trabalhista' => 'Confirme vínculo, competência, verbas, bases e particularidades do trabalhador antes de fechar folha, rescisão ou outra obrigação. Situações especiais podem exigir tratamento próprio.',
            'societario' => 'Confirme contrato social, regime tributário, escrituração e premissas da empresa antes de usar o resultado em uma deliberação societária ou distribuição de valores.',
            'validadores' => 'Use a validação para identificar inconsistências técnicas. Quando a situação exigir existência, regularidade ou situação cadastral oficial, confirme também na fonte competente.',
            'documentos', 'geradores' => 'Revise nomes, valores, datas, cláusulas e demais dados antes de usar o documento formalmente. Um modelo gerado deve refletir o caso real e as exigências aplicáveis.',
            'conversores' => 'Confira a origem, o formato e a integridade dos dados convertidos antes de importar ou transmitir o arquivo em outro sistema.',
            'calculadoras' => 'Revise as entradas e as premissas do cenário. Projeções e simulações dependem diretamente dos valores informados e não devem receber suposições que não correspondam ao seu caso.',
            default => 'Revise as entradas, as premissas e os avisos do resultado antes de utilizá-lo em uma decisão profissional.',
        };
    }
}
