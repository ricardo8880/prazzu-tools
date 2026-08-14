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

Get-ChildItem -LiteralPath $projectRoot -Recurse -Force -File |
    Where-Object { $_.Name.StartsWith('~$') -or $_.Name -in @('.DS_Store', 'Thumbs.db') } |
    ForEach-Object {
        if ($PSCmdlet.ShouldProcess($_.FullName, 'Remover arquivo temporário')) {
            Remove-Item -LiteralPath $_.FullName -Force
            Write-Host "Removido: $($_.FullName)"
        }
    }

if (Get-Command git -ErrorAction SilentlyContinue) {
    & git -C $projectRoot rm --cached --ignore-unmatch -- .env
    if ($LASTEXITCODE -ne 0) { throw 'Não foi possível remover .env do índice do Git.' }

    & git -C $projectRoot rm -r --cached --ignore-unmatch -- node_modules backup
    if ($LASTEXITCODE -ne 0) { throw 'Não foi possível remover dependências ou backups do índice do Git.' }
}

Write-Host 'Limpeza concluída. .env, node_modules e backup permanecem locais quando existirem, mas ficam fora do índice do Git.'
