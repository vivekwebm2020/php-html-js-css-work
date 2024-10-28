# Define the path to the Google Chrome executable
$chromePath = "C:\Program Files\Google\Chrome\Application\chrome.exe"

# Define the .crx file extension registry key
$crxKeyPath = "HKCU:\Software\Classes\.crx"

# Create or update the .crx file association
New-Item -Path $crxKeyPath -Force -Value "Chrome.crx"

# Set the default application to open .crx files
$crxAppKeyPath = "HKCU:\Software\Classes\Chrome.crx\shell\open\command"
New-Item -Path $crxAppKeyPath -Force -Value "`"$chromePath`" `"%1`""

# Optionally set Chrome as the default program for .crx files
$defaultProgramsKeyPath = "HKCU:\Software\Microsoft\Windows\Shell\Associations\UrlAssociations\crx\UserChoice"
Set-ItemProperty -Path $defaultProgramsKeyPath -Name "ProgId" -Value "Chrome.crx"
