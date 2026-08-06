import fs from 'node:fs';
import path from 'node:path';
import type {
    FullConfig,
    FullResult,
    Reporter,
    Suite,
    TestCase,
    TestResult,
} from '@playwright/test/reporter';

const REPORT_PATH = path.resolve(
    process.cwd(),
    process.env.E2E_GPT_REPORT_PATH ?? 'txt_e2e_testes.txt',
);

function clean(value: string, maxLength = 8_000): string {
    const normalized = value
        .replace(/\u001b\[[0-9;]*m/g, '')
        .replace(/\r\n/g, '\n')
        .trim();

    return normalized.length > maxLength
        ? `${normalized.slice(0, maxLength)}\n...[conteúdo truncado]`
        : normalized;
}

function suggestion(message: string): string {
    const text = message.toLowerCase();

    if (text.includes('element(s) not found') || text.includes('to be visible') || text.includes('tobevisible')) {
        return 'Confirme se o elemento existe na view, se o data-testid esperado está correto e se ele só aparece após alguma ação assíncrona.';
    }
    if (text.includes('tohaveurl') || text.includes('waiting for navigation')) {
        return 'Revise a rota esperada e sincronize a ação que dispara a navegação antes de validar a URL.';
    }
    if (text.includes('timeout')) {
        return 'Verifique se a ação realmente termina, se há requisição pendente ou seletor incorreto. Evite apenas aumentar o timeout sem localizar a causa.';
    }
    if (text.includes('request-failed') || text.includes('net::') || text.includes('ns_binding')) {
        return 'Inspecione a URL e a origem da requisição. Recursos funcionais internos devem ser corrigidos; telemetria ou recursos externos abortados devem ser classificados como aviso.';
    }
    if (text.includes('download') || text.includes('content-disposition')) {
        return 'Confirme se o botão de exportação existe, se inicia um download e se a resposta possui nome, extensão e Content-Type corretos.';
    }
    if (text.includes('login') || text.includes('autentica') || text.includes('/entrar')) {
        return 'Confira as credenciais E2E, o redirecionamento após o login e a gravação do storageState do perfil.';
    }
    if (text.includes('strict mode violation')) {
        return 'Torne o seletor específico para que corresponda a apenas um elemento.';
    }
    if (text.includes('malformed value')) {
        return 'Gere um valor compatível com o tipo do campo, como YYYY-MM-DD para input date.';
    }
    if (text.includes('500') || text.includes('server error')) {
        return 'Consulte storage/logs/laravel.log usando o horário e a rota informados neste relatório para localizar a exceção do backend.';
    }

    return 'Abra o trace e o error-context indicados, reproduza o cenário isoladamente e corrija a primeira causa apresentada antes dos erros derivados.';
}

function relative(filePath: string): string {
    return path.relative(process.cwd(), filePath).replace(/\\/g, '/');
}

export default class GptTxtReporter implements Reporter {
    private startedAt = new Date();
    private total = 0;
    private passed = 0;
    private failed = 0;
    private skipped = 0;
    private failures: string[] = [];
    private config?: FullConfig;

    onBegin(config: FullConfig, suite: Suite): void {
        this.config = config;
        this.startedAt = new Date();
        this.total = suite.allTests().length;
        fs.mkdirSync(path.dirname(REPORT_PATH), { recursive: true });
    }

    onTestEnd(test: TestCase, result: TestResult): void {
        if (result.status === 'passed') {
            this.passed += 1;
            return;
        }
        if (result.status === 'skipped') {
            this.skipped += 1;
            return;
        }

        this.failed += 1;
        const projectName = test.parent.project()?.name ?? 'sem-projeto';
        const location = `${relative(test.location.file)}:${test.location.line}:${test.location.column}`;
        const title = test.titlePath().filter(Boolean).join(' › ');
        const errorText = result.errors
            .map((error) => clean(error.stack || error.message || String(error)))
            .filter(Boolean)
            .join('\n\n--- erro adicional ---\n\n') || 'Falha sem mensagem detalhada.';
        const attachments = result.attachments
            .filter((attachment) => attachment.path)
            .map((attachment) => `- ${attachment.name}: ${relative(attachment.path!)}`)
            .join('\n');

        this.failures.push([
            `ERRO ${this.failed}`,
            `Projeto/navegador: ${projectName}`,
            `Teste: ${title}`,
            `Arquivo do teste: ${location}`,
            `Duração: ${result.duration} ms`,
            '',
            'ERRO ENCONTRADO:',
            errorText,
            '',
            'COMO INVESTIGAR/CORRIGIR:',
            suggestion(errorText),
            '',
            'ARTEFATOS PARA DIAGNÓSTICO:',
            attachments || '- Nenhum artefato anexado.',
            '',
            'COMANDO PARA REPETIR APENAS ESTE TESTE:',
            `npx playwright test "${relative(test.location.file)}:${test.location.line}" --project="${projectName}"`,
        ].join('\n'));
    }

    onEnd(result: FullResult): void {
        const finishedAt = new Date();
        const durationSeconds = Math.round((finishedAt.getTime() - this.startedAt.getTime()) / 1000);
        const section = [
            '',
            '='.repeat(100),
            `EXECUÇÃO E2E — ${this.startedAt.toISOString()}`,
            '='.repeat(100),
            `Status geral: ${result.status}`,
            `Diretório do projeto: ${process.cwd()}`,
            `Quantidade planejada: ${this.total}`,
            `Aprovados nesta execução: ${this.passed}`,
            `Falhas nesta execução: ${this.failed}`,
            `Ignorados nesta execução: ${this.skipped}`,
            `Duração aproximada: ${durationSeconds}s`,
            `Workers: ${this.config?.workers ?? 'padrão'}`,
            '',
            this.failed === 0
                ? 'NENHUM ERRO FOI ENCONTRADO NESTA EXECUÇÃO.'
                : this.failures.join(`\n\n${'-'.repeat(100)}\n\n`),
            '',
        ].join('\n');

        fs.mkdirSync(path.dirname(REPORT_PATH), { recursive: true });
        fs.appendFileSync(REPORT_PATH, section, 'utf8');

        console.log(`\n[E2E] Relatório para análise por GPT: ${relative(REPORT_PATH)}`);
        console.log(`[E2E] Resultado: ${this.passed} aprovados, ${this.failed} falhas, ${this.skipped} ignorados.`);
    }
}
