[CmdletBinding(SupportsShouldProcess = $true)]
param()

$ErrorActionPreference = 'Stop'
$projectRoot = Split-Path -Parent $PSScriptRoot

$targets = @(
    (Join-Path $projectRoot '.idea'),
    (Join-Path $projectRoot 'ARQUIVOS_REMOVIDOS.txt'),
    (Join-Path $projectRoot 'ferramentas'),
    (Join-Path $projectRoot (Split-Path -Leaf $projectRoot))
)

foreach ($target in $targets) {
    if (Test-Path -LiteralPath $target) {
        if ($PSCmdlet.ShouldProcess($target, 'Remover resíduo do projeto')) {
            Remove-Item -LiteralPath $target -Recurse -Force
            Write-Host "Removido: $target"
        }
    } else {
        Write-Host "Não encontrado, nenhuma ação necessária: $target"
    }
}

if (Get-Command git -ErrorAction SilentlyContinue) {
    & git -C $projectRoot rm --cached --ignore-unmatch -- .env
    if ($LASTEXITCODE -ne 0) { throw 'Não foi possível remover .env do índice do Git.' }
}

Write-Host 'Limpeza concluída. A cópia aninhada foi removida e .env permanece local, mas fora do índice do Git.'
