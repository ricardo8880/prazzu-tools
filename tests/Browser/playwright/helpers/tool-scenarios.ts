import { expect, type Download, type Locator, type Page, type Response } from '@playwright/test';
import { readFileSync } from 'node:fs';
import { resolve } from 'node:path';
import type { DownloadExpectation } from './download-validator';

export type ScenarioStep = {
    action: 'fill' | 'select' | 'check' | 'uncheck' | 'click' | 'submit' | 'auto_fill_form' | 'invalidate_required';
    test_id?: string;
    scope_test_id?: string;
    value?: string;
};

export type ScenarioExpectation = {
    type: 'visible' | 'hidden' | 'text' | 'url' | 'field_value' | 'form_invalid';
    test_id?: string;
    value?: string;
    contains?: string;
};

export type ToolScenario = {
    id: string;
    title: string;
    kind: 'valid' | 'invalid' | 'boundary';
    tool_slug: string;
    access_profile: string;
    tags: string[];
    steps: ScenarioStep[];
    expectations: ScenarioExpectation[];
    downloads: DownloadExpectation[];
};

type ScenarioManifest = {
    schema_version: string;
    scenario_count: number;
    tool_count: number;
    coverage: { valid_tools: number; invalid_tools: number };
    scenarios: ToolScenario[];
};

type InvalidControl = {
    name: string;
    type: string;
    value: string;
    message: string;
};

type SubmitIdentity = {
    label: string;
    testId: string | null;
    method: string;
    action: string;
};

export type ActionAuditEntry = {
    kind: 'main-submit' | 'result-action';
    label: string;
    test_id: string | null;
    method: string;
    action: string;
    status: number | null;
    before_url: string;
    after_url: string;
    outcome: string;
};

export type ScenarioExecutionAudit = {
    tool_slug: string;
    scenario_id: string;
    steps: string[];
    filled_fields: string[];
    main_action: ActionAuditEntry | null;
};

const RESULT_EVIDENCE_SELECTOR = [
    '[data-testid="tool-result"]',
    '[data-analytics-result]',
    '[data-testid="contract-editor"]',
    '[data-testid="contract-preview"]',
    '[data-testid="download-actions"]',
].join(', ');

export function loadToolScenarios(): ScenarioManifest {
    const path = resolve(process.cwd(), 'storage/app/e2e/runtime/tool-scenarios.json');
    const manifest = JSON.parse(readFileSync(path, 'utf8')) as ScenarioManifest;
    if (manifest.schema_version !== '1.2.0') throw new Error(`Versão desconhecida de cenários E2E: ${manifest.schema_version}`);
    if (manifest.scenario_count !== manifest.scenarios.length) throw new Error('Contagem de cenários E2E divergente.');
    if (manifest.tool_count !== 32 || manifest.coverage.valid_tools !== 32 || manifest.coverage.invalid_tools !== 32) {
        throw new Error('O manifesto não possui cobertura mínima válida e inválida para as 32 ferramentas.');
    }
    return manifest;
}

