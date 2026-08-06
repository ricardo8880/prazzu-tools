import { expect, test, type Locator } from '@playwright/test';
import { loadToolCatalog } from './helpers/tool-catalog';

const catalog = loadToolCatalog();
const seed = Number(process.env.E2E_EXPLORATION_SEED ?? '12012');
const maxActions = Number(process.env.E2E_EXPLORATION_ACTIONS ?? '12');

function hash(value: string): number {
    let result = seed;
    for (const character of value) result = ((result * 31) + character.charCodeAt(0)) >>> 0;
    return result;
}

async function safeAction(target: Locator, index: number): Promise<void> {
    const tag = await target.evaluate(element => element.tagName.toLowerCase());
    const type = (await target.getAttribute('type') ?? '').toLowerCase();
    if (tag === 'select') {
        const options = await target.locator('option:not([disabled])').evaluateAll(items => items.map(item => (item as HTMLOptionElement).value).filter(Boolean));
        if (options.length > 0) await target.selectOption(options[index % options.length]);
        return;
    }
    if (type === 'checkbox' || type === 'radio') {
        await target.check({ force: true });
        return;
    }
    if (['button', 'submit'].includes(type) || tag === 'button') return;
    await target.fill(String((index + 1) * 17));
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
                body: Buffer.from(JSON.stringify({ seed, maxActions, tool: tool.slug, errors }, null, 2)),
            });
            expect(errors, `A exploração de [${tool.slug}] encontrou falhas técnicas.`).toEqual([]);
        });
    }
});
