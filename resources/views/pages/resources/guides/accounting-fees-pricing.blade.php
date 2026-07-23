@extends('layouts.app')

@section('title', $item['seo']['title'].' — Prazzu Tools')
@section('meta_description', $item['seo']['description'])
@section('canonical_url', route('resources.item', ['resource' => $item['type'], 'slug' => $item['slug']]))
@section('og_type', $item['type'] === 'guias' ? 'article' : 'website')
@section('og_title', $item['seo']['title'])
@section('og_description', $item['seo']['description'])

@push('head')
    <script type="application/ld+json">
        {!! json_encode([
            '@context' => 'https://schema.org',
            '@type' => $item['seo']['schema_type'],
            'name' => $item['seo']['title'],
            'headline' => $item['seo']['title'],
            'description' => $item['seo']['description'],
            'url' => route('resources.item', ['resource' => $item['type'], 'slug' => $item['slug']]),
            'dateModified' => \Carbon\Carbon::createFromFormat('d/m/Y', $item['reviewed_at'])->toDateString(),
            'isPartOf' => ['@type' => 'WebSite', 'name' => config('app.name', 'Prazzu Tools'), 'url' => route('home')],
            'about' => $item['category'],
        ], JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES) !!}
    </script>
@endpush


@section('content')
<div class="prazzu-page prazzu-resource-article">
    <nav class="prazzu-breadcrumb mb-3" aria-label="Navegação estrutural"><ol>
        <li><a href="{{ route('resources.index') }}">Recursos</a></li><li aria-hidden="true">/</li>
        <li><a href="{{ route('resources.show', 'guias') }}">Guias</a></li><li aria-hidden="true">/</li>
        <li aria-current="page">Precificação de honorários</li>
    </ol></nav>

    <header class="prazzu-resource-article__hero">
        <div>
            <span class="prazzu-eyebrow">{{ $item['category'] }}</span>
            <h1>Como precificar honorários contábeis com método</h1>
            <p>Um roteiro para transformar escopo, volume, complexidade e responsabilidade técnica em uma proposta sustentável — sem copiar preço de concorrente e sem depender apenas do faturamento do cliente.</p>
            <div class="prazzu-resource-article__meta">
                <span><i class="bi bi-clock" aria-hidden="true"></i>{{ $item['reading_time'] }}</span>
                <span><i class="bi bi-calendar-check" aria-hidden="true"></i>Revisado em {{ $item['reviewed_at'] }}</span>
                <span><i class="bi bi-shield-check" aria-hidden="true"></i>Método orientativo</span>
            </div>
        </div>
        <span class="prazzu-resource-article__hero-icon"><i class="bi bi-calculator" aria-hidden="true"></i></span>
    </header>

    <div class="prazzu-resource-article__layout">
        <aside class="prazzu-resource-article__aside">
            <nav aria-label="Neste guia">
                <strong>Neste guia</strong>
                <a href="#principio">1. O princípio central</a>
                <a href="#levantamento">2. Levantamento</a>
                <a href="#metodo">3. Método em 7 etapas</a>
                <a href="#exemplo">4. Exemplo prático</a>
                <a href="#erros">5. Erros comuns</a>
                <a href="#reajuste">6. Revisão e reajuste</a>
                <a href="#checklist">7. Checklist final</a>
                <a href="#faq">8. Perguntas frequentes</a>
            </nav>
        </aside>

        <article class="prazzu-resource-article__content">
            <section class="prazzu-resource-callout prazzu-resource-callout--primary">
                <i class="bi bi-compass" aria-hidden="true"></i>
                <div><strong>Objetivo do guia</strong><p>Chegar a um valor defensável e sustentável. A calculadora fornece referências; a decisão final precisa considerar o contrato, a realidade operacional e a estratégia do escritório.</p></div>
            </section>

            <section id="principio">
                <span class="prazzu-resource-step">01</span>
                <h2>Preço não é uma porcentagem do faturamento</h2>
                <p>O faturamento ajuda a dimensionar a empresa, mas não revela sozinho o trabalho contábil. Duas empresas com a mesma receita podem exigir esforços completamente diferentes: uma pode emitir poucas notas e ter operação simples; outra pode ter dezenas de empregados, alto volume financeiro, filiais e obrigações específicas.</p>
                <p>Uma precificação profissional combina cinco dimensões:</p>
                <div class="prazzu-resource-grid">
                    <div><strong>Escopo</strong><span>O que será entregue e o que ficará fora.</span></div>
                    <div><strong>Volume</strong><span>Documentos, transações, pessoas e eventos processados.</span></div>
                    <div><strong>Complexidade</strong><span>Regime, segmento, integrações e exceções.</span></div>
                    <div><strong>Risco</strong><span>Responsabilidade, prazo, qualidade dos dados e exposição técnica.</span></div>
                    <div><strong>Capacidade</strong><span>Tempo, equipe, tecnologia e margem necessária para atender bem.</span></div>
                </div>
            </section>

            <section id="levantamento">
                <span class="prazzu-resource-step">02</span>
                <h2>Levante o cenário antes de falar em preço</h2>
                <p>O orçamento começa com diagnóstico. Evite dar valor definitivo em uma conversa sem dados mínimos. Registre ao menos:</p>
                <div class="prazzu-resource-checklist">
                    @foreach ([
                        'Regime tributário atual e possibilidade de mudança.',
                        'Segmento, atividades, filiais e particularidades operacionais.',
                        'Faturamento mensal e sazonalidade.',
                        'Quantidade de sócios, empregados, autônomos e eventos trabalhistas.',
                        'Volume médio de notas emitidas, recebidas e documentos fiscais.',
                        'Quantidade de contas, transações bancárias e meios de pagamento.',
                        'Serviços incluídos: contábil, fiscal, folha, societário, consultoria ou BPO.',
                        'Qualidade, organização e prazo de entrega das informações pelo cliente.',
                        'Demandas extraordinárias esperadas e nível de atendimento solicitado.'
                    ] as $check)
                        <div><i class="bi bi-check2-circle" aria-hidden="true"></i><span>{{ $check }}</span></div>
                    @endforeach
                </div>
                <div class="prazzu-resource-callout">
                    <i class="bi bi-exclamation-triangle" aria-hidden="true"></i>
                    <div><strong>Não confunda informação declarada com escopo contratado</strong><p>O levantamento identifica a situação. A proposta e o contrato devem transformar essa situação em entregas, limites, responsabilidades e critérios de cobrança adicional.</p></div>
                </div>
            </section>

            <section id="metodo">
                <span class="prazzu-resource-step">03</span>
                <h2>Método em 7 etapas</h2>
                <ol class="prazzu-resource-method">
                    <li><div><strong>Defina o escopo recorrente</strong><p>Liste entregas mensais e periódicas. Separe claramente serviços extraordinários, regularizações, alterações societárias e consultorias avulsas.</p></div></li>
                    <li><div><strong>Estabeleça uma base mínima</strong><p>A base deve cobrir a estrutura necessária para manter o cliente ativo, mesmo em meses de baixo movimento: atendimento, sistemas, revisão, responsabilidade e rotinas obrigatórias.</p></div></li>
                    <li><div><strong>Adicione o efeito do volume</strong><p>Funcionários, notas e transações aumentam processamento e conferência. Defina franquias e faixas para que o crescimento do cliente não seja absorvido silenciosamente.</p></div></li>
                    <li><div><strong>Aplique a complexidade</strong><p>Regime, segmento, controles especiais e baixa qualidade de dados exigem mais análise. O acréscimo deve refletir esforço real, não apenas percepção.</p></div></li>
                    <li><div><strong>Considere responsabilidade e risco</strong><p>Prazos apertados, informações incompletas, operações sensíveis e exposição técnica precisam aparecer no preço ou nas condições de atendimento.</p></div></li>
                    <li><div><strong>Proteja a margem operacional</strong><p>Preço que cobre apenas horas aparentes não sustenta revisão, treinamento, tecnologia, retrabalho e períodos de pico. Inclua margem compatível com o padrão de serviço prometido.</p></div></li>
                    <li><div><strong>Defina faixa e condições</strong><p>Trabalhe com mínimo, valor recomendado e referência superior. A negociação pode alterar o formato, nunca apagar escopo e capacidade.</p></div></li>
                </ol>
            </section>

            <section id="exemplo">
                <span class="prazzu-resource-step">04</span>
                <h2>Exemplo prático de raciocínio</h2>
                <p>Considere uma empresa prestadora de serviços no Simples Nacional, com faturamento mensal de R$ 100 mil, cinco empregados, dois sócios, cerca de 120 documentos fiscais e 250 transações bancárias por mês.</p>
                <div class="prazzu-resource-table-wrap">
                    <table class="table align-middle mb-0">
                        <thead><tr><th>Fator</th><th>Leitura correta</th><th>Decisão de escopo</th></tr></thead>
                        <tbody>
                            <tr><td>Regime</td><td>Rotina fiscal recorrente, mas não necessariamente simples.</td><td>Confirmar anexos, retenções e obrigações aplicáveis.</td></tr>
                            <tr><td>Equipe</td><td>Cinco vínculos aumentam eventos e responsabilidade trabalhista.</td><td>Descrever admissões, férias e desligamentos incluídos.</td></tr>
                            <tr><td>Documentos</td><td>Volume acima de uma operação básica.</td><td>Definir franquia mensal e excedentes.</td></tr>
                            <tr><td>Financeiro</td><td>250 transações demandam conciliação ou organização adicional.</td><td>Distinguir escrituração contábil de BPO financeiro.</td></tr>
                        </tbody>
                    </table>
                </div>
                <p>O valor não deve sair de uma regra isolada. Primeiro dimensione cada fator, depois use a calculadora para obter uma faixa coerente e, por fim, ajuste o contrato às entregas efetivas.</p>
                <a class="btn btn-primary prazzu-resource-action" href="{{ route($item['tool']['route']) }}"><i class="bi bi-calculator me-1" aria-hidden="true"></i>Abrir Calculadora de Honorários</a>
            </section>

            <section id="erros">
                <span class="prazzu-resource-step">05</span>
                <h2>Erros que destroem margem e relacionamento</h2>
                <div class="prazzu-resource-mistakes">
                    <div><strong>Copiar o concorrente</strong><p>Você não conhece a estrutura, o escopo, a qualidade ou a estratégia de margem dele.</p></div>
                    <div><strong>Cobrar só pelo faturamento</strong><p>Receita não representa volume documental, pessoas, risco ou complexidade.</p></div>
                    <div><strong>Incluir tudo no mensal</strong><p>Sem limites, serviços extraordinários viram obrigação recorrente sem remuneração.</p></div>
                    <div><strong>Ignorar a qualidade dos dados</strong><p>Desorganização gera retrabalho. Corrigir continuamente a operação do cliente precisa ter regra própria.</p></div>
                    <div><strong>Negociar apenas desconto</strong><p>Quando o preço cai, o escopo, frequência ou nível de serviço também deve ser revisto.</p></div>
                    <div><strong>Esperar anos para revisar</strong><p>O reajuste monetário não corrige crescimento de volume ou mudança de complexidade.</p></div>
                </div>
            </section>

            <section id="reajuste">
                <span class="prazzu-resource-step">06</span>
                <h2>Reajuste anual não substitui revisão de escopo</h2>
                <p>São movimentos diferentes:</p>
                <ul>
                    <li><strong>Reajuste:</strong> atualiza monetariamente o preço conforme a regra contratada.</li>
                    <li><strong>Revisão de escopo:</strong> recalcula o valor porque a operação mudou.</li>
                </ul>
                <p>Crie gatilhos objetivos de revisão: mudança de regime, aumento relevante de funcionários ou documentos, nova filial, inclusão de serviços, atrasos recorrentes no envio de dados ou aumento do nível de atendimento.</p>
            </section>

            <section id="checklist">
                <span class="prazzu-resource-step">07</span>
                <h2>Checklist antes de enviar a proposta</h2>
                <div class="prazzu-resource-checklist prazzu-resource-checklist--boxed">
                    @foreach ([
                        'O escopo está escrito em linguagem clara?',
                        'Há limites de volume e regra para excedentes?',
                        'Serviços extraordinários estão separados?',
                        'Responsabilidades do cliente foram registradas?',
                        'O valor cobre execução, revisão, tecnologia, risco e margem?',
                        'Existe regra de reajuste e gatilhos de revisão?',
                        'A proposta deixa claro prazo, vencimento e canais de atendimento?',
                        'O contrato corresponde ao que foi vendido?'
                    ] as $check)
                        <div><i class="bi bi-square" aria-hidden="true"></i><span>{{ $check }}</span></div>
                    @endforeach
                </div>
            </section>

            <section id="faq">
                <span class="prazzu-resource-step">08</span>
                <h2>Perguntas frequentes</h2>
                <div class="accordion prazzu-resource-faq" id="accounting-fees-faq">
                    @foreach ([
                        ['Posso usar uma tabela fixa por regime?', 'Pode ser um ponto de partida, desde que volume, complexidade, serviços e risco sejam ajustados. Tabela fixa não deve substituir diagnóstico.'],
                        ['Devo cobrar por hora?', 'Horas ajudam a entender custo e capacidade, mas o preço final pode combinar base recorrente, faixas de volume e serviços avulsos.'],
                        ['Como agir quando o cliente pede desconto?', 'Negocie contrapartidas objetivas: reduzir escopo, frequência, prioridade, canais ou franquias. Evite manter a mesma entrega por um preço insustentável.'],
                        ['Quando revisar o valor?', 'Além do reajuste periódico, revise sempre que houver mudança relevante de regime, volume, equipe, serviços, risco ou qualidade das informações.'],
                        ['A calculadora define o preço final?', 'Não. Ela organiza fatores e oferece referências. A decisão final depende do diagnóstico, da proposta, do contrato e da estratégia do escritório.']
                    ] as $index => [$question, $answer])
                        <div class="accordion-item">
                            <h3 class="accordion-header"><button class="accordion-button {{ $index > 0 ? 'collapsed' : '' }}" type="button" data-bs-toggle="collapse" data-bs-target="#fees-faq-{{ $index }}" aria-expanded="{{ $index === 0 ? 'true' : 'false' }}">{{ $question }}</button></h3>
                            <div id="fees-faq-{{ $index }}" class="accordion-collapse collapse {{ $index === 0 ? 'show' : '' }}" data-bs-parent="#accounting-fees-faq"><div class="accordion-body">{{ $answer }}</div></div>
                        </div>
                    @endforeach
                </div>
            </section>

            <section class="prazzu-resource-cta">
                <div><span class="prazzu-eyebrow">Próximo passo</span><h2>Transforme o diagnóstico em uma faixa de honorários</h2><p>Preencha o cenário mensal, confira a composição do valor e use o resultado como base para sua proposta.</p></div>
                <a class="btn btn-light" href="{{ route($item['tool']['route']) }}">Calcular honorários <i class="bi bi-arrow-right ms-1" aria-hidden="true"></i></a>
            </section>

            <x-resources.journey :item="$item" :related-items="$relatedItems" />

            <p class="prazzu-resource-disclaimer"><strong>Nota:</strong> este guia oferece um método gerencial e orientativo. Cada escritório deve validar custos, capacidade, contrato, responsabilidades profissionais e condições comerciais próprias.</p>
        </article>
    </div>
</div>
@endsection
