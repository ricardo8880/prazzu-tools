import { expect, test, type Locator } from '@playwright/test';
import { loadToolCatalog } from './helpers/tool-catalog';

const catalog = loadToolCatalog();
const environment = process.env;
const seed = Number(environment.E2E_EXPLORATION_SEED ?? '12012');
const maxActions = Number(environment.E2E_EXPLORATION_ACTIONS ?? '12');

function hash(value: string): number {
    let result = seed;
    for (const character of value) result = ((result * 31) + character.charCodeAt(0)) >>> 0;
    return result;
}

async function safeAction(target: Locator, index: number): Promise<void> {
    const tag = await target.evaluate(element => element.tagName.toLowerCase());
    const type = (await target.getAttribute('type') ?? '').toLowerCase();

    if (await target.getAttribute('readonly') !== null) return;

    if (tag === 'select') {
        const options = await target.locator('option:not([disabled])').evaluateAll(items => items
            .map(item => (item as HTMLOptionElement).value)
            .filter(Boolean));
        if (options.length > 0) await target.selectOption(options[index % options.length]);
        return;
    }

    if (type === 'checkbox' || type === 'radio') {
        await target.check({ force: true });
        return;
    }

    if (['button', 'submit', 'reset', 'file', 'image'].includes(type) || tag === 'button') return;

    const numericValue = String((index + 1) * 17);
    const valuesByType: Record<string, string> = {
        date: '2026-08-06',
        'datetime-local': '2026-08-06T12:00',
        month: '2026-08',
        week: '2026-W32',
        time: '12:00',
        email: `e2e-${index + 1}@example.test`,
        url: `https://example.test/e2e/${index + 1}`,
        tel: '11999999999',
        color: '#336699',
        password: `E2e-${index + 1}-Safe`,
        number: numericValue,
        range: numericValue,
    };

    await target.fill(valuesByType[type] ?? `e2e-${index + 1}`);
}

test.describe('Exploração controlada e não bloqueante', () => {
    for (const tool of catalog.tools) {
        test(`${tool.slug} suporta sequência exploratória reproduzível`, async ({ page }, testInfo) => {
            const errors: string[] = [];
            page.on('pageerror', error => errors.push(error.message));
            page.on('response', response => {
                if (response.status() >= 500) errors.push(`${response.status()} ${response.url()}`);
            });

            const response = await page.goto(tool.path, { waitUntil: 'domcontentloaded' });
            expect(response?.status()).toBeLessThan(500);
            const form = page.getByTestId(tool.test_ids.form).first().locator('form').first();
            await expect(form).toBeVisible();
            const controls = form.locator('input:not([type="hidden"]), textarea, select');
            const count = Math.min(await controls.count(), maxActions);
            const offset = count === 0 ? 0 : hash(tool.slug) % count;

            for (let step = 0; step < count; step += 1) {
                const target = controls.nth((offset + step) % count);
                if (await target.isVisible() && await target.isEnabled()) await safeAction(target, step);
            }

            await testInfo.attach('exploration-seed', {
                contentType: 'application/json',
                body: JSON.stringify({ seed, maxActions, tool: tool.slug, errors }, null, 2),
            });
            expect(errors, `A exploração de [${tool.slug}] encontrou falhas técnicas.`).toEqual([]);
        });
    }
});
