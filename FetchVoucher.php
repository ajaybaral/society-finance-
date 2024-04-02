<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Voucher Claims</title>
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
    </style>
</head>
<body>
<?php include 'navbar.php'; ?>
<h1>Voucher Claims</h1>

<!-- Form for filtering records by date, name, and/or reason -->
<form action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>" method="GET" class="filter-form" id="filterForm">
    <label for="date">Date:</label>
    <input type="date" id="date" name="date" class="filter-input">
    
    <label for="name">Name:</label>
    <input type="text" id="name" name="name" placeholder="Enter name" class="filter-input">
    
    <label for="reason">Reason:</label>
    <input type="text" id="reason" name="reason" placeholder="Enter reason" class="filter-input">
    
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

// Construct the SQL query based on the filters
$filter_date = $_GET['date'] ?? '';
$filter_name = $_GET['name'] ?? '';
$filter_reason = $_GET['reason'] ?? '';

$sql = "SELECT * FROM voucher_claims WHERE 1=1";

if (!empty($filter_date)) {
    $sql .= " AND date = '$filter_date'";
}

if (!empty($filter_name)) {
    $sql .= " AND name LIKE '%$filter_name%'";
}

if (!empty($filter_reason)) {
    $sql .= " AND reason LIKE '%$filter_reason%'";
}

$sql .= " ORDER BY date DESC";

// Execute the query
$result = $conn->query($sql);

// Initialize month variable to keep track of current month
$currentMonth = null;

// Display records in a table
if ($result->num_rows > 0) {
    echo "<table id='recordsTable'>";
    echo "<tr><th colspan='5'>Voucher Claims</th></tr>";
    
    while ($row = $result->fetch_assoc()) {
        $date = date_create($row['date']);
        $month = date_format($date, 'F Y'); // Format date as month name and year
        
        // Check if month has changed
        if ($month !== $currentMonth) {
            $currentMonth = $month;
            echo "<tr><th colspan='5'>$currentMonth</th></tr>"; // Add month header
            echo "<tr><th>Date</th><th>Name</th><th>Amount</th><th>Reason</th><th>Action</th></tr>"; // Table header row
        }
        
        // Table row for each record
        echo "<tr>";
        echo "<td>" . $row["date"] . "</td>";
        echo "<td>" . $row["name"] . "</td>";
        echo "<td>" . $row["amount"] . "</td>";
        echo "<td>" . $row["reason"] . "</td>";
        echo "<td><button class='delete-btn' data-id='" . $row['id'] . "'>Delete</button></td>"; // Add delete button with data-id attribute
        echo "</tr>";
    }
    
    echo "</table>";
} else {
    echo "No voucher claims found.";
}

// Check if id parameter is set
if (isset($_POST['id'])) {
    // Escape user inputs for security
    $id = $conn->real_escape_string($_POST['id']);

    // Delete record from the table
    $sql = "DELETE FROM voucher_claims WHERE id = '$id'";

    if ($conn->query($sql) === TRUE) {
        echo "Record deleted successfully";
    } else {
        echo "Error deleting record: " . $conn->error;
    }
}

// If any filter is applied, display the Reset button
if (!empty($filter_date) || !empty($filter_name) || !empty($filter_reason)) {
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

</body>
</html>
