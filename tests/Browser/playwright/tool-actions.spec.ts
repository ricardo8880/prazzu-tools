import { expect, test } from '@playwright/test';
import { attachBrowserDiagnostics, blockingBrowserDiagnostics, collectBrowserDiagnostics } from './helpers/diagnostics';
import { applyE2ECorrelation, attachCorrelatedServerLogs } from './helpers/e2e-correlation';
import { auditVisibleResultActions, executeScenario, loadToolScenarios } from './helpers/tool-scenarios';

const manifest = loadToolScenarios();
const requestedToolSlug = process.env.E2E_TOOL_SLUG?.trim() || null;
const validScenarios = manifest.scenarios.filter(scenario => scenario.kind === 'valid');
const scenarios = requestedToolSlug
    ? validScenarios.filter(scenario => scenario.tool_slug === requestedToolSlug)
    : validScenarios;

function expectedResultSummary(scenario: (typeof scenarios)[number]): string {
    const visible = scenario.expectations
        .filter(expectation => expectation.type === 'visible' && expectation.test_id)
        .map(expectation => expectation.test_id);
    const viewport = scenario.expectations
        .filter(expectation => expectation.type === 'in_viewport' && expectation.test_id)
        .map(expectation => `${expectation.test_id} (na tela do usuário)`);
    const expected = [...visible, ...viewport];
    return expected.length > 0 ? expected.join(', ') : 'contrato semântico padrão de resultado';
}

test.describe('Auditoria operacional rápida de formulários e botões', () => {
    test(requestedToolSlug
        ? `carregou a ferramenta solicitada: ${requestedToolSlug}`
        : `carregou ${manifest.tool_count} fluxos válidos para teste rápido`, async () => {
        if (requestedToolSlug) {
            const availableSlugs = validScenarios.map(scenario => scenario.tool_slug).sort();
            expect(
                scenarios,
                `[E2E TOOL] Ferramenta \"${requestedToolSlug}\" não encontrada no manifesto.\n` +
                `Slugs disponíveis: ${availableSlugs.join(', ')}`,
            ).toHaveLength(1);
            return;
        }

        expect(scenarios).toHaveLength(manifest.tool_count);
    });

    for (const scenario of scenarios) {
        test(`${scenario.tool_slug} · preencher, clicar e validar resultado/botões`, async ({ page }, testInfo) => {
            const diagnostics = collectBrowserDiagnostics(page);
            const correlation = await applyE2ECorrelation(page, `${scenario.tool_slug}:${scenario.id}:actions`);
            const declaredSteps = scenario.steps.map((step, index) => `${index + 1}. ${step.action}${step.test_id ? ` data-testid=${step.test_id}` : ''}${step.scope_test_id ? ` scope=${step.scope_test_id}` : ''}`);

            console.log(`\n[ACTIONS] TESTANDO: ${scenario.tool_slug}`);
            console.log(`[ACTIONS] Página: /ferramentas/${scenario.tool_slug}`);
            console.log(`[ACTIONS] Fluxo: ${declaredSteps.join(' -> ')}`);
            console.log(`[ACTIONS] Resultado esperado: ${expectedResultSummary(scenario)}`);

            try {
                const response = await page.goto(`/ferramentas/${scenario.tool_slug}`, { waitUntil: 'domcontentloaded' });
                expect(response?.status(), `[${scenario.tool_slug}] a página da ferramenta precisa abrir antes do teste do botão.`).toBeLessThan(400);

                const execution = await executeScenario(page, scenario);
                console.log(`[ACTIONS] Campos populados: ${execution.filled_fields.length > 0 ? execution.filled_fields.join(' | ') : '(nenhum autopreenchimento)'}`);
                if (execution.main_action) {
                    console.log(`[ACTIONS] Botão principal: "${execution.main_action.label}" -> ${execution.main_action.method} ${execution.main_action.action}`);
                    console.log(`[ACTIONS] Resposta principal: ${execution.main_action.status ?? 'sem status capturado'}; ${execution.main_action.outcome}`);
                }

                const auditedResultActions = await auditVisibleResultActions(page, scenario);
                for (const action of auditedResultActions) {
                    console.log(`[ACTIONS] Botão adicional OK: "${action.label}" -> ${action.status ?? 'download'}; ${action.outcome}`);
                }

                await testInfo.attach('action-audit-summary', {
                    contentType: 'application/json',
                    body: Buffer.from(JSON.stringify({
                        tool: scenario.tool_slug,
                        declared_steps: declaredSteps,
                        expected_result: expectedResultSummary(scenario),
                        execution,
                        audited_result_actions: auditedResultActions,
                    }, null, 2)),
                });

                const blocking = blockingBrowserDiagnostics(diagnostics);
                expect(blocking, `Falhas técnicas durante a ação principal de [${scenario.tool_slug}].`).toEqual([]);
                console.log(`[ACTIONS] OK: ${scenario.tool_slug} — formulário produziu resultado real e ${auditedResultActions.length} botão(ões) do resultado foram auditados.`);
            } catch (error) {
                const message = error instanceof Error ? error.message : String(error);
                console.error(`\n[ACTIONS] FALHOU: ${scenario.tool_slug}`);
                console.error(`[ACTIONS] O que foi executado: ${declaredSteps.join(' -> ')}`);
                console.error(`[ACTIONS] O que deveria aparecer: ${expectedResultSummary(scenario)}`);
                console.error(`[ACTIONS] Erro:\n${message}`);
                throw error;
            } finally {
                await attachBrowserDiagnostics(testInfo, diagnostics);
                await attachCorrelatedServerLogs(testInfo, correlation);
            }
        });
    }
});