function deterministicValue(name: string, type: string, min?: string | null, max?: string | null, inputMode?: string | null, placeholder?: string | null): string {
    const key = name.toLowerCase();
    const exact: Record<string, string> = {
        admission_date: '2024-01-01', termination_date: '2025-06-30', contract_end_date: '2025-12-31',
        acquisition_start_date: '2024-01-01', vacation_start_date: '2025-02-01', start_date: '2025-01-15',
        competence: '2026-07', reference_date: '2026-01-15', origin_uf: 'SP', destination_uf: 'RJ',
        monthly_salary: '5000', base_salary: '5000', base_cost: '100', monthly_revenue: '50000', payroll: '50000',
        revenue_last_twelve_months: '600000', payroll_last_twelve_months: '180000', monthly_operating_costs: '15000',
        monthly_deductible_expenses: '5000', accounting_profit: '50000', gross_pro_labore: '5000', intended_distribution: '10000',
        principal: '1000', amount: '1000', monthly_hours: '220', working_days: '22', rest_days: '8', due_day: '10', duration_months: '12',
        ownership_percentage: '100', internal_rate: '18', interstate_rate: '12', fcp_rate: '2',
        first_party_document: '52998224725', second_party_document: '52998224725',
        first_party_state: 'SP', second_party_state: 'RJ', jurisdiction_state: 'SP',
    };
    const decimalText = inputMode === 'decimal' || /\d+[.,]\d+/.test(placeholder ?? '');
    const formatDecimal = (value: string): string => decimalText && type !== 'number'
        ? Number(value).toLocaleString('pt-BR', { minimumFractionDigits: 2, maximumFractionDigits: 2 })
        : value;

    if (exact[key]) return formatDecimal(exact[key]);
    if (type === 'email' || key.includes('email')) return 'e2e@example.test';
    if (type === 'date' || key.includes('date') || key.includes('data_')) return '2025-01-15';
    if (type === 'month') return '2025-01';
    if (type === 'time') return '08:00';
    if (type === 'tel' || key.includes('phone') || key.includes('telefone')) return '11999999999';
    if (key.includes('cnpj')) return '11222333000181';
    if (key.includes('cpf')) return '52998224725';
    if (key.includes('cep')) return '01001000';
    if (key.includes('percent') || key.includes('aliquot') || key.includes('taxa') || key.includes('rate')) return decimalText ? '10,00' : '10';
    if (key.includes('hour') || key.includes('hora')) return '8';
    if (key.includes('day') || key.includes('dia')) return '30';
    if (decimalText || key.includes('salario') || key.includes('salary') || key.includes('receita') || key.includes('revenue') || key.includes('valor') || key.includes('amount') || key.includes('sales') || key.includes('goal') || key.includes('cost') || key.includes('custo') || key.includes('payroll')) {
        return ['number', 'range'].includes(type) ? '5000' : '5.000,00';
    }
    if (type === 'number' || type === 'range') {
        const lower = min !== null && min !== undefined && min !== '' ? Number(min) : NaN;
        const upper = max !== null && max !== undefined && max !== '' ? Number(max) : NaN;
        if (Number.isFinite(lower) && Number.isFinite(upper)) return String(Math.min(upper, Math.max(lower, lower === 0 ? 1 : lower)));
        if (Number.isFinite(lower)) return String(lower === 0 ? 1 : lower);
        if (Number.isFinite(upper)) return String(Math.min(100, upper));
        return '100';
    }
    return 'Teste E2E';
}

async function interactiveContainer(scope: Locator): Promise<Locator> {
    await expect(scope, 'O painel da ferramenta precisa estar visível.').toBeVisible();
    const isForm = await scope.evaluate(element => element instanceof HTMLFormElement);
    if (isForm) return scope;
    const form = scope.locator('form').first();
    return await form.count() > 0 ? form : scope;
}

