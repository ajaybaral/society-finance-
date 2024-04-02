<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Maintenance Records</title>
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
        }

        .reset-btn:hover {
            background-color: #d32f2f;
        }
    </style>
</head>
<body>

<h1>Maintenance Records</h1>

<!-- Form for filtering records by month and/or flat number -->
<form action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>" method="GET" class="filter-form" id="filterForm">
    <label for="month">Month:</label>
    <input type="number" id="month" name="month" min="1" max="12" placeholder="MM" class="filter-input">
    
    <label for="year">Year:</label>
    <input type="number" id="year" name="year" min="2000" max="2099" value="<?php echo isset($_GET['year']) ? $_GET['year'] : ''; ?>" class="filter-input">

    <label for="flat_no">Flat Number:</label>
    <input type="text" id="flat_no" name="flat_no" placeholder="Enter flat number" class="filter-input">
    
    <input type="submit" value="Filter" class="filter-submit">
</form>
<?php
// Establish database connection (replace with your credentials)
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

// Handle form submission
if ($_SERVER["REQUEST_METHOD"] == "GET") {
    // Get filter values
    $filter_month = isset($_GET['month']) ? $_GET['month'] : '';
    $filter_year = isset($_GET['year']) ? $_GET['year'] : '';
    $filter_flat_no = isset($_GET['flat_no']) ? $_GET['flat_no'] : '';

    // Construct the SQL query based on the filters
    $sql = "SELECT * FROM maintenance_records WHERE 1=1";

    if (!empty($filter_month) && !empty($filter_year)) {
        $month_year = $filter_year . "-" . str_pad($filter_month, 2, '0', STR_PAD_LEFT);
        $sql .= " AND month_year = '$month_year'";
    }

    if (!empty($filter_flat_no)) {
        $sql .= " AND flat_no = '$filter_flat_no'";
    }

    $sql .= " ORDER BY date";

    // Execute the query
    $result = $conn->query($sql);

    // Check if any records are found
    if ($result->num_rows > 0) {
        // Initialize variables to track month changes
        $currentMonth = null;

        echo "<table id='recordsTable'>";
        echo "<tr><th onclick='sortTable(0)'>Date</th><th onclick='sortTable(1)'>Flat No</th><th onclick='sortTable(2)'>Payment Amount</th><th onclick='sortTable(3)'>Final Amount</th></tr>";

        while ($row = $result->fetch_assoc()) {
            // Extract month and year from the month_year field
            $dateTime = DateTime::createFromFormat('Y-m', $row['month_year']);
            $monthYear = $dateTime->format('F Y');

            // Display month header if it's different from the current month
            if ($monthYear !== $currentMonth) {
                echo "<tr class='month-header'><th colspan='4'>$monthYear</th></tr>";
                $currentMonth = $monthYear;
            }

            // Display record row
            echo "<tr>";
            echo "<td>" . $row["date"] . "</td>";
            echo "<td>" . $row["flat_no"] . "</td>";
            echo "<td>" . $row["payment_amount"] . "</td>";
            echo "<td>" . $row["final_amount"] . "</td>";
            echo "</tr>";
        }
        echo "</table>";
    } else {
        if (!empty($filter_month) && !empty($filter_year)) {
            echo "No maintenance records found for the month $filter_month and year $filter_year.";
        } elseif (!empty($filter_month)) {
            echo "No maintenance records found for the month $filter_month.";
        } elseif (!empty($filter_year)) {
            echo "No maintenance records found for the year $filter_year.";
        } else {
            echo "No maintenance records found.";
        }
    }

    // If any filter is applied, display the Reset button
    if (!empty($filter_month) || !empty($filter_year) || !empty($filter_flat_no)) {
        echo "<form  action='' method='GET'>";
        echo "<input  type='submit' value='Reset' class='reset-btn'>";
        echo "</form>";
    }
}

// Close database connection
$conn->close();
?>
