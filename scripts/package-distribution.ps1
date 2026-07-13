[CmdletBinding()]
param(
    [string]$OutputPath = ''
)

$ErrorActionPreference = 'Stop'
$projectRoot = Split-Path -Parent $PSScriptRoot
$projectName = Split-Path -Leaf $projectRoot

if ([string]::IsNullOrWhiteSpace($OutputPath)) {
    $OutputPath = Join-Path (Split-Path -Parent $projectRoot) "$projectName-distribuicao.zip"
}

$temporaryRoot = Join-Path ([System.IO.Path]::GetTempPath()) ("prazzu-package-" + [guid]::NewGuid().ToString('N'))
$stagingRoot = Join-Path $temporaryRoot $projectName

$excludedDirectories = @(
    '.git',
    '.idea',
    '.vscode',
    'node_modules',
    'vendor',
    '.phpunit.cache'
)

$excludedFiles = @(
    '.env',
    'database.sqlite',
    'ARQUIVOS_REMOVIDOS.txt'
)

try {
    New-Item -ItemType Directory -Path $stagingRoot -Force | Out-Null

    Get-ChildItem -LiteralPath $projectRoot -Force | ForEach-Object {
        if ($excludedDirectories -contains $_.Name) {
            return
        }

        if ($excludedFiles -contains $_.Name) {
            return
        }

        Copy-Item -LiteralPath $_.FullName -Destination $stagingRoot -Recurse -Force
    }

    $databaseFile = Join-Path $stagingRoot 'database\database.sqlite'
    if (Test-Path -LiteralPath $databaseFile) {
        Remove-Item -LiteralPath $databaseFile -Force
    }

    $logsDirectory = Join-Path $stagingRoot 'storage\logs'
    if (Test-Path -LiteralPath $logsDirectory) {
        Get-ChildItem -LiteralPath $logsDirectory -File -Force | Remove-Item -Force
    }

    if (Test-Path -LiteralPath $OutputPath) {
        Remove-Item -LiteralPath $OutputPath -Force
    }

    Compress-Archive -Path $stagingRoot -DestinationPath $OutputPath -CompressionLevel Optimal
    Write-Host "Pacote criado: $OutputPath"
} finally {
    if (Test-Path -LiteralPath $temporaryRoot) {
        Remove-Item -LiteralPath $temporaryRoot -Recurse -Force
    }
}