async function autoFillForm(scope: Locator): Promise<string[]> {
    const processedRadioGroups = new Set<string>();
    const filled = new Set<string>();

    // Mais de uma passagem é intencional: selects/radios podem revelar novos campos.
    for (let pass = 0; pass < 4; pass += 1) {
        const container = await interactiveContainer(scope);
        const controls = container.locator('input:not([type="hidden"]):not([type="submit"]):not([type="button"]), textarea, select');
        const count = await controls.count();
        let changed = false;

        for (let index = 0; index < count; index += 1) {
            const control = controls.nth(index);
            if (!await control.isVisible() || !await control.isEnabled()) continue;
            const tag = await control.evaluate(element => element.tagName.toLowerCase());
            const type = (await control.getAttribute('type') ?? '').toLowerCase();
            const name = await control.getAttribute('name') ?? '';
            const min = await control.getAttribute('min');
            const max = await control.getAttribute('max');
            const inputMode = await control.getAttribute('inputmode');
            const placeholder = await control.getAttribute('placeholder');

            if (type === 'file') {
                if ((await control.inputValue()) !== '') continue;
                const accept = (await control.getAttribute('accept') ?? '').toLowerCase();
                const isXml = accept.includes('xml') || name.toLowerCase().includes('xml');
                await control.setInputFiles({
                    name: isXml ? 'documento-e2e.xml' : 'arquivo-e2e.txt',
                    mimeType: isXml ? 'application/xml' : 'text/plain',
                    buffer: Buffer.from(isXml ? '<?xml version="1.0" encoding="UTF-8"?><documento><valor>100</valor></documento>' : 'arquivo de teste e2e'),
                });
                filled.add(`${name || '(arquivo)'}=[arquivo e2e]`);
                changed = true;
                continue;
            }
            if (tag === 'select') {
                if ((await control.inputValue()) !== '') continue;
                const values = await control.locator('option:not([disabled])').evaluateAll(options => options
                    .map(option => (option as HTMLOptionElement).value)
                    .filter(value => value !== ''));
                if (values.length > 0) {
                    await control.selectOption(values[0], { timeout: 3_000 });
                    filled.add(`${name || '(select)'}=${values[0]}`);
                    changed = true;
                }
                continue;
            }
            if (type === 'radio') {
                if (name !== '' && !processedRadioGroups.has(name)) {
                    processedRadioGroups.add(name);
                    const checked = container.locator(`input[type="radio"][name="${name}"]:checked`);
                    if (await checked.count() === 0) {
                        const first = container.locator(`input[type="radio"][name="${name}"]`).filter({ visible: true }).first();
                        if (await first.count() > 0) {
                            await first.check();
                            filled.add(`${name || '(radio)'}=${await first.getAttribute('value') ?? 'checked'}`);
                            changed = true;
                        }
                    }
                }
                continue;
            }
            if (type === 'checkbox') {
                if (!await control.isChecked()) {
                    await control.check();
                    filled.add(`${name || '(checkbox)'}=checked`);
                    changed = true;
                }
                continue;
            }
            if (!(await control.isEditable())) continue;
            if ((await control.inputValue()).trim() === '') {
                const generated = deterministicValue(name, type, min, max, inputMode, placeholder);
                await control.fill(generated);
                filled.add(`${name || '(campo)'}=${type === 'password' ? '[oculto]' : generated}`);
                changed = true;
            }
        }

        if (!changed) break;
        await scope.page().waitForTimeout(50);
    }

    return Array.from(filled);
}

async function invalidateRequired(scope: Locator): Promise<void> {
    await expect.poll(async () => {
        if (scope.page().isClosed()) return false;
        return await scope.count() > 0 && await scope.isVisible().catch(() => false);
    }, { timeout: 10_000 }).toBe(true);

    const container = await interactiveContainer(scope);
    const required = container.locator('input[required]:not([type="hidden"]), textarea[required], select[required]').filter({ visible: true }).first();
    if (await required.count() === 0) {
        const fallback = container.locator('input:not([type="hidden"]):not([type="submit"]):not([type="button"]), textarea, select').filter({ visible: true }).first();
        if (await fallback.count() === 0) throw new Error('O cenário inválido exige ao menos um controle editável no contrato visual.');
        await fallback.evaluate((element: HTMLInputElement | HTMLSelectElement | HTMLTextAreaElement) => element.setCustomValidity('Campo obrigatório para o cenário E2E.'));
        return;
    }

    const tag = await required.evaluate(element => element.tagName.toLowerCase());
    const type = (await required.getAttribute('type') ?? '').toLowerCase();
    if (type === 'file') await required.setInputFiles([]);
    else if (type === 'checkbox' || type === 'radio') await required.uncheck();
    else if (tag === 'select') {
        await required.evaluate((element: HTMLSelectElement) => {
            element.selectedIndex = -1;
            element.dispatchEvent(new Event('input', { bubbles: true }));
            element.dispatchEvent(new Event('change', { bubbles: true }));
        });
    } else await required.fill('');
}

