FIREFOX SETUP:
1) Install Firefox from mozilla.com

WAMP SETUP:
2) Install WAMP from wampserver.com/en.
2a) If needed, first install Visual C++ from the WAMP download page.
2b) During WAMP's setup, set the SMTP to smtp.gmail.com with the username barryg22@gmail.com.
2c) Also set Firefox to WAMP's default browser if asked.

DROPBOX SETUP:
3) Install Dropbox from dropbox.com.
3a) Connect Dropbox to the account barryg22@comcast.net with password tuxman22.

4) Delete the C:\wamp\www folder; we'll be redirecting our Dropbox here.
4a) Open a command prompt as an administrator by entering Command Prompt in the
    Start Menu search, then right-clicking its icon and choosing Run As Administrator.
4b) In the command prompt, enter the following command:

    mklink /J "C:\wamp\www" "PATH_TO_DROPBOX_HERE"

    Replace PATH_TO_DROPBOX_HERE with the full path to the Dropbox folder.
    Usually that's something like C:\Users\Username\Dropbox, but double-check
    that first to be sure.

DATABASE IMPORT:
5) Click the WAMP icon and click phpmyadmin.
5a) Login with the username "root" and a blank password.
5b) Click Import and browse for the most recent sniders2013.sql file in the Dropbox folder. Import that.
5c) If the import says the file is too large, do the following:
  5c.1) Click the WAMP icon, go to PHP, and click php.ini
  5c.2) Change the line that says upload_max_filesize to be 50M
  5c.3) Change the line that says post_max_size to 100M
  5c.4) Save the file, then click the WAMP icon and choose Restart All
        Services. Wait for the icon to turn green again.
  5c.5) Reload the phpmyadmin page and try #5b again.

MAKING WAMP START WITH WINDOWS:
6) Open the Task Scheduler by typing "Task Scheduler" into the Start Menu search (or Charms search for Windows 8) and clicking its icon.
6a) Click Create Basic Task.
6b) Name it "Start WAMP" with any description.
6c) Choose "When I log on" as the trigger, and "Start a program" as the action.
6d) Browse for C:\wamp\wampmanager.exe as the program, and click Next.
6e) Check the "Open the properties" box and click Finish.
6f) In the General tab, make sure Run With Highest Priveleges (or Run As Administrator) is checked and click OK. Then you can close the Task Scheduler.

CONFIGURING FIREFOX:
7) Go to the folder %APPDATA%/Mozilla/Firefox/Profiles.
7a) Go to the first (and hopefully only) folder in here.
7b) Copy the file prefs.js from the Dropbox folder into this folder, replacing the old version if asked.
7c) Open Firefox.
7d) Press F11 to enter Fullscreen Mode, then close Firefox.

CONFIGURING THE TICKET PRINTER:
8) Install WebKit HTML to Image from here: http://wkhtmltopdf.googlecode.com/files/wkhtmltox-0.11.0_rc1-installer.exe
8a) Keep SnidersPrinter running in the taskbar at all times.

BACKING UP THE DATABASE [OPTIONAL--CAN BE DONE AT ANY TIME]:
9) Click the WAMP icon and click phpmyadmin.
9a)Login with the username "root" and a blank password.
9b) Make sure you're on the sniders2013 database and click Export.
9c) Leave the options at their defaults and click Go. Give it a little while to create the backup file.
9d) Lastly, choose a place to save the backup.

NOTES:
-Whenever the computer is first turned on, WAMP will open, but Firefox won't. Make sure to open Firefox, as that's where the system will be.

-Shortcut keys, indicated by underlined letters in buttons, are accessed by pressing ALT+{KEY}.

*-Firefox installation (step 1) and configuations (steps 7-7d) must be done on every computer using the system. Steps 2-6 and 8 must only be done on the main server computer.
*-On all non-server computers, you should set the homepage of Firefox (Firefox button->Options->Options) to the local IP of the server on the network. This can be found by opening a command prompt on the server computer and typing ipconfig. It'll be listed as "IPv4 Address". Only the server should NOT have its homepage changed.

-To backup database, run: C:\wamp\bin\mysql\mysql###\bin\mysqldump -uUSERNAME -pPASSWORD sniders2013 > OUTPUT_FILE.sql