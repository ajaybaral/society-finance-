<?php
// Database connection parameters
$servername = "localhost";
$username = "root"; // Change to your MySQL username
$password = ""; // Change to your MySQL password

// Create connection
$conn = new mysqli($servername, $username, $password);

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Create database
$sql_create_db = "CREATE DATABASE IF NOT EXISTS society";
if ($conn->query($sql_create_db) === TRUE) {
    echo "Database 'society' created successfully<br>";
} else {
    echo "Error creating database: " . $conn->error . "<br>";
}

// Select database
$conn->select_db("society");

// SQL to create table
$sql_create_table = "CREATE TABLE IF NOT EXISTS maintenance_records (
    id INT(6) UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    month DATE NOT NULL,
    flat_no VARCHAR(10) NOT NULL,
    payment_amount DECIMAL(10, 2) NOT NULL,
    parking_fees DECIMAL(6, 2) NOT NULL,
    late_fees DECIMAL(6, 2) NOT NULL,
    timestamp TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
)";

// Execute table creation query
if ($conn->query($sql_create_table) === TRUE) {
    echo "Table 'maintenance_records' created successfully<br>";
} else {
    echo "Error creating table: " . $conn->error . "<br>";
}

// Close connection
$conn->close();
?>