async function resolveInteractiveScope(page: Page, testId: string): Promise<Locator> {
    const candidates = page.getByTestId(testId).filter({ visible: true });
    const directForm = candidates.locator('xpath=self::form').first();
    if (await directForm.count() > 0) return directForm;
    const containingForm = candidates.filter({ has: page.locator('form') }).last();
    if (await containingForm.count() > 0) return containingForm;
    if (await candidates.count() > 0) return candidates.last();
    return page.locator('main').first();
}

async function invalidControls(container: Locator): Promise<InvalidControl[]> {
    return container.evaluate(element => {
        const root = element instanceof HTMLFormElement ? element : element.querySelector('form') ?? element;
        const controls = Array.from(root.querySelectorAll<HTMLInputElement | HTMLSelectElement | HTMLTextAreaElement>('input, select, textarea'));
        return controls.filter(control => !control.checkValidity()).map(control => ({
            name: control.name || control.id || control.tagName.toLowerCase(),
            type: control instanceof HTMLInputElement ? control.type : control.tagName.toLowerCase(),
            value: control instanceof HTMLInputElement && control.type === 'password' ? '[oculto]' : control.value,
            message: control.validationMessage,
        }));
    });
}

async function containerIsInvalid(container: Locator): Promise<boolean> {
    return (await invalidControls(container)).length > 0;
}

async function findSubmit(container: Locator, page: Page): Promise<Locator> {
    let submit = container.locator([
        'button[type="submit"]', 'input[type="submit"]', '[data-testid="calculate-button"]', '[data-testid="submit-button"]',
    ].join(', ')).filter({ visible: true }).first();

    if (await submit.count() === 0) {
        const actionButtons = container.locator('button').filter({ visible: true });
        for (let index = 0; index < await actionButtons.count(); index += 1) {
            const candidate = actionButtons.nth(index);
            const label = (await candidate.innerText()).trim();
            if (/calcular|simular|gerar|validar|converter|comparar|emitir|atualizar|processar|continuar/i.test(label)) {
                submit = candidate;
                break;
            }
        }
    }
    if (await submit.count() === 0) submit = page.locator('button[type="submit"], input[type="submit"]').filter({ visible: true }).first();
    return submit;
}

async function submitIdentity(submit: Locator, form: Locator, page: Page): Promise<SubmitIdentity> {
    const label = ((await submit.innerText().catch(() => '')) || await submit.getAttribute('value') || '').trim();
    const testId = await submit.getAttribute('data-testid');
    const formAction = await form.getAttribute('action');
    const buttonAction = await submit.getAttribute('formaction');
    const action = new URL(buttonAction || formAction || page.url(), page.url()).toString();
    const method = ((await form.getAttribute('method')) || 'GET').toUpperCase();
    return { label: label || '(sem texto)', testId, method, action };
}

async function validationMessages(page: Page): Promise<string[]> {
    return page.locator([
        '.is-invalid', '.invalid-feedback:visible', '[data-testid="validation-summary"]:visible',
    ].join(', ')).evaluateAll(elements => Array.from(new Set(elements
        .map(element => (element.textContent ?? '').replace(/\s+/g, ' ').trim())
        .filter(Boolean))).slice(0, 8));
}

async function submittedValues(form: Locator): Promise<string[]> {
    return form.evaluate(element => {
        const controls = Array.from(element.querySelectorAll<HTMLInputElement | HTMLSelectElement | HTMLTextAreaElement>('input, select, textarea'));
        return controls
            .filter(control => control.name && !['hidden', 'submit', 'button', 'reset'].includes(control instanceof HTMLInputElement ? control.type : ''))
            .filter(control => !(control instanceof HTMLInputElement && ['checkbox', 'radio'].includes(control.type) && !control.checked))
            .map(control => {
                const type = control instanceof HTMLInputElement ? control.type : control.tagName.toLowerCase();
                const raw = control instanceof HTMLInputElement && type === 'file'
                    ? Array.from(control.files ?? []).map(file => file.name).join(',')
                    : control.value;
                const value = type === 'password' ? '[oculto]' : raw.replace(/\s+/g, ' ').slice(0, 160);
                return `${control.name}=${value}`;
            })
            .slice(0, 80);
    });
}

