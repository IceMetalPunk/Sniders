QUESTIONS:
1) Should payments, credits, extra charges, and other adjustments be listed on the invoice as the date they were entered or the date they were invoiced?

TODO:
1) Invoice changes:
	a. Include opening balance
	b. Include adjusted charges (lost items, etc.) with work tickets and consolidate them into "previous invoice" totals
	c. Include payments and other adjusted credits on invoices in a separate section and DO NOT consolidate them into "previous invoice" totals
	d. Only include charges (with discount) and "previous invoice" totals in the total invoice amount
	e. Show a "total balance" at the bottom, which includes the invoice's total minus the listed payments and credits
3) End-of-week cycle
	a. Process invoices into accounts receivable table
	b. Print a summary of all (nonzero) account balances
	c. Update the customer table to have the closing
4) End-of-month cycle
	a. Print a summary of all customers' nonzero balances for the month
	b. For each customer, print a statement
		i. The statement should list all invoice totals for the month and their opening and closing balance

--------------------------
INSTALLATION AND SETUP
--------------------------
FIREFOX SETUP:
1) Install Firefox from mozilla.com

WAMP SETUP:
2) Install WAMP from wampserver.com/en.
2a) If needed, first install Visual C++ from the WAMP download page.
2b) During WAMP's setup, set the SMTP to smtp.gmail.com with the username barryg22@gmail.com.
2c) Also set Firefox to WAMP's default browser if asked.

GITHUB SETUP:
3) Install GitHub from windows.github.com
3a) Open the Git Shell from the desktop
3b) Run the following command to change to the WAMP root:
	cd "C:\wamp\www"
3c) Run the following two commands, in order, to setup the GitHub repository in that directory. MAKE SURE the dot is at the end of the second command!
	git init
	git clone https://github.com/IceMetalPunk/Sniders.git .

DATABASE IMPORT:
5) Click the WAMP icon and click phpmyadmin.
5a) Login with the username "root" and a blank password.
5b) Click Import and browse for the most recent sniders2013.sql file in the WAMP/www folder. Import that.
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
7b) Copy the file prefs.js from the WAMP/www folder into this folder, replacing the old version if asked.
7c) Open Firefox.
7d) Press F11 to enter Fullscreen Mode, then close Firefox.

CONFIGURING THE TICKET PRINTER:
8) Install WebKit HTML to Image from here: http://wkhtmltopdf.googlecode.com/files/wkhtmltox-0.11.0_rc1-installer.exe
8a) Install PrintHTML from www.printhtml.com/download.php AND ALSO install the DHTML Editing Control for Applications Redistributable Package from the same page.
8b) Keep SnidersPrinter running in the taskbar at all times.

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

-To backup database from a command prompt, you can run the following command: C:\wamp\bin\mysql\mysql5.6.12\bin\mysqldump -uUSERNAME -pPASSWORD sniders2013 > OUTPUT_FILE.sql