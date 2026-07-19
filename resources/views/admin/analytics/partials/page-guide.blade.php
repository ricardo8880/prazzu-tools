@php
    $guides = [
        'dashboard' => ['Visão geral da saúde da plataforma.', 'Use para acompanhar volume, engajamento e conversões e decidir qual área merece investigação.'],
        'acquisition' => ['Mostra como os visitantes chegaram à plataforma.', 'Use para comparar canais e descobrir quais origens trazem pessoas que realmente usam as ferramentas.'],
        'audience' => ['Resume visitantes, recorrência, dispositivos e localização.', 'Use para orientar experiência mobile, acessibilidade e prioridades regionais.'],
        'tools' => ['Compara uso e entrega de valor das ferramentas.', 'Use para identificar ferramentas fortes e pontos de abandono entre início e conclusão.'],
        'funnels' => ['Mostra a passagem entre etapas de uma jornada.', 'Use para localizar a etapa com maior perda e investigar o motivo antes de alterar o produto.'],
        'insights' => ['Reúne mudanças e anomalias detectadas automaticamente.', 'Use como ponto de partida; confirme sempre a evidência antes de tratar uma hipótese como causa.'],
        'realtime' => ['Mostra eventos recentes enquanto acontecem.', 'Use para validar campanhas, lançamentos, compartilhamentos e instrumentação.'],
        'seo' => ['Combina auditoria técnica e desempenho orgânico do conteúdo.', 'Use para priorizar artigos com oportunidade de tráfego ou problemas de indexação.'],
        'reports' => ['Permite investigar e exportar eventos com filtros.', 'O relatório atual contém dados brutos para auditoria; o relatório estratégico preparado para IA será acrescentado no próximo lote.'],
    ];
    [$guidePurpose, $guideDecision] = $guides[$page] ?? ['Área analítica da plataforma.', 'Use os filtros e comparações para apoiar decisões baseadas em evidências.'];
@endphp
<div class="alert alert-light border shadow-sm mb-4" role="note">
    <div class="d-flex gap-3">
        <i class="bi bi-info-circle text-primary fs-5"></i>
        <div>
            <div class="fw-semibold mb-1">Como interpretar esta aba</div>
            <div class="small text-body-secondary">{{ $guidePurpose }} {{ $guideDecision }}</div>
        </div>
    </div>
</div>