function actionFailure(scenario: ToolScenario, identity: SubmitIdentity, beforeUrl: string, afterUrl: string, response: Response | null, problem: string, details: string[] = []): Error {
    const responseSummary = response ? `${response.status()} ${response.request().method()} ${response.url()}` : 'nenhuma resposta HTTP do formulário foi capturada';
    return new Error([
        `[E2E_ACTION_FAILURE] Ferramenta [${scenario.tool_slug}] · cenário [${scenario.id}]`,
        `Botão: "${identity.label}"${identity.testId ? ` (data-testid=${identity.testId})` : ''}`,
        `Formulário: ${identity.method} ${identity.action}`,
        `Resposta: ${responseSummary}`,
        `URL antes: ${beforeUrl}`,
        `URL depois: ${afterUrl}`,
        `Problema: ${problem}`,
        ...details,
    ].join('\n'));
}

async function submitForm(scope: Locator, scenario: ToolScenario): Promise<ActionAuditEntry | null> {
    const container = await interactiveContainer(scope);
    const page = container.page();
    const invalidScenario = scenario.kind === 'invalid';

    if (invalidScenario && await containerIsInvalid(container)) return null;

    const submit = await findSubmit(container, page);
    if (await submit.count() === 0) throw new Error(`[E2E_ACTION_FAILURE] Ferramenta [${scenario.tool_slug}] não expõe botão principal de envio no formulário.`);

    let form = container.locator('xpath=self::form').first();
    if (await form.count() === 0) form = container.locator('form').first();
    if (await form.count() === 0) form = submit.locator('xpath=ancestor::form[1]');
    if (await form.count() === 0) throw new Error(`[E2E_ACTION_FAILURE] Botão principal de [${scenario.tool_slug}] não pertence a um formulário identificável.`);

    const identity = await submitIdentity(submit, form, page);
    const sentValues = await submittedValues(form);
    const beforeUrl = page.url();
    const expectedVisibleIds = scenario.expectations
        .filter(expectation => expectation.type === 'visible' && expectation.test_id)
        .map(expectation => expectation.test_id!);
    const beforeVisibleEvidence = await page.locator(RESULT_EVIDENCE_SELECTOR).filter({ visible: true }).count();
    const clientInvalid = await invalidControls(form);
    if (!invalidScenario && clientInvalid.length > 0) {
        throw actionFailure(scenario, identity, beforeUrl, beforeUrl, null, 'o E2E tentou clicar com campos inválidos no próprio navegador.', [
            `Dados preparados: ${sentValues.join(' | ')}`,
            `Campos inválidos: ${clientInvalid.map(field => `${field.name} (${field.type})="${field.value}": ${field.message}`).join(' | ')}`,
        ]);
    }

    const actionUrl = new URL(identity.action);
    const responsePromise = page.waitForResponse(response => {
        const request = response.request();
        const responseUrl = new URL(response.url());
        return request.method().toUpperCase() === identity.method
            && responseUrl.pathname === actionUrl.pathname;
    }, { timeout: 15_000 }).then(response => ({ type: 'response' as const, response })).catch(() => null);
    const navigationPromise = page.waitForNavigation({ waitUntil: 'domcontentloaded', timeout: 15_000 })
        .then(response => ({ type: 'navigation' as const, response })).catch(() => null);

    await submit.click({ noWaitAfter: true });
    const submitOutcome = await Promise.race([responsePromise, navigationPromise]);
    const response: Response | null = submitOutcome?.response ?? null;

    if (invalidScenario) return null;

    const expectedResultWaits = expectedVisibleIds.map(testId =>
        page.getByTestId(testId).first().waitFor({ state: 'visible', timeout: 8_000 }).catch(() => undefined),
    );
    await Promise.race([
        ...expectedResultWaits,
        page.locator(RESULT_EVIDENCE_SELECTOR).filter({ visible: true }).first().waitFor({ state: 'visible', timeout: 8_000 }),
        page.locator('.invalid-feedback:visible, [data-testid="validation-summary"]:visible').first().waitFor({ state: 'visible', timeout: 8_000 }),
    ]).catch(() => undefined);

    const afterUrl = page.url();
    if (response && response.status() >= 400) {
        throw actionFailure(scenario, identity, beforeUrl, afterUrl, response, `o endpoint respondeu HTTP ${response.status()}.`, [
            `Dados enviados: ${sentValues.join(' | ')}`,
        ]);
    }

    const messages = await validationMessages(page);
    if (messages.length > 0) {
        throw actionFailure(scenario, identity, beforeUrl, afterUrl, response, 'o envio considerado válido retornou erros de validação.', [
            `Dados enviados: ${sentValues.join(' | ')}`,
            `Validação: ${messages.join(' | ')}`,
        ]);
    }

    const missingExpectedIds: string[] = [];
    for (const testId of expectedVisibleIds) {
        if (!await page.getByTestId(testId).first().isVisible().catch(() => false)) missingExpectedIds.push(testId);
    }
    if (missingExpectedIds.length > 0) {
        throw actionFailure(scenario, identity, beforeUrl, afterUrl, response, 'o botão principal respondeu, mas o resultado funcional esperado não apareceu.', [
            `Dados enviados: ${sentValues.join(' | ')}`,
            `Resultados/botões ausentes: ${missingExpectedIds.join(', ')}`,
            `Resultados/botões exigidos: ${expectedVisibleIds.join(', ')}`,
            'Um reload da mesma página sem esses elementos é FALHA, mesmo que a resposta HTTP seja 200.',
        ]);
    }

    const afterVisibleEvidence = await page.locator(RESULT_EVIDENCE_SELECTOR).filter({ visible: true }).count();
    if (expectedVisibleIds.length === 0 && afterVisibleEvidence <= beforeVisibleEvidence) {
        throw actionFailure(scenario, identity, beforeUrl, afterUrl, response, 'o clique terminou sem produzir evidência visível de resultado. Um simples reload da página NÃO é considerado sucesso.', [
            `Dados enviados: ${sentValues.join(' | ')}`,
            `Evidências procuradas: ${RESULT_EVIDENCE_SELECTOR}`,
        ]);
    }

    return {
        kind: 'main-submit',
        label: identity.label,
        test_id: identity.testId,
        method: identity.method,
        action: identity.action,
        status: response?.status() ?? null,
        before_url: beforeUrl,
        after_url: afterUrl,
        outcome: expectedVisibleIds.length > 0 ? `resultado confirmado (${expectedVisibleIds.length} contrato(s) visual(is))` : `resultado visível confirmado (${afterVisibleEvidence - beforeVisibleEvidence} nova(s) evidência(s))`,
    };
}

