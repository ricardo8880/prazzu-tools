<?php

declare(strict_types=1);

namespace App\Core\Analytics\Domain\Catalog;

use App\Core\Analytics\Domain\Enums\AnalyticsEventName;
use App\Core\Analytics\Domain\Services\AnalyticsEventNameResolver;

final class AnalyticsEventCatalog
{
    /** @var array<string, array{label:string,category:string,description:string,business_meaning:string}> */
    private const DEFINITIONS = [
        'page.viewed' => ['label' => 'Página visualizada', 'category' => 'Navegação', 'description' => 'Uma página da plataforma foi carregada.', 'business_meaning' => 'Mede alcance e navegação geral.'],
        'blog.post.viewed' => ['label' => 'Artigo visualizado', 'category' => 'Blog', 'description' => 'Um artigo do blog foi aberto.', 'business_meaning' => 'Mede alcance do conteúdo.'],
        'blog.reading.started' => ['label' => 'Leitura iniciada', 'category' => 'Blog', 'description' => 'O visitante demonstrou início real de leitura.', 'business_meaning' => 'Indica interesse além da abertura da página.'],
        'blog.reading.completed' => ['label' => 'Leitura concluída', 'category' => 'Blog', 'description' => 'O visitante chegou ao critério de conclusão da leitura.', 'business_meaning' => 'Indica consumo relevante do conteúdo.'],
        'blog.reading.abandoned' => ['label' => 'Leitura abandonada', 'category' => 'Blog', 'description' => 'A leitura foi encerrada antes do critério de conclusão.', 'business_meaning' => 'Ajuda a localizar conteúdos com baixa retenção.'],
        'blog.shared' => ['label' => 'Artigo compartilhado', 'category' => 'Blog', 'description' => 'Uma ação de compartilhamento foi utilizada.', 'business_meaning' => 'Sinaliza conteúdo com potencial de recomendação.'],
        'blog.downloaded' => ['label' => 'Conteúdo baixado', 'category' => 'Blog', 'description' => 'Um material relacionado ao artigo foi baixado.', 'business_meaning' => 'Indica interesse aprofundado no conteúdo.'],
        'blog.commented' => ['label' => 'Comentário realizado', 'category' => 'Blog', 'description' => 'O visitante enviou um comentário.', 'business_meaning' => 'Mede participação ativa no conteúdo.'],
        'blog.tool.clicked' => ['label' => 'Clique do blog para ferramenta', 'category' => 'Blog', 'description' => 'O visitante saiu de um artigo em direção a uma ferramenta.', 'business_meaning' => 'Mede a capacidade do conteúdo de gerar uso do produto.'],
        'blog.scroll.measured' => ['label' => 'Profundidade de rolagem registrada', 'category' => 'Blog', 'description' => 'Foi registrada a profundidade alcançada no artigo.', 'business_meaning' => 'Ajuda a entender até onde o conteúdo é consumido.'],
        'blog.time.spent' => ['label' => 'Tempo de leitura registrado', 'category' => 'Blog', 'description' => 'Foi registrado o tempo ativo no artigo.', 'business_meaning' => 'Ajuda a avaliar atenção e profundidade da leitura.'],
        'tool.opened' => ['label' => 'Ferramenta aberta', 'category' => 'Ferramentas', 'description' => 'A página principal de uma ferramenta foi aberta.', 'business_meaning' => 'Mede descoberta e entrada na ferramenta.'],
        'tool.viewed' => ['label' => 'Ferramenta visualizada', 'category' => 'Ferramentas', 'description' => 'A visualização útil da ferramenta foi confirmada.', 'business_meaning' => 'Distingue carregamento técnico de uma visualização válida.'],
        'tool.calculation.started' => ['label' => 'Cálculo iniciado', 'category' => 'Ferramentas', 'description' => 'O visitante iniciou a ação principal da ferramenta.', 'business_meaning' => 'Mede intenção real de resolver o problema.'],
        'tool.calculation.completed' => ['label' => 'Cálculo concluído', 'category' => 'Ferramentas', 'description' => 'A ferramenta entregou com sucesso seu resultado principal.', 'business_meaning' => 'É o principal indicador de valor entregue pelo produto.'],
        'tool.time.spent' => ['label' => 'Tempo de uso registrado', 'category' => 'Ferramentas', 'description' => 'Foi registrado o tempo ativo de uso da ferramenta.', 'business_meaning' => 'Ajuda a identificar fluidez ou possível dificuldade de uso.'],
        'tool.history.viewed' => ['label' => 'Histórico visualizado', 'category' => 'Ferramentas', 'description' => 'O usuário abriu o histórico de resultados.', 'business_meaning' => 'Indica retorno e uso recorrente.'],
        'tool.plus.used' => ['label' => 'Recurso Plus utilizado', 'category' => 'Ferramentas', 'description' => 'Um recurso classificado como Plus foi utilizado.', 'business_meaning' => 'Mede demanda por produtividade e recursos avançados.'],
        'tool.result.exported' => ['label' => 'Resultado exportado', 'category' => 'Ferramentas', 'description' => 'Um resultado foi baixado, impresso ou exportado.', 'business_meaning' => 'Indica que o resultado teve utilidade além da tela.'],
        'account.created' => ['label' => 'Conta criada', 'category' => 'Conta e Plus', 'description' => 'Uma nova conta foi criada.', 'business_meaning' => 'Mede adesão à persistência e continuidade, não o valor principal da ferramenta.'],
        'subscription.started' => ['label' => 'Assinatura iniciada', 'category' => 'Conta e Plus', 'description' => 'O fluxo de assinatura Plus foi iniciado.', 'business_meaning' => 'Mede intenção comercial.'],
        'subscription.created' => ['label' => 'Assinatura criada', 'category' => 'Conta e Plus', 'description' => 'A assinatura Plus foi efetivamente criada.', 'business_meaning' => 'Mede conversão comercial concluída.'],
        'business-document-validator.batch.processed' => ['label' => 'Lote de documentos processado', 'category' => 'Validador de documentos', 'description' => 'Um lote de documentos foi validado.', 'business_meaning' => 'Mede uso da capacidade de processamento em volume.'],
        'business-document-validator.batch.exported' => ['label' => 'Lote de documentos exportado', 'category' => 'Validador de documentos', 'description' => 'O resultado de um lote foi exportado.', 'business_meaning' => 'Indica aproveitamento operacional do processamento em volume.'],
    ];

