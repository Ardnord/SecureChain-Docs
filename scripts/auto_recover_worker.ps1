# PowerShell worker to run CodeIgniter CLI auto:recover every 5 minutes
# Usage: Run this script via Task Scheduler at startup or run in background.

param(
    [string]$PhpPath = "C:\\xampp\\php\\php.exe",
    [string]$ProjectPath = "c:\xampp\htdocs\blockchain",
    [int]$SleepSeconds = 300
)

Write-Output "Starting AutoRecover worker. Project: $ProjectPath"

while ($true) {
    try {
        $startInfo = "$PhpPath $ProjectPath\spark auto:recover"
        Write-Output "Running: $startInfo"
        & $PhpPath "$ProjectPath\spark" auto:recover
    } catch {
        Write-Error "AutoRecover error: $_"
    }

    Start-Sleep -Seconds $SleepSeconds
}
