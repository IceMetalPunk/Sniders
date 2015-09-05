@echo off
echo Welcome to the full-system updater. This will update the Snider's system to the latest version.
echo.
set /p backup=Create new backup before updating (recommended)? (y/n)
echo.

if /i {%backup%}=={y} (
	echo C | call "Sniders Backup.bat"
	echo.
)

if not exist DB_Backups (
	echo No backups found. For safety, updates will not be performed without a backup in place.
	goto finish
)

if not exist DB_Backups/tickets (
	echo Ticket backup folder could not be found. For safety, updates will not be performed without a valid backup in place.
	goto finish
)

if not exist DB_Backups/billing (
	echo Billing backup folder could not be found. For safety, updates will not be performed without a valid backup in place.
	goto finish
)

echo.
echo Updating system...
echo.
set bash=C:\Users\Stu\AppData\Local\GitHub\PortableGit_c2ba306e536fdf878271f7fe636a147ff37326ad\bin\bash.exe
@"%gitbash%" --login -i -c "exec "UpdateSystem.git""
echo.
echo Update complete. Restoring tickets and invoices from backup.
echo.

echo D | xcopy "DB_Backups/tickets" "C:\wamp\www\tickets" /E /Y
echo D | xcopy "DB_Backups/billing" "C:\wamp\www\billing" /E /Y

echo.
echo Done. Press ENTER to continue.

:finish
echo.
pause