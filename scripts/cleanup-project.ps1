[CmdletBinding(SupportsShouldProcess = $true)]
param()

$ErrorActionPreference = 'Stop'
$projectRoot = Split-Path -Parent $PSScriptRoot

$targets = @(
    (Join-Path $projectRoot '.idea'),
    (Join-Path $projectRoot 'ARQUIVOS_REMOVIDOS.txt'),
    (Join-Path $projectRoot 'ferramentas')
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

Write-Host 'Limpeza concluída. .git, .env, vendor e database/database.sqlite foram preservados.'
