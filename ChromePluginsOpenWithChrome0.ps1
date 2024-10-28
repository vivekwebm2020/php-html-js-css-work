# Define the path to the Google Chrome executable
$chromePath = "C:\Program Files\Google\Chrome\Application\chrome.exe"

# Define the .crx file extension registry key
$crxKeyPath = "HKCU:\Software\Classes\.crx"

# Check if the .crx registry key exists; if not, create it
if (-not (Test-Path $crxKeyPath)) {
    New-Item -Path $crxKeyPath -Force -Value "Chrome.crx"
}

# Define the command registry key path for opening .crx files
$crxAppKeyPath = "HKCU:\Software\Classes\Chrome.crx\shell\open\command"

# Check if the command key exists; if not, create it
if (-not (Test-Path $crxAppKeyPath)) {
    New-Item -Path $crxAppKeyPath -Force -Value "`"$chromePath`" `"%1`""
} else {
    # If it exists, just update the value to ensure it's set correctly
    Set-ItemProperty -Path $crxAppKeyPath -Name "(default)" -Value "`"$chromePath`" `"%1`""
}

# Optionally set Chrome as the default program for .crx files
$defaultProgramsKeyPath = "HKCU:\Software\Microsoft\Windows\Shell\Associations\UrlAssociations\crx\UserChoice"

# Check if the UserChoice key exists; if not, create it
if (-not (Test-Path $defaultProgramsKeyPath)) {
    New-Item -Path $defaultProgramsKeyPath -Force
}

# Set the ProgId to associate .crx files with Chrome
Set-ItemProperty -Path $defaultProgramsKeyPath -Name "ProgId" -Value "Chrome.crx"

# Confirm the association
Write-Host "Successfully set .crx files to open with Google Chrome."
