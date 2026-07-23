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
        <li><a href="{{ route('resources.index') }}">Recursos</a></li>
        <li aria-hidden="true">/</li>
        <li><a href="{{ route('resources.show', ['resource' => 'modelos']) }}">Modelos</a></li>
        <li aria-hidden="true">/</li>
        <li aria-current="page">Levantamento de honorários</li>
    </ol></nav>

    <header class="prazzu-resource-article__hero">
        <div>
            <span class="prazzu-eyebrow">Modelo profissional</span>
            <h1>Levantamento para precificação de honorários contábeis</h1>
            <p>Organize os dados do cliente, o volume operacional, o escopo e os fatores de complexidade antes de definir uma faixa de honorários.</p>
            <div class="prazzu-resource-article__meta">
                <span><i class="bi bi-file-earmark-spreadsheet" aria-hidden="true"></i>Planilha XLSX</span>
                <span><i class="bi bi-check2-circle" aria-hidden="true"></i>Revisado em {{ $item['reviewed_at'] }}</span>
                <span><i class="bi bi-shield-check" aria-hidden="true"></i>Sem macros</span>
            </div>
        </div>
        <span class="prazzu-resource-article__hero-icon"><i class="bi bi-clipboard-data" aria-hidden="true"></i></span>
    </header>

    <div class="prazzu-resource-article__layout">
        <aside class="prazzu-resource-article__aside">
            <nav aria-label="Nesta página">
                <strong>Neste modelo</strong>
                <a href="#objetivo">Objetivo</a>
                <a href="#abas">Abas incluídas</a>
                <a href="#uso">Como usar</a>
                <a href="#limites">Limites do material</a>
                <a href="#entrega">O que você terá ao final</a>
                <a href="#download">Baixar modelo</a>
            </nav>
        </aside>

        <article class="prazzu-resource-article__content">
            <section id="objetivo">
                <div class="prazzu-resource-callout prazzu-resource-callout--primary">
                    <i class="bi bi-info-circle" aria-hidden="true"></i>
                    <div><strong>O modelo organiza o diagnóstico; ele não calcula o preço.</strong><p>Depois do preenchimento, transfira as informações relevantes para a Calculadora de Honorários e valide a proposta conforme a realidade do escritório.</p></div>
                </div>
                <span class="prazzu-resource-step">01</span>
                <h2>Para que serve</h2>
                <p>O arquivo foi criado para reduzir esquecimentos na etapa de levantamento. Ele ajuda a registrar fatos que costumam alterar esforço, risco e escopo: regime tributário, número de pessoas, documentos, transações, qualidade dos dados, atendimento esperado e serviços extraordinários.</p>
                <p>O material pode ser usado em uma reunião inicial, em uma revisão de carteira ou antes de renegociar um contrato existente.</p>
            </section>

            <section id="abas">
                <span class="prazzu-resource-step">02</span>
                <h2>O que está incluído</h2>
                <div class="prazzu-resource-grid">
                    <div><strong>Como usar</strong><span>Sequência recomendada e aviso de responsabilidade.</span></div>
                    <div><strong>Levantamento</strong><span>Dados cadastrais, volumes, equipe, atendimento e qualidade das informações.</span></div>
                    <div><strong>Escopo</strong><span>Serviços incluídos, avulsos, fora do escopo, frequência e limites.</span></div>
                    <div><strong>Complexidade</strong><span>Evidências de esforço, risco, retrabalho, sazonalidade e integrações.</span></div>
                    <div><strong>Consolidação</strong><span>Premissas finais e checklist antes da proposta e do cálculo.</span></div>
                </div>
            </section>

            <section id="uso">
                <span class="prazzu-resource-step">03</span>
                <h2>Como usar sem transformar a planilha em cadastro</h2>
                <ol class="prazzu-resource-method">
                    <li><div><strong>Crie uma cópia por análise.</strong><p>Use o arquivo como instrumento pontual de levantamento, não como banco permanente de clientes.</p></div></li>
                    <li><div><strong>Registre somente o necessário.</strong><p>Evite dados pessoais ou documentos que não sejam indispensáveis à precificação.</p></div></li>
                    <li><div><strong>Confirme volumes e exceções.</strong><p>Diferencie média mensal, picos sazonais, retrabalho e atividades extraordinárias.</p></div></li>
                    <li><div><strong>Separe recorrência de avulsos.</strong><p>O mensal não deve absorver automaticamente alterações, regularizações ou projetos.</p></div></li>
                    <li><div><strong>Consolide as premissas.</strong><p>Use a última aba para preparar o cenário que será levado à calculadora e à proposta.</p></div></li>
                </ol>
            </section>

            <section id="limites">
                <span class="prazzu-resource-step">04</span>
                <h2>O que este material não faz</h2>
                <div class="prazzu-resource-checklist prazzu-resource-checklist--boxed">
                    <div><i class="bi bi-x-circle" aria-hidden="true"></i><span>Não define valor mínimo, tabela de mercado ou preço obrigatório.</span></div>
                    <div><i class="bi bi-x-circle" aria-hidden="true"></i><span>Não substitui proposta comercial, contrato ou revisão profissional.</span></div>
                    <div><i class="bi bi-x-circle" aria-hidden="true"></i><span>Não funciona como CRM, cadastro de clientes ou gestão do escritório.</span></div>
                    <div><i class="bi bi-x-circle" aria-hidden="true"></i><span>Não envia dados ao Prazzu Tools e não contém macros ou automações ocultas.</span></div>
                </div>
            </section>

            <section id="entrega">
                <span class="prazzu-resource-step">05</span>
                <h2>O que você terá ao final do preenchimento</h2>
                <p>O modelo foi estruturado para transformar uma conversa inicial em premissas verificáveis. Ao concluir as cinco abas, você terá uma visão organizada do cliente antes de calcular ou apresentar qualquer valor.</p>
                <div class="prazzu-resource-outcomes">
                    <div><i class="bi bi-check2-circle" aria-hidden="true"></i><span><strong>Volumes confirmados</strong>Dados médios, picos sazonais e pontos ainda pendentes de validação.</span></div>
                    <div><i class="bi bi-check2-circle" aria-hidden="true"></i><span><strong>Escopo separado</strong>Serviços recorrentes, avulsos, limites incluídos e possíveis excedentes.</span></div>
                    <div><i class="bi bi-check2-circle" aria-hidden="true"></i><span><strong>Complexidade justificada</strong>Evidências de risco, retrabalho, atendimento, integrações e qualidade dos dados.</span></div>
                    <div><i class="bi bi-check2-circle" aria-hidden="true"></i><span><strong>Premissas para proposta</strong>Responsabilidades, gatilhos de revisão e informações prontas para a calculadora.</span></div>
                </div>
            </section>

            <section id="download" class="prazzu-resource-download">
                <div class="prazzu-resource-download__content">
                    <span class="prazzu-eyebrow">Arquivo pronto para uso</span>
                    <h2>Baixe, faça uma cópia e conduza o levantamento</h2>
                    <p>Planilha XLSX editável, sem macros e organizada em cinco abas para conduzir o diagnóstico do início à consolidação.</p>
                    <ul class="prazzu-resource-download__details" aria-label="Detalhes do arquivo">
                        <li><i class="bi bi-layers" aria-hidden="true"></i><span><strong>5 abas</strong>Como usar, Levantamento, Escopo, Complexidade e Consolidação</span></li>
                        <li><i class="bi bi-file-earmark-spreadsheet" aria-hidden="true"></i><span><strong>Formato XLSX</strong>Compatível com Excel, LibreOffice Calc e editores equivalentes</span></li>
                        <li><i class="bi bi-shield-check" aria-hidden="true"></i><span><strong>Arquivo seguro</strong>Sem macros, scripts ou envio automático de dados</span></li>
                    </ul>
                </div>
                <div class="prazzu-resource-download__action">
                    <a class="btn btn-primary prazzu-resource-action" href="{{ asset($item['download']) }}" download>
                        <i class="bi bi-download me-1" aria-hidden="true"></i>Baixar planilha XLSX
                    </a>
                    <small>Faça uma cópia antes de iniciar um novo levantamento.</small>
                </div>
            </section>

            <section class="prazzu-resource-cta">
                <div><span class="prazzu-eyebrow">Depois do levantamento</span><h2>Transforme os dados em uma referência de honorários</h2><p>Use a calculadora para organizar os fatores e comparar a composição do cenário.</p></div>
                <a class="btn btn-light text-dark" href="{{ route($item['tool']['route']) }}">Abrir calculadora <i class="bi bi-arrow-right ms-1" aria-hidden="true"></i></a>
            </section>

            <x-resources.journey :item="$item" :related-items="$relatedItems" />

            <p class="prazzu-resource-disclaimer"><strong>Nota:</strong> o preenchimento e a guarda do arquivo são responsabilidade de quem o utiliza. Evite inserir dados pessoais desnecessários e aplique as práticas de segurança adequadas à sua operação.</p>
        </article>
    </div>
</div>
@endsection