    public function __construct(private readonly AnalyticsEventNameResolver $resolver = new AnalyticsEventNameResolver) {}

    /** @return array{key:string,label:string,category:string,description:string,business_meaning:string,technical_name:string,known:bool} */
    public function describe(string|AnalyticsEventName $event): array
    {
        $technicalName = $event instanceof AnalyticsEventName ? $event->value : $event;
        $canonical = $this->resolver->canonical($technicalName);
        $definition = self::DEFINITIONS[$canonical] ?? null;

        return [
            'key' => $canonical,
            'label' => $definition['label'] ?? $technicalName,
            'category' => $definition['category'] ?? 'Outros eventos',
            'description' => $definition['description'] ?? 'Evento sem descrição cadastrada no catálogo semântico.',
            'business_meaning' => $definition['business_meaning'] ?? 'Analise o contexto e os metadados antes de interpretar este evento.',
            'technical_name' => $technicalName,
            'known' => $definition !== null,
        ];
    }

    /** @param iterable<string> $events
     * @return array<string, list<array{value:string,label:string,technical_name:string,description:string}>>
     */
    public function groupedOptions(iterable $events): array
    {
        $groups = [];
        foreach ($events as $event) {
            $definition = $this->describe($event);
            $groups[$definition['category']][] = [
                'value' => $event,
                'label' => $definition['label'],
                'technical_name' => $event,
                'description' => $definition['description'],
            ];
        }
        ksort($groups);

        return $groups;
    }

    /** @return array<string, array{label:string,category:string,description:string,business_meaning:string}> */
    public function definitions(): array
    {
        return self::DEFINITIONS;
    }
}
