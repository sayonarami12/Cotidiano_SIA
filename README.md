# Cotidiano Fragrance System - Local Setup Guide (XAMPP)

This guide provides step-by-step instructions on how to set up the Cotidiano Fragrance web system on a new computer using XAMPP. It covers exporting the existing database, transferring the files, and configuring the new environment.

## 📋 Prerequisites
*   **XAMPP** installed on the new computer (Download from [apachefriends.org](https://www.apachefriends.org/)).
*   A web browser (Chrome, Firefox, Edge, etc.).
*   The project folder (`cotidiano`).
*   The exported MySQL database file (`.sql`).

---

## Phase 1: Prepare the Files on YOUR Computer
Before moving to the new computer, you need to ensure you have a clean, working copy of your project and a proper MySQL database backup.

### 1. Export your working database (The MySQL version)
1. Open your browser and go to `http://localhost/phpmyadmin`.
2. Click on **`database_cotidiano`** on the left sidebar.
3. Click the **Export** tab at the top.
4. Leave the settings as **"Quick"** and format as **"SQL"**. Click **Export**.
5. Save this file (usually named `database_cotidiano.sql`) into your project folder. **This is now your master database file. Do NOT use the old `_mssql.sql` file.**

### 2. Ensure your `config.php` is MySQL-ready
Open `api/config.php` in your code editor (like VS Code) and make sure it looks exactly like this:

```php
<?php
header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Methods: GET, POST");
header("Content-Type: application/json");

$host = 'localhost';
$dbname = 'database_cotidiano';
$username = 'root';
$password = '';

try {
    $pdo = new PDO("mysql:host=$host;dbname=$dbname;charset=utf8mb4", $username, $password);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch(PDOException $e) {
    http_response_code(500);
    die(json_encode(["error" => "Connection failed: " . $e->getMessage()]));
}
?>

1. Install XAMPP
Download and install XAMPP from apachefriends.org.

Open the XAMPP Control Panel and start Apache and MySQL.

2. Place the Project Files
Take your zipped cotidiano folder and extract it directly into C:\xampp\htdocs\ on the new computer.

Your path should now look like: C:\xampp\htdocs\cotidiano\index.html.

3. Import the Database
Open your browser and go to http://localhost/phpmyadmin.

On the left sidebar, click New.

Type database_cotidiano in the database name field and click Create.

Click on your new database_cotidiano database, then click the Import tab at the top.

Click Choose File and select the database_cotidiano.sql file you exported in Phase 1.

Scroll down and click Go. You should see a success message, and all your tables should appear on the left.

4. Test the Connection
In your browser, go to: http://localhost/cotidiano/api/test_connection.php

If it says "Connected successfully to MySQL!", you are good to go.

If it says "could not find driver":

Open C:\xampp\php\php.ini.

Search for ;extension=pdo_mysql.

Remove the semicolon so it becomes extension=pdo_mysql.

Save the file, restart Apache in the XAMPP Control Panel, and refresh the page.

5. Launch the System
Open your browser and go to:
http://localhost/cotidiano/index.html
