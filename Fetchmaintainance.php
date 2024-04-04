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
        @media print {
            body {
                font-family: Arial, sans-serif;
                background-color: #f4f4f4;
                margin: 0;
                padding: 0;
            }

            h2 {
                text-align: center;
            }

            ul {
                list-style: none;
                padding: 0;
                margin: 0;
            }

            li {
                margin-bottom: 5px;
            }

            #printBtn {
                display: none;
            }
        }
    </style>
</head>
<body>
<?php include 'navbar.php'; ?>
<h1>Maintenance Records</h1>

<!-- Reset button -->


<!-- Form for filtering records by month and/or flat number -->
<form action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>" method="GET" class="filter-form" id="filterForm">
    <label for="month">Month:</label>
    <input type="number" id="month" name="month" min="1" max="12" placeholder="MM" class="filter-input">
    
    <label for="year">Year:</label>
    <input type="number" id="year" name="year" min="2024" max="2099" value="2024" class="filter-input">

    <label for="flat_no">Flat Number:</label>
    <input type="text" id="flat_no" name="flat_no" placeholder="Enter flat number" class="filter-input">
     
    <!-- Pending button -->


    <input type="submit" value="Filter" class="filter-submit">
</form>
<form action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>" method="GET" class="filter-form" id="pendingForm">
    <input type="submit" name="pending" value="Pending" class="filter-submit">
</form>
<button id="printBtn" onclick="window.print()">Print</button>
</body>
</html>


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
// Construct the SQL query based on the filters
$filter_month = $_GET['month'] ?? '';
$filter_year = $_GET['year'] ?? '';
$filter_flat_no = $_GET['flat_no'] ?? '';

$sql = "SELECT * FROM maintenance_records WHERE 1=1";

if (!empty($filter_month) && !empty($filter_year)) {
    $month_year = $filter_year . "-" . str_pad($filter_month, 2, '0', STR_PAD_LEFT);
    $sql .= " AND month_year = '$month_year'";
}

if (!empty($filter_flat_no)) {
    $sql .= " AND flat_no = '$filter_flat_no'";
}

$sql .= " ORDER BY  month_year DESC";


// Execute the query
$result = $conn->query($sql);

// Display records in a table with month headers
if ($result->num_rows > 0) {
    // Initialize variables to track month changes
    $currentMonth = null;

    echo "<table id='recordsTable'>";
    echo "<tr><th onclick='sortTable(0)'>Date</th><th onclick='sortTable(1)'>Flat No</th><th onclick='sortTable(2)'>Payment Amount</th><th onclick='sortTable(3)'>Final Amount</th><th>Action</th></tr>";

    while ($row = $result->fetch_assoc()) {
        // Extract month and year from the month_year field
        $dateTime = DateTime::createFromFormat('Y-m', $row['month_year']);
        $monthYear = $dateTime->format('F Y');

        // Display month header if it's different from the current month
        if ($monthYear !== $currentMonth) {
            echo "<tr class='month-header'><th colspan='5'>$monthYear</th></tr>";
            $currentMonth = $monthYear;
        }

        // Display record row with delete button
        echo "<tr>";
        echo "<td>" . $row["date"] . "</td>";
        echo "<td>" . $row["flat_no"] . "</td>";
        echo "<td>" . $row["payment_amount"] . "</td>";
        echo "<td>" . $row["final_amount"] . "</td>";
        echo "<td><button class='delete-btn' data-id='" . $row['id'] . "'>Delete</button></td>"; // Add delete button with data-id attribute
        echo "</tr>";
    }
    echo "</table>";
} else {
    echo "No maintenance records found.";
}


