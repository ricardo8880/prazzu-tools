import fs from 'node:fs';
import path from 'node:path';

const reportPath = path.resolve(
    process.cwd(),
    process.env.E2E_GPT_REPORT_PATH ?? 'txt_e2e_testes.txt',
);

fs.mkdirSync(path.dirname(reportPath), { recursive: true });
fs.writeFileSync(
    reportPath,
    [
        'RELATÓRIO CONSOLIDADO DE ERROS E2E — PRAZZU TOOLS sempre respeite o readme da raiz',
        `Gerado em: ${new Date().toISOString()}`,
        `Projeto: ${process.cwd()}`,
        '',
        'INSTRUÇÕES PARA ANÁLISE POR GPT:',
        '1. Considere cada bloco ERRO separadamente.',
        '2. Use Arquivo do teste, mensagem, stack e artefatos para localizar a causa.',
        '3. Diferencie falha da automação de defeito funcional da aplicação.',
        '4. Priorize a primeira causa; erros posteriores podem ser consequência.',
        '',
    ].join('\n'),
    'utf8',
);

console.log(`[E2E] Relatório TXT reiniciado em ${path.relative(process.cwd(), reportPath).replace(/\\/g, '/')}.`);
