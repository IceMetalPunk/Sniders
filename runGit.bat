if not exist %1 exit
set bash=C:\Program Files (x86)\Git\bin\bash.exe
"%bash%" --login -i -c "exec "%1""