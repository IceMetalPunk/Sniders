@echo off
mkdir DB_Backups

echo Backing up database...
"C:\wamp\bin\mysql\mysql5.6.12\bin\mysqldump.exe" -uroot -ptux898 sniders2013 > ./DB_Backups/snidersbackup.sql

echo Backing up work ticket images...
echo D | xcopy "C:\wamp\www\tickets" "DB_Backups/tickets" /E /Y

echo Backing up invoices and reports...
echo D | xcopy "C:\wamp\www\billing" "DB_Backups/billing" /E /Y

echo Backup completed. Press ENTER.
pause