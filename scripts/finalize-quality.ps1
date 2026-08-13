[CmdletBinding()]
param()

$ErrorActionPreference = 'Stop'
$projectRoot = Split-Path -Parent $PSScriptRoot

Push-Location $projectRoot
try {
    & composer format
    if ($LASTEXITCODE -ne 0) { throw 'O Pint não conseguiu formatar o projeto.' }

    & composer release:check
    if ($LASTEXITCODE -ne 0) { throw 'O gate de release ainda possui falhas.' }

    Write-Host 'Formatação aplicada e gate de release aprovado.'
} finally {
    Pop-Location
}
