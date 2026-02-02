@echo off
powershell -Command "Get-Content storage/logs/laravel.log -Tail 50 | Select-String -Pattern 'RAW REQUEST|reorder' -Context 2"
pause
