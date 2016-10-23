NOTICE: The code in this repository are copyright Sniders Formal Wear, and are not for public use, distribution, or modification.

--------------------------
INSTALLATION AND SETUP
--------------------------
FIREFOX SETUP:
1) Install Firefox from mozilla.com

GITHUB SETUP:
2) Install GitHub from windows.github.com
2a) Create the following folders on the computer, if they don't already exist:
	C:\wamp
	C:\wamp\www
2b) Open the Git Shell from the desktop
2c) Run the following command to change to the WAMP root:
	cd "C:\wamp\www"
2d) Run the following two commands, in order, to setup the GitHub repository in that directory. MAKE SURE the dot is at the end of the second command!
	git init
	git clone https://github.com/IceMetalPunk/Sniders.git .

WAMP SETUP:
3) Install WAMP from wampserver.com/en.
3a) If needed, first install Visual C++ from the WAMP download page.
3b) During WAMP's setup, set the SMTP to smtp.gmail.com with the username barryg22@gmail.com.
3c) If it mentions that the WAMP directory already exists, tell it to install there anyway.
3d) Also set Firefox to WAMP's default browser if asked.
3e) On the Windows charms bar, search for "Environment Variables" and open the "Edit the System Environment Variables" dialog.
	3e.1) Click "Environment Variables"
	3e.2) In the System Variables box, find the variable called Path and double-click it to edit.
	3e.3) Add the following to the END of the path variable's value. MAKE SURE to include the starting semicolon!
				;C:\wamp\www\bin\php\php5.4.12
	3e.4) Click OK on all the open dialogs

DATABASE IMPORT:
4) Click the WAMP icon and click phpmyadmin.
4a) Login with the username "root" and a blank password.
4b) Click Import and browse for the most recent sniders2013.sql file in the WAMP/www folder. Import that.
4c) If the import says the file is too large, do the following:
  4c.1) Click the WAMP icon, go to PHP, and click php.ini
  4c.2) Change the line that says upload_max_filesize to be 50M
  4c.3) Change the line that says post_max_size to 100M
  4c.4) Save the file, then click the WAMP icon and choose Restart All
        Services. Wait for the icon to turn green again.
  4c.5) Reload the phpmyadmin page and try #4b again.

MAKING WAMP START WITH WINDOWS:
5) Open the Task Scheduler by typing "Schedule Tasks" in the Charms bar search
5a) Click Create Basic Task.
5b) Name it "Start WAMP" with any description.
5c) Choose "When I log on" as the trigger, and "Start a program" as the action.
5d) Browse for C:\wamp\wampmanager.exe as the program, and click Next.
5e) Check the "Open the properties" box and click Finish.
5f) In the General tab, make sure Run With Highest Privileges (or Run As Administrator) is checked and click OK. Then you can close the Task Scheduler.

CONFIGURING FIREFOX:
6) Go to the folder %APPDATA%/Mozilla/Firefox/Profiles.
6a) Go to the first (and hopefully only) folder in here.
6b) Copy the file prefs.js from the WAMP/www folder into this folder, replacing the old version if asked.
6c) Open Firefox.
6d) Press F11 to enter Fullscreen Mode, then close Firefox.

CONFIGURING THE TICKET PRINTER:
7) Install WebKit HTML to Image from here: http://wkhtmltopdf.googlecode.com/files/wkhtmltox-0.11.0_rc1-installer.exe
7a)Go to www.printhtml.com/download.php and install the DHTML Editing Control for Applications Redistributable Package linked at the bottom of the page.
7b) Keep SnidersPrinter running in the taskbar at all times.

BACKING UP THE DATABASE [OPTIONAL--CAN BE DONE AT ANY TIME]:
8) Click the WAMP icon and click phpmyadmin.
8a)Login with the username "root" and a blank password.
8b) Make sure you're on the sniders2013 database and click Export.
8c) Leave the options at their defaults and click Go. Give it a little while to create the backup file.
8d) Lastly, choose a place to save the backup.

SYNCING BETWEEN COMPUTERS [ONLY NEEDED WHEN THERE ARE CHANGES]
9) If you make a change on one computer that needs to be synced, open the Git Shell and run the following commands:
	cd C:\wamp\www
	git add --all
	git commit -m "Type a message here to describe your changes. Make sure to keep the quotes around it and no quotes inside it."
	git push --all
	
9a) If a change has been made and you need to sync it to your computer, open the Git Shell and run the following commands:
	cd C:\wamp\www
	git fetch
	git reset --hard origin/master

NOTES:
-Whenever the computer is first turned on, WAMP will open, but Firefox won't. Make sure to open Firefox, as that's where the system will be.

-Shortcut keys, indicated by underlined letters in buttons, are accessed by pressing ALT+{KEY}.

*-Firefox installation (step 1) and configurations (steps 6-6d) must be done on every computer using the system. Steps 2-5 and 7 must only be done on the main server computer.
*-You should set the homepage of Firefox (Firefox button->Options->Options) to the local IP of the server on the network. This can be found by opening a command prompt on the server computer and typing ipconfig. It'll be listed as "IPv4 Address".

-To backup database from a command prompt if you want, you can run the following command: C:\wamp\bin\mysql\mysql5.6.12\bin\mysqldump -uUSERNAME -pPASSWORD sniders2013 > OUTPUT_FILE.sql