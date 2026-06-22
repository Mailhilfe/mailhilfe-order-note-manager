$ErrorActionPreference = "Stop"

$Root = Split-Path -Parent (Split-Path -Parent $MyInvocation.MyCommand.Path)
$Slug = "mailhilfe-order-note-manager"
$MainFile = Join-Path $Root "mailhilfe-order-note-manager.php"
$Header = Get-Content $MainFile -Raw
if ($Header -notmatch "Version:\s*([0-9.]+)") { throw "Plugin version not found." }
$Version = $Matches[1]
$BuildDir = Join-Path $Root "build"
$StageRoot = Join-Path $BuildDir $Slug
$ZipFile = Join-Path $BuildDir "$Slug-$Version.zip"

if (Test-Path $StageRoot) { Remove-Item $StageRoot -Recurse -Force }
if (Test-Path $ZipFile) { Remove-Item $ZipFile -Force }
New-Item -ItemType Directory -Path $StageRoot -Force | Out-Null

$ExcludeTopLevel = @(
  ".git", ".github", "build", "docs", "scripts", ".gitignore", ".gitattributes",
  ".editorconfig", ".distignore", "README.md", "README.de.md", "CHANGELOG.md",
  "CONTRIBUTING.md", "SECURITY.md", "SUPPORT.md", "CODE_OF_CONDUCT.md",
  "GITHUB-PUBLISHING.md", "phpcs.xml.dist"
)

Get-ChildItem $Root -Force | Where-Object { $ExcludeTopLevel -notcontains $_.Name } | ForEach-Object {
  Copy-Item $_.FullName -Destination $StageRoot -Recurse -Force
}

Compress-Archive -Path $StageRoot -DestinationPath $ZipFile -CompressionLevel Optimal
Write-Host "Created $ZipFile"
