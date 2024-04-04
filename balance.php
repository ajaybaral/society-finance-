<!DOCTYPE html>
<html>
<head>
    <title>Balance and Recent Records</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            background-color: #f4f4f4;
            margin: 0;
            padding: 0;
        }

        h1 {
            text-align: center;
            margin-top: 20px;
        }

        h2 {
            text-align: center;
            margin-top: 20px;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 20px;
        }

        th, td {
            border: 1px solid #ddd;
            padding: 8px;
            text-align: left;
            cursor: pointer; /* Add cursor pointer to indicate sorting */
        }

        th {
            background-color: #f2f2f2;
        }

        .month-header {
            background-color: #ddd;
            font-weight: bold;
            text-align: center;
        }

        .month_text {
            display: flex;
            align-items: center;
            justify-content: center;
        }

        .filter-form, form {
            margin-bottom: 20px;
            text-align: center;
        }

        .filter-input {
            margin: 5px;
            padding: 8px;
            border-radius: 5px;
            border: 1px solid #ccc;
            box-sizing: border-box;
        }

        .filter-submit {
            background-color: #4CAF50; /* Green */
            border: none;
            color: white;
            padding: 10px 20px;
            text-align: center;
            text-decoration: none;
            display: inline-block;
            font-size: 16px;
            cursor: pointer;
            border-radius: 5px;
            transition: background-color 0.3s;
        }

        .filter-submit:hover {
            background-color: #45a049;
        }

        .reset-btn {
            background-color: #f44336;
            border: none;
            color: white;
            padding: 10px 20px;
            text-align: center;
            text-decoration: none;
            display: inline-block;
            font-size: 16px;
            cursor: pointer;
            border-radius: 5px;
            transition: background-color 0.3s;
            margin: 8px;
        }

        .reset-btn:hover {
            background-color: #d32f2f;
           
        }
        .delete-btn {
            background-color: #ff5555;
            color: white;
            border: none;
            padding: 8px 12px;
            border-radius: 4px;
            cursor: pointer;
        }

        .delete-btn:hover {
            background-color: #cc0000;
        }
    </style>
</head>
<body>
<?php include 'navbar.php'; ?>
    <?php
    // Assuming you have established a database connection
    $servername = "localhost";
    $username = "root";
    $password = "";
    $dbname = "society";

    // Create connection
    $conn = new mysqli($servername, $username, $password, $dbname);

    // Check connection
    if ($conn->connect_error) {
        die("Connection failed: " . $conn->connect_error);
    }

    // Calculate Balance of Money
    $sql_balance = "
        SELECT
            COALESCE((SELECT SUM(final_amount) FROM maintenance_records), 0) -
            COALESCE((SELECT SUM(amount) FROM voucher_claims), 0) AS balance";
    $result_balance = $conn->query($sql_balance);
    $balance_row = $result_balance->fetch_assoc();
    $balance = $balance_row['balance'];

    // Ensure balance doesn't go negative
    $balance = max($balance, 0);

    // Retrieve Recent Maintenance Records
    $sql_maintenance = "SELECT * FROM maintenance_records ORDER BY date DESC LIMIT 5";
    $result_maintenance = $conn->query($sql_maintenance);

    // Retrieve Recent Voucher Claims
    $sql_voucher = "SELECT * FROM voucher_claims ORDER BY date DESC LIMIT 5";
    $result_voucher = $conn->query($sql_voucher);

    // Close the connection
    $conn->close();
    ?>

    <h1>Balance of Money: <?php echo $balance; ?></h1>
    <h2>Recent Maintenance Records</h2>
    <table border="1">
        <tr>
            <th>ID</th>
            <th>Date</th>
            <th>Flat No</th>
            <th>Payment Amount</th>
            <!-- Add more columns as needed -->
        </tr>
        <?php
        while ($row = $result_maintenance->fetch_assoc()) {
            echo "<tr>";
            echo "<td>".$row['id']."</td>";
            echo "<td>".$row['date']."</td>";
            echo "<td>".$row['flat_no']."</td>";
            echo "<td>".$row['payment_amount']."</td>";
            // Add more columns as needed
            echo "</tr>";
        }
        ?>
    </table>

    <h2>Recent Voucher Claims</h2>
    <table border="1">
        <tr>
            <th>ID</th>
            <th>Date</th>
            <th>Name</th>
            <th>Amount</th>
            <!-- Add more columns as needed -->
        </tr>
        <?php
        while ($row = $result_voucher->fetch_assoc()) {
            echo "<tr>";
            echo "<td>".$row['id']."</td>";
            echo "<td>".$row['date']."</td>";
            echo "<td>".$row['name']."</td>";
            echo "<td>".$row['amount']."</td>";
            // Add more columns as needed
            echo "</tr>";
        }
        ?>
    </table>
</body>
</html>
