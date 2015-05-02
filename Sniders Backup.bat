@echo off
mkdir DB_Backups

echo Backing up database...
"C:\wamp\bin\mysql\mysql5.6.12\bin\mysqldump.exe" -uroot -ptux898 sniders2013 > ./DB_Backups/snidersbackup.sql

echo .php>>ignore.txt
echo .exe>>ignore.txt
echo .css>>ignore.txt

echo Backing up invoices and reports...
echo D | xcopy "C:\wamp\www\billing" "DB_Backups/billing" /E /Ys /exclude:ignore.txt


echo .html>>ignore.txt

echo Backing up work ticket images...
echo D | xcopy "C:\wamp\www\tickets" "DB_Backups/tickets" /E /Y /exclude:ignore.txt

rm ignore.txt

echo Backup completed. Press ENTER.
echo.
pause