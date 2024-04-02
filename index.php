<?php
// Establish database connection (replace with your credentials)
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "society";


try {
    $conn = new PDO("mysql:host=$servername", $username, $password);
    $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    
    // Create database if it doesn't exist
    $sql_create_db = "CREATE DATABASE IF NOT EXISTS society";
    $conn->exec($sql_create_db);
    
    // Switch to the created database
    $conn->exec("USE society");
    
    // Create maintenance_records table if it doesn't exist
    $sql_create_maintenance_records = "
    CREATE TABLE IF NOT EXISTS `maintenance_records` (
      `id` int(11) NOT NULL AUTO_INCREMENT,
      `date` date NOT NULL,
      `month_year` varchar(7) NOT NULL,
      `flat_no` varchar(10) NOT NULL,
      `payment_amount` float NOT NULL,
      `sinking_fees` float NOT NULL,
      `parking_fees` tinyint(1) NOT NULL,
      `late_fees` tinyint(1) NOT NULL,
      `final_amount` float NOT NULL,
      PRIMARY KEY (`id`),
      UNIQUE KEY `unique_month_flat` (`date`,`flat_no`)
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;";
    $conn->exec($sql_create_maintenance_records);
    
    // Create voucher_claims table if it doesn't exist
    $sql_create_voucher_claims = "
    CREATE TABLE IF NOT EXISTS `voucher_claims` (
      `id` int(11) NOT NULL AUTO_INCREMENT,
      `date` date NOT NULL,
      `name` varchar(255) NOT NULL,
      `amount` decimal(10,2) NOT NULL,
      `reason` varchar(500) NOT NULL,
      PRIMARY KEY (`id`)
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;";
    $conn->exec($sql_create_voucher_claims);
    
} catch(PDOException $e) {
    echo "Connection failed: " . $e->getMessage();
}

// Create connection
$conn = new mysqli($servername, $username, $password, $dbname);

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <style>
    /* Reset some default styles */
* {
    margin: 0;
    padding: 0;
    box-sizing: border-box;
}

/* Body styles */
body {
    font-family: Arial, sans-serif;
    background-color: #f2f2f2;
}

/* Navbar styles */
.navbar {
    background-color: #333;
    color: #fff;
    padding: 10px;
    display: flex;
    justify-content: space-between;
    align-items: center;
}

.logo img {
    width: 100px; /* Adjust size as needed */
    height: auto;
}

.nav-items {
    display: flex;
}

.nav-item {
    margin-right: 20px;
}

.nav-item a {
    text-decoration: none;
    color: #fff;
    transition: color 0.3s ease;
}

.nav-item a:hover {
    color: #ffcc00;
}

/* Section styles */

p:hover{
color: red;
}
.section1 {
    padding: 20px;
    background-color: #fff;
    margin: 20px;
    border-radius: 5px;
    box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
}

.section1 h2 {
    color: #333;
    margin-bottom: 15px;
}

.services-list p {
    margin-bottom: 20px; /* Increased spacing between services */
}

.services-list p a {
    color: #333; /* Services link color */
    text-decoration: none;
    transition: color 0.3s ease;
}

.services-list p a:hover {
    color: #666; /* Services link hover color */
}

/* Footer styles */
.footer {
    background-color: #333;
    color: #fff;
    padding: 10px;
    text-align: center;
    margin-top: 20px;
    border-top: 1px solid #666;
}

.footer a {
    color: #fff;
    text-decoration: none;
    transition: color 0.3s ease;
}

.footer a:hover {
    color: #ffcc00;
}

  </style>
    <title>Document</title>
</head>
<body>
    <div class="navbar">
        <div class="logo">
            <img src="ani1.png" alt="Logo">
        </div>
        <div class="nav-items">
            <p class="nav-item"><a href="/SFS/ABOUT-US">ABOUT US</a></p>
            <p class="nav-item"><a href="/SFS/SERVICES">SERVICES</a></p>
            <p class="nav-item"><a href="/SFS/LOGOUT">LOGOUT</a></p>
            <p class="nav-item"><a href="/SFS/CONTACT">CONTACT</a></p>
        </div>
    </div>

    <section class="section1">
        <div class="content">
            <h2>SERVICES</h2>
            <div class="services-list">
                <p><a href="/SFS/maintainance.php">PAY MAINTENANCE</a></p>
                <p><a href="/SFS/Fetchmaintainance.php">MAINTENANCE RECORD</a></p>
                <p><a href="/SFS/Voucher.php">VOUCHER CLAIM</a></p>
                <p><a href="/SFS/FetchVoucher.php">VOUCHER RECORDS</a></p>
                <p><a href="/SFS/KNOW FINANCE STATUS">KNOW FINANCE STATUS</a></p>
                <p><a href="/SFS/OTHER FUNDS CREDIT">OTHER FUNDS CREDIT</a></p>
                <p><a href="/SFS/OTHER FUNDS DEBIT">OTHER FUNDS DEBIT</a></p>
            </div>
        </div>
    </section>

    <footer class="footer">
        <p>COPYRIGHT - BY DHARMRAJ APT</p>
    </footer>
</body>
</html>