function fetchPendingMonths($conn) {
    // Function to get the list of months between two dates
    function getMonthsInRange($start_date, $end_date) {
        $start_date = new DateTime($start_date);
        $end_date = new DateTime($end_date);

        $interval = new DateInterval('P1M');
        $period = new DatePeriod($start_date, $interval, $end_date->modify('+1 month'));

        $months = array();
        foreach ($period as $date) {
            $months[] = $date->format('F Y');
        }

        return $months;
    }

    // Get the current date
    $current_month_year = date('F Y');

    // Flat numbers to search for
    $flat_numbers = array('1', '2', '3', '4', '5', '6', '001', '002', '003', '101', '102', '103', '104', '201', '202', '203', '204', '301', '302', '303', '304');

    // Array to store the list of months for each flat number
    $months_for_flat_numbers = array();

    // Filter flat numbers that have already paid
    $sql_paid_flat_numbers = "SELECT DISTINCT flat_no FROM maintenance_records";
    $result_paid_flat_numbers = $conn->query($sql_paid_flat_numbers);
    $paid_flat_numbers = array();
    if ($result_paid_flat_numbers->num_rows > 0) {
        while ($row_paid_flat_number = $result_paid_flat_numbers->fetch_assoc()) {
            $paid_flat_numbers[] = $row_paid_flat_number['flat_no'];
        }
    }

    $unpaid_flat_numbers = array_diff($flat_numbers, $paid_flat_numbers);

    // Loop through unpaid flat numbers
    foreach ($unpaid_flat_numbers as $flat_number) {
        // Construct SQL query to get the last paid month for the current flat number
        $sql_last_paid_month = "SELECT MAX(month_year) AS last_paid_month FROM maintenance_records WHERE flat_no = '$flat_number'";
        $result_last_paid_month = $conn->query($sql_last_paid_month);
        $row_last_paid_month = $result_last_paid_month->fetch_assoc();
        $last_paid_month = $row_last_paid_month['last_paid_month'];

        // If no records found for the flat number, assume the last paid month is April 2024
        if (empty($last_paid_month)) {
            $last_paid_month = 'April 2024';
        }

        // Get the list of months between the last paid month and the current month
        $months_between = getMonthsInRange($last_paid_month, $current_month_year);

        // Store the list of months for the current flat number
        $months_for_flat_numbers[$flat_number] = $months_between;
    }

    // Output the list of months between last paid month and current month for each flat number
    foreach ($months_for_flat_numbers as $flat_number => $months) {
        echo "<h2>Flat Number: $flat_number</h2>";
        echo "<ul>";
        foreach ($months as $month) {
            echo "<li>$month</li>";
        }
        echo "</ul>";
    }
}
// Check if the "Pending" button is clicked
if (isset($_GET['pending'])) {
    // Call the function to fetch pending months
    fetchPendingMonths($conn);
}

// Check if id parameter is set
if (isset($_POST['id'])) {
    // Escape user inputs for security
    $id = $conn->real_escape_string($_POST['id']);

    // Delete record from the table
    $sql = "DELETE FROM maintenance_records WHERE id = '$id'";

    if ($conn->query($sql) === TRUE) {
        echo "Record deleted successfully";
    } else {
        echo "Error deleting record: " . $conn->error;
    }
}



// If any filter is applied, display the Reset button
if (!empty($filter_month) || !empty($filter_year) || !empty($filter_flat_no)||isset($_GET['pending'])) {
    echo "<form  action='' method='GET'>";
    echo "<input  type='submit' value='Reset' class='reset-btn'>";
    echo "</form>";
}

// Close database connection
$conn->close();
?>

<!-- JavaScript for delete button functionality -->
<script>
     function deleteRecord(event) {
        // Prompt user for confirmation
        const confirmation = confirm("Are you sure you want to delete this record?");

        // Proceed with deletion if user confirms
        if (confirmation) {
            const id = event.target.dataset.id;

            // Send an AJAX request to delete the record from the database
            const xhr = new XMLHttpRequest();
            xhr.open('POST', '<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>', true);
            xhr.setRequestHeader('Content-Type', 'application/x-www-form-urlencoded');
            xhr.onload = function() {
                if (xhr.status === 200) {
                    // Remove the row from the table upon successful deletion
                    event.target.closest('tr').remove();
                } else {
                    console.error('Error deleting record');
                }
            };
            xhr.send('id=' + id);
        }
    }

    // Adding event listeners to delete buttons
    const deleteButtons = document.querySelectorAll('.delete-btn');
    deleteButtons.forEach(button => {
        button.addEventListener('click', deleteRecord);
    });



    
</script>