async function assertExpectation(page: Page, scenario: ToolScenario, expectation: ScenarioExpectation): Promise<void> {
    if (expectation.type === 'url') {
        await expect(page, `[${scenario.tool_slug}:${scenario.id}] URL final incorreta.`).toHaveURL(new RegExp(expectation.contains ?? ''));
        return;
    }
    const testId = expectation.test_id ?? '';
    const target = page.getByTestId(testId).first();
    const context = `[${scenario.tool_slug}:${scenario.id}] expectativa ${expectation.type} em data-testid="${testId}"`;
    if (expectation.type === 'visible') await expect(target, `${context}: o resultado/controle esperado não apareceu após a ação.`).toBeVisible({ timeout: 10_000 });
    if (expectation.type === 'hidden') await expect(target, `${context}: o elemento deveria desaparecer após a ação.`).toBeHidden();
    if (expectation.type === 'text') await expect(target, `${context}: conteúdo inesperado.`).toContainText(expectation.value ?? '');
    if (expectation.type === 'field_value') await expect(target, `${context}: valor inesperado.`).toHaveValue(expectation.value ?? '');
    if (expectation.type === 'form_invalid') {
        const scope = expectation.test_id ? await resolveInteractiveScope(page, expectation.test_id) : page.locator('main').first();
        await expect.poll(() => containerIsInvalid(scope), { message: `${context}: o formulário deveria permanecer inválido.`, timeout: 3_000 }).toBe(true);
    }
}

export async function executeScenario(page: Page, scenario: ToolScenario): Promise<ScenarioExecutionAudit> {
    const audit: ScenarioExecutionAudit = {
        tool_slug: scenario.tool_slug,
        scenario_id: scenario.id,
        steps: [],
        filled_fields: [],
        main_action: null,
    };

    for (const step of scenario.steps) {
        const target = step.test_id ? page.getByTestId(step.test_id) : null;
        const scope = await resolveInteractiveScope(page, step.scope_test_id ?? 'tool-form-panel');
        const targetDescription = step.test_id ? ` data-testid=${step.test_id}` : step.scope_test_id ? ` scope=${step.scope_test_id}` : '';
        audit.steps.push(`${step.action}${targetDescription}${step.value !== undefined ? ` value=${step.value}` : ''}`);

        switch (step.action) {
            case 'fill': await target!.fill(step.value ?? ''); break;
            case 'select': await target!.selectOption(step.value ?? ''); break;
            case 'check': await target!.check(); break;
            case 'uncheck': await target!.uncheck(); break;
            case 'click': {
                const href = await target!.getAttribute('href');
                if (href) {
                    const destination = new URL(href, page.url()).toString();
                    await Promise.all([
                        page.waitForURL(url => url.toString() === destination, { timeout: 10_000 }),
                        target!.click({ noWaitAfter: true }),
                    ]);
                    await page.waitForLoadState('domcontentloaded');
                } else await target!.click();
                break;
            }
            case 'auto_fill_form': audit.filled_fields.push(...await autoFillForm(scope)); break;
            case 'invalidate_required': await invalidateRequired(scope); break;
            case 'submit': audit.main_action = await submitForm(scope, scenario); break;
        }
    }

    for (const expectation of scenario.expectations) await assertExpectation(page, scenario, expectation);
    return audit;
}

export async function auditVisibleResultActions(page: Page, scenario: ToolScenario): Promise<ActionAuditEntry[]> {
    const resultRoots = page.locator([
        '[data-testid="contract-editor"]',
        '[data-testid="download-actions"]',
        '[data-testid="tool-result"] form',
        '[data-analytics-result] form',
    ].join(', '));
    const buttons = resultRoots.locator('button[type="submit"], input[type="submit"], button:not([type])').filter({ visible: true });
    const identities: Array<{ testId: string | null; label: string }> = [];

    for (let index = 0; index < await buttons.count(); index += 1) {
        const button = buttons.nth(index);
        if (!await button.isEnabled()) continue;
        identities.push({
            testId: await button.getAttribute('data-testid'),
            label: ((await button.innerText().catch(() => '')) || await button.getAttribute('value') || '').replace(/\s+/g, ' ').trim(),
        });
    }

    const audited: ActionAuditEntry[] = [];
    for (const candidate of identities) {
        const button = candidate.testId
            ? page.getByTestId(candidate.testId).filter({ visible: true }).first()
            : resultRoots.locator('button[type="submit"], input[type="submit"], button:not([type])').filter({ visible: true }).filter({ hasText: candidate.label }).first();
        if (await button.count() === 0 || !await button.isEnabled()) continue;

        const form = button.locator('xpath=ancestor::form[1]');
        if (await form.count() === 0) continue;
        const identity = await submitIdentity(button, form, page);
        const beforeUrl = page.url();
        const sentValues = await submittedValues(form);
        const actionUrl = new URL(identity.action);
        const isExport = (await button.getAttribute('data-analytics-action')) === 'export'
            || /^download-/i.test(identity.testId ?? '')
            || /export|baixar|download|pdf|excel|xlsx|csv|json|word|docx/i.test(`${identity.label} ${identity.action}`);

        type ButtonOutcome =
            | { type: 'response'; response: Response }
            | { type: 'navigation'; response: Response | null }
            | { type: 'download'; download: Download }
            | null;
        const responsePromise: Promise<ButtonOutcome> = page.waitForResponse(response => {
            const request = response.request();
            const responseUrl = new URL(response.url());
            return request.method().toUpperCase() === identity.method && responseUrl.pathname === actionUrl.pathname;
        }, { timeout: 10_000 }).then(response => ({ type: 'response' as const, response })).catch(() => null);
        const navigationPromise: Promise<ButtonOutcome> = page.waitForNavigation({ waitUntil: 'domcontentloaded', timeout: 10_000 })
            .then(response => ({ type: 'navigation' as const, response })).catch(() => null);
        const downloadPromise: Promise<ButtonOutcome> | null = isExport
            ? page.waitForEvent('download', { timeout: 10_000 }).then(download => ({ type: 'download' as const, download })).catch(() => null)
            : null;

        await button.click({ noWaitAfter: true });
        const contenders: Promise<ButtonOutcome>[] = [responsePromise, navigationPromise];
        if (downloadPromise) contenders.push(downloadPromise);
        const firstOutcome = await Promise.race(contenders);

        let response: Response | null = null;
        let download: Download | null = null;
        if (firstOutcome?.type === 'download') download = firstOutcome.download;
        else if (firstOutcome && 'response' in firstOutcome) response = firstOutcome.response;

        if (download) {
            const failure = await download.failure();
            if (failure) {
                throw actionFailure(scenario, identity, beforeUrl, page.url(), response, `o download iniciado pelo botão falhou: ${failure}.`, [
                    `Dados enviados: ${sentValues.join(' | ')}`,
                ]);
            }
            audited.push({ kind: 'result-action', label: identity.label, test_id: identity.testId, method: identity.method, action: identity.action, status: response?.status() ?? null, before_url: beforeUrl, after_url: page.url(), outcome: `download OK: ${download.suggestedFilename()}` });
            continue;
        }

        if (!response) {
            throw actionFailure(scenario, identity, beforeUrl, page.url(), null, 'o botão do formulário não gerou download nem resposta HTTP observável.', [
                `Dados enviados: ${sentValues.join(' | ')}`,
            ]);
        }
        if (response.status() >= 400) {
            throw actionFailure(scenario, identity, beforeUrl, page.url(), response, `a ação respondeu HTTP ${response.status()}.`, [
                `Dados enviados: ${sentValues.join(' | ')}`,
            ]);
        }

        if (isExport) {
            const disposition = response.headers()['content-disposition'] ?? '';
            const contentType = response.headers()['content-type'] ?? '';
            if (!/attachment|filename=/i.test(disposition) && !/(pdf|spreadsheet|excel|csv|json|word|officedocument|octet-stream)/i.test(contentType)) {
                throw actionFailure(scenario, identity, beforeUrl, page.url(), response, 'o botão de exportação respondeu, mas não entregou um arquivo reconhecível.', [
                    `Dados enviados: ${sentValues.join(' | ')}`,
                    `Content-Type: ${contentType || '(ausente)'}`,
                    `Content-Disposition: ${disposition || '(ausente)'}`,
                ]);
            }
        } else {
            const messages = await validationMessages(page);
            if (messages.length > 0) {
                throw actionFailure(scenario, identity, beforeUrl, page.url(), response, 'o botão secundário do formulário respondeu, mas deixou erros de validação.', [
                    `Dados enviados: ${sentValues.join(' | ')}`,
                    `Validação: ${messages.join(' | ')}`,
                ]);
            }
        }

        audited.push({
            kind: 'result-action',
            label: identity.label,
            test_id: identity.testId,
            method: identity.method,
            action: identity.action,
            status: response.status(),
            before_url: beforeUrl,
            after_url: page.url(),
            outcome: isExport ? 'arquivo HTTP reconhecido' : 'ação HTTP concluída sem erro de validação',
        });
    }

    return audited;
}
