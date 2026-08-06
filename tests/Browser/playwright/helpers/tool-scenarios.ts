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

function deterministicValue(name: string, type: string): string {
    const key = name.toLowerCase();
    if (type === 'email' || key.includes('email')) return 'e2e@example.test';
    if (type === 'date' || key.includes('date') || key.includes('data_')) return '2026-01-15';
    if (type === 'month') return '2026-01';
    if (type === 'time') return '08:00';
    if (type === 'tel' || key.includes('phone') || key.includes('telefone')) return '11999999999';
    if (key.includes('cnpj')) return '11222333000181';
    if (key.includes('cpf')) return '52998224725';
    if (key.includes('cep')) return '01001000';
    if (key.includes('percent') || key.includes('aliquot') || key.includes('taxa')) return '10';
    if (key.includes('hour') || key.includes('hora')) return '8';
    if (key.includes('day') || key.includes('dia')) return '30';
    if (type === 'number' || type === 'range') return '100';
    return '1000';
}

async function formWithin(scope: Locator): Promise<Locator> {
    const form = scope.locator('form').first();
    await expect(form, 'O painel da ferramenta precisa conter um formulário.').toBeVisible();
    return form;
}

async function autoFillForm(scope: Locator): Promise<void> {
    const form = await formWithin(scope);
    const controls = form.locator('input:not([type="hidden"]):not([type="submit"]):not([type="button"]), textarea, select');
    for (let index = 0; index < await controls.count(); index += 1) {
        const control = controls.nth(index);
        if (!await control.isVisible() || !await control.isEnabled()) continue;
        const tag = await control.evaluate(element => element.tagName.toLowerCase());
        const type = (await control.getAttribute('type') ?? '').toLowerCase();
        const name = await control.getAttribute('name') ?? '';
        if (tag === 'select') {
            const values = await control.locator('option:not([disabled])').evaluateAll(options => options.map(option => (option as HTMLOptionElement).value).filter(Boolean));
            if (values.length > 0) await control.selectOption(values[0]);
            continue;
        }
        if (type === 'checkbox' || type === 'radio') {
            if (await control.getAttribute('required') !== null && !await control.isChecked()) await control.check();
            continue;
        }
        const current = await control.inputValue();
        if (current.trim() === '') await control.fill(deterministicValue(name, type));
    }
}

async function invalidateRequired(scope: Locator): Promise<void> {
    const form = await formWithin(scope);
    const required = form.locator('input[required]:not([type="hidden"]), textarea[required], select[required]').filter({ visible: true }).first();
    if (await required.count() === 0) {
        throw new Error('O cenário inválido exige ao menos um campo obrigatório no contrato visual.');
    }
    const tag = await required.evaluate(element => element.tagName.toLowerCase());
    const type = (await required.getAttribute('type') ?? '').toLowerCase();
    if (type === 'checkbox' || type === 'radio') await required.uncheck();
    else if (tag === 'select') await required.selectOption('');
    else await required.fill('');
}

async function submitForm(scope: Locator, expectsNavigation: boolean): Promise<void> {
    const form = await formWithin(scope);
    const page = form.page();
    const submit = form.locator('button[type="submit"], input[type="submit"]').first();

    await expect(submit, 'O formulário precisa expor uma ação submit.').toBeVisible();

    if (expectsNavigation) {
        // Registra a espera antes do clique. Isso cobre POST para a mesma URL e
        // redirecionamentos, sem permitir que as expectativas leiam page.url()
        // durante a troca do documento.
        await Promise.all([
            page.waitForNavigation({ waitUntil: 'domcontentloaded', timeout: 30_000 }),
            submit.click({ noWaitAfter: true }),
        ]);
        return;
    }

    // Cenários inválidos normalmente são bloqueados pela validação nativa e não
    // navegam. Neles, o clique deve terminar sem inventar uma navegação obrigatória.
    await submit.click({ noWaitAfter: true });
    await expect.poll(
        () => form.evaluate(element => (element as HTMLFormElement).checkValidity()),
        { message: 'A validação nativa do formulário precisa estabilizar.', timeout: 5_000 },
    ).toBe(false);
}

export async function executeScenario(page: Page, scenario: ToolScenario): Promise<void> {
    for (const step of scenario.steps) {
        const target = step.test_id ? page.getByTestId(step.test_id) : null;
        const scope = page.getByTestId(step.scope_test_id ?? 'tool-form-panel').first();
        switch (step.action) {
            case 'fill': await target!.fill(step.value ?? ''); break;
            case 'select': await target!.selectOption(step.value ?? ''); break;
            case 'check': await target!.check(); break;
            case 'uncheck': await target!.uncheck(); break;
            case 'click': await target!.click(); break;
            case 'auto_fill_form': await autoFillForm(scope); break;
            case 'invalidate_required': await invalidateRequired(scope); break;
            case 'submit': await submitForm(scope, scenario.kind !== 'invalid'); break;
        }
    }

    for (const expectation of scenario.expectations) {
        if (expectation.type === 'url') {
            await expect(page).toHaveURL(new RegExp(expectation.contains ?? ''));
            continue;
        }
        const target = page.getByTestId(expectation.test_id ?? '').first();
        if (expectation.type === 'visible') await expect(target).toBeVisible();
        if (expectation.type === 'hidden') await expect(target).toBeHidden();
        if (expectation.type === 'text') await expect(target).toContainText(expectation.value ?? '');
        if (expectation.type === 'field_value') await expect(target).toHaveValue(expectation.value ?? '');
        if (expectation.type === 'form_invalid') {
            const form = await formWithin(target);
            await expect.poll(() => form.evaluate(element => !(element as HTMLFormElement).checkValidity())).toBe(true);
        }
    }
}
