Step 1: Install XAMPP
Download and install XAMPP from the official website: apachefriends.org.

Step 2: Prepare the Project Files
Download the Cotidiano project file.

If it is in a .zip format, extract (unzip) the folder first.

Copy the entire cotidiano folder and paste it into your XAMPP htdocs directory.

Typical path: C:\xampp\htdocs\

Final path should look like: C:\xampp\htdocs\cotidiano\

Step 3: Start XAMPP
Open the XAMPP Control Panel.

Click Start next to Apache.

Click Start next to MySQL.

⚠️ Important: Make sure both modules turn green. If MySQL turns red or refuses to start, you have a port conflict (see the Troubleshooting section below).

Step 4: Create the Database
Open your web browser and go to: http://localhost/phpmyadmin

On the left sidebar, click New.

In the "Database name" field, type exactly: database_cotidiano

Click the Create button.

Step 5: Import the Database
Click on your newly created database_cotidiano database on the left sidebar.

At the top of the screen, click the Import tab.

Under "File to import", click Choose File.

Navigate to your project folder (C:\xampp\htdocs\cotidiano\) and select the database file (usually named database_cotidiano.sql).

Scroll to the bottom and click the Go button.

You should see a green success message, and the database tables will appear on the left.

If you get an error while importing, go back to the XAMPP Control Panel and double-check that MySQL is started and glowing green.

Step 6: Launch the Website
Open a new tab in your browser.

Go to: http://localhost/cotidiano

You should now see the Cotidiano website live on your screen!


Troubleshooting Common Issues
Problem: MySQL won't start (turns red in XAMPP)
This usually means another program is using the database port.

Quick Fix: Open Task Manager (Ctrl+Shift+Esc), find mysqld.exe, right-click it, and select End Task. Then click Start on MySQL in XAMPP again.

Alternative Fix: Change XAMPP's MySQL port to 3307 in the XAMPP Config, and update the port in your project's api/config.php file.

Problem: The website loads but shows a "Database Connection Error"

Make sure you completed Step 4 and Step 5 correctly.

Check that the api/config.php file in your project folder has the correct database name (database_cotidiano), username (root), and password (blank).

Problem: The website shows no products

If the database imported successfully but no products show up, run the seed script by visiting this link in your browser: http://localhost/cotidiano/api/seed_products.php
