import { expect, type Locator, type Page } from '@playwright/test';
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

function deterministicValue(name: string, type: string, min?: string | null, max?: string | null): string {
    const key = name.toLowerCase();
    const exact: Record<string, string> = {
        admission_date: '2024-01-01', termination_date: '2025-06-30', contract_end_date: '2025-12-31',
        acquisition_start_date: '2024-01-01', vacation_start_date: '2025-02-01', start_date: '2025-01-15',
        competence: '2026-07', reference_date: '2026-01-15', origin_uf: 'SP', destination_uf: 'RJ',
        monthly_salary: '5000', base_salary: '5000', base_cost: '100', monthly_revenue: '50000',
        accounting_profit: '50000', gross_pro_labore: '5000', intended_distribution: '10000',
        monthly_hours: '220', working_days: '22', rest_days: '8', due_day: '10', duration_months: '12',
        ownership_percentage: '100', internal_rate: '18', interstate_rate: '12', fcp_rate: '2',
        first_party_document: '52998224725', second_party_document: '52998224725',
        first_party_state: 'SP', second_party_state: 'RJ', jurisdiction_state: 'SP',
    };
    if (exact[key]) {
        const value = exact[key];
        const monetary = /(salary|salario|revenue|receita|cost|custo|profit|lucro|valor|amount|base|sales|goal|reversal|pro_labore|distribution)/.test(key);
        return monetary && !['number', 'range'].includes(type) && /^\d+(?:\.\d+)?$/.test(value)
            ? Number(value).toLocaleString('pt-BR', { minimumFractionDigits: 2, maximumFractionDigits: 2 })
            : value;
    }
    if (type === 'email' || key.includes('email')) return 'e2e@example.test';
    if (type === 'date' || key.includes('date') || key.includes('data_')) return '2025-01-15';
    if (type === 'month') return '2025-01';
    if (type === 'time') return '08:00';
    if (type === 'tel' || key.includes('phone') || key.includes('telefone')) return '11999999999';
    if (key.includes('cnpj')) return '11222333000181';
    if (key.includes('cpf')) return '52998224725';
    if (key.includes('cep')) return '01001000';
    if (key.includes('percent') || key.includes('aliquot') || key.includes('taxa')) return '10';
    if (key.includes('hour') || key.includes('hora')) return '8';
    if (key.includes('day') || key.includes('dia')) return '30';
    if (key.includes('salario') || key.includes('salary') || key.includes('receita') || key.includes('revenue') || key.includes('valor') || key.includes('amount') || key.includes('sales') || key.includes('goal') || key.includes('cost') || key.includes('custo')) {
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

async function autoFillForm(scope: Locator): Promise<void> {
    const container = await interactiveContainer(scope);
    const controls = container.locator('input:not([type="hidden"]):not([type="submit"]):not([type="button"]), textarea, select');
    const count = await controls.count();
    const processedRadioGroups = new Set<string>();
    for (let index = 0; index < count; index += 1) {
        const control = controls.nth(index);
        if (!await control.isVisible() || !await control.isEnabled()) continue;
        const tag = await control.evaluate(element => element.tagName.toLowerCase());
        const type = (await control.getAttribute('type') ?? '').toLowerCase();
        const name = await control.getAttribute('name') ?? '';
        const min = await control.getAttribute('min');
        const max = await control.getAttribute('max');

        if (type === 'file') {
            const accept = (await control.getAttribute('accept') ?? '').toLowerCase();
            const isXml = accept.includes('xml') || name.toLowerCase().includes('xml');
            await control.setInputFiles({
                name: isXml ? 'documento-e2e.xml' : 'arquivo-e2e.txt',
                mimeType: isXml ? 'application/xml' : 'text/plain',
                buffer: Buffer.from(isXml ? '<?xml version="1.0" encoding="UTF-8"?><documento><valor>100</valor></documento>' : 'arquivo de teste e2e'),
            });
            continue;
        }
        if (tag === 'select') {
            const currentValue = await control.inputValue();
            if (currentValue !== '') continue;
            const values = await control.locator('option:not([disabled])').evaluateAll(options => options
                .map(option => (option as HTMLOptionElement).value)
                .filter(value => value !== ''));
            if (values.length > 0) await control.selectOption(values[0], { timeout: 3_000 });
            continue;
        }
        if (type === 'radio') {
            if (name !== '' && !processedRadioGroups.has(name)) {
                processedRadioGroups.add(name);
                const checked = container.locator(`input[type="radio"][name="${name}"]:checked`);
                if (await checked.count() === 0) {
                    const first = container.locator(`input[type="radio"][name="${name}"]`).filter({ visible: true }).first();
                    if (await first.count() > 0) await first.check();
                }
            }
            continue;
        }
        if (type === 'checkbox') {
            if (!await control.isChecked()) await control.check();
            continue;
        }
        if (!(await control.isEditable())) continue;
        const current = await control.inputValue();
        if (current.trim() === '') await control.fill(deterministicValue(name, type, min, max));
    }
}

async function invalidateRequired(scope: Locator): Promise<void> {
    // Alguns fluxos, como o Gerador de Contratos, trocam de etapa por navegação.
    // Aguarde o formulário interativo da nova página antes de procurar controles.
    await expect.poll(async () => {
        if (scope.page().isClosed()) return false;
        return await scope.count() > 0 && await scope.isVisible().catch(() => false);
    }, { timeout: 10_000 }).toBe(true);

    const container = await interactiveContainer(scope);
    const required = container.locator('input[required]:not([type="hidden"]), textarea[required], select[required]').filter({ visible: true }).first();
    if (await required.count() === 0) {
        const fallback = container.locator('input:not([type="hidden"]):not([type="submit"]):not([type="button"]), textarea, select').filter({ visible: true }).first();
        if (await fallback.count() === 0) throw new Error('O cenário inválido exige ao menos um controle editável no contrato visual.');
        await fallback.evaluate((element: HTMLInputElement | HTMLSelectElement | HTMLTextAreaElement) => {
            element.setCustomValidity('Campo obrigatório para o cenário E2E.');
        });
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

    // Algumas ferramentas reutilizam o mesmo test-id no painel introdutório e no
    // formulário real. Priorize o elemento que é um <form> ou contém um formulário.
    const directForm = candidates.locator('xpath=self::form').first();
    if (await directForm.count() > 0) return directForm;

    const containingForm = candidates.filter({ has: page.locator('form') }).last();
    if (await containingForm.count() > 0) return containingForm;

    if (await candidates.count() > 0) return candidates.last();

    return page.locator('main').first();
}

async function containerIsInvalid(container: Locator): Promise<boolean> {
    return container.evaluate(element => {
        const root = element instanceof HTMLFormElement ? element : element.querySelector('form') ?? element;
        const controls = Array.from(root.querySelectorAll<HTMLInputElement | HTMLSelectElement | HTMLTextAreaElement>('input, select, textarea'));
        return controls.some(control => !control.checkValidity());
    });
}

async function submitForm(scope: Locator, invalidScenario: boolean): Promise<void> {
    const container = await interactiveContainer(scope);
    const page = container.page();

    if (invalidScenario && await containerIsInvalid(container)) return;

    let submit = container.locator([
        'button[type="submit"]',
        'input[type="submit"]',
        '[data-testid="calculate-button"]',
        '[data-testid="submit-button"]',
    ].join(', ')).filter({ visible: true }).first();

    if (await submit.count() === 0) {
        const actionButtons = container.locator('button').filter({ visible: true });
        const count = await actionButtons.count();
        for (let index = 0; index < count; index += 1) {
            const candidate = actionButtons.nth(index);
            const label = (await candidate.innerText()).trim();
            if (/calcular|simular|gerar|validar|converter|comparar|emitir|atualizar|processar|continuar/i.test(label)) {
                submit = candidate;
                break;
            }
        }
    }

    let form = container.locator('xpath=self::form').first();
    if (await form.count() === 0) form = container.locator('form').first();

    // Formulários HTML tradicionais fazem POST e renderizam novamente a ferramenta.
    // Use o clique real no submit e aguarde a navegação em paralelo. Isso preserva
    // validação nativa, listeners de clique e o comportamento consistente no Firefox.
    if (await form.count() > 0) {
        if (await submit.count() === 0) {
            throw new Error('O formulário não expõe um controle de envio visível.');
        }

        const navigation = page.waitForNavigation({
            waitUntil: 'domcontentloaded',
            timeout: 15_000,
        }).catch(() => null);

        await submit.click({ noWaitAfter: true });
        await navigation;
    } else {
        if (await submit.count() === 0) {
            submit = page.locator('button[type="submit"], input[type="submit"]').filter({ visible: true }).first();
        }
        if (await submit.count() === 0) throw new Error('A página não expõe botão de cálculo nem formulário submetível.');
        await submit.click();
    }

    await Promise.race([
        page.getByTestId('tool-result').first().waitFor({ state: 'visible', timeout: 10_000 }),
        page.waitForLoadState('domcontentloaded', { timeout: 10_000 }),
    ]).catch(() => undefined);

    if (!invalidScenario) {
        const visibleValidationErrors = page.locator([
            '.is-invalid',
            '.invalid-feedback:visible',
            '[data-testid="validation-summary"]:visible',
        ].join(', '));

        if (await visibleValidationErrors.count() > 0) {
            const messages = await visibleValidationErrors.evaluateAll(elements => elements
                .map(element => (element.textContent ?? '').trim())
                .filter(Boolean)
                .slice(0, 5));
            throw new Error(`O envio válido retornou erros de validação: ${messages.join(' | ') || 'campos inválidos visíveis'}`);
        }
    }
}

export async function executeScenario(page: Page, scenario: ToolScenario): Promise<void> {
    for (const step of scenario.steps) {
        const target = step.test_id ? page.getByTestId(step.test_id) : null;
        const scope = await resolveInteractiveScope(page, step.scope_test_id ?? 'tool-form-panel');
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
                } else {
                    await target!.click();
                }
                break;
            }
            case 'auto_fill_form': await autoFillForm(scope); break;
            case 'invalidate_required': await invalidateRequired(scope); break;
            case 'submit': await submitForm(scope, scenario.kind === 'invalid'); break;
        }
    }

    for (const expectation of scenario.expectations) {
        if (expectation.type === 'url') {
            await expect(page).toHaveURL(new RegExp(expectation.contains ?? ''));
            continue;
        }
        const target = page.getByTestId(expectation.test_id ?? '').first();
        if (expectation.type === 'visible') await expect(target).toBeVisible({ timeout: 10_000 });
        if (expectation.type === 'hidden') await expect(target).toBeHidden();
        if (expectation.type === 'text') await expect(target).toContainText(expectation.value ?? '');
        if (expectation.type === 'field_value') await expect(target).toHaveValue(expectation.value ?? '');
        if (expectation.type === 'form_invalid') {
            const scope = expectation.test_id
                ? await resolveInteractiveScope(page, expectation.test_id)
                : page.locator('main').first();
            await expect.poll(() => containerIsInvalid(scope), { timeout: 3_000 }).toBe(true);
        }
    }
}
