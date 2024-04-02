<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Maintenance Records Form</title>
    <style>
     .container {
    background-color: #fff;
    padding: 20px;
    border-radius: 5px;
    box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
    margin-bottom: 20px;
    max-height: auto; /* Set maximum height */
    overflow-y: auto; /* Make container scrollable */
}

body {
    font-family: Arial, sans-serif;
    background-color: #f4f4f4;
    margin: 0;
    padding: 0;
    /* Remove display:flex and justify-content:center */
    align-items: center;
    min-height: 100vh; /* Use min-height instead of height */
}


        h1 {
            text-align: center;
            color: #333;
        }

        form {
            max-width: 400px;
            margin: 0 auto;
        }

        label {
            display: block;
            margin-bottom: 10px;
        }

        input[type="date"],
        input[type="number"],
        input[type="text"],
        select {
            width: 100%;
            padding: 10px;
            margin-bottom: 15px;
            border: 1px solid #ccc;
            border-radius: 5px;
            box-sizing: border-box;
            font-size: 16px;
        }

        input[type="checkbox"] {
            margin-right: 5px;
        }

        input[type="submit"] {
            background-color: #4CAF50;
            color: white;
            padding: 14px 20px;
            margin: 8px 0;
            border: none;
            border-radius: 4px;
            cursor: pointer;
            width: 100%;
            font-size: 16px;
        }

        input[type="submit"]:hover {
            background-color: #45a049;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 20px;
        }

        table th, table td {
            border: 1px solid #ddd;
            padding: 8px;
            text-align: left;
        }

        table th {
            background-color: #f2f2f2;
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

<div class="container">
<?php include 'navbar.php'; ?>
    <h1>Maintenance Records Form</h1>

    <form action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>" method="POST" id="maintenanceForm">
        <label for="date">Date:</label>
        <input type="date" id="date" name="date" required>

        <label for="flatNo">Flat No:</label>
        <select id="flatNo" name="flatNo" required>
            <option value="">Select Flat No</option>
            <optgroup label="Ground Floor">
                <option value="001">001</option>
                <option value="002">002</option>
                <option value="003">003</option>
                <option value="004">004</option>
            </optgroup>
            <optgroup label="First Floor">
                <option value="101">101</option>
                <option value="102">102</option>
                <option value="103">103</option>
                <option value="104">104</option>
            </optgroup>
            <optgroup label="Second Floor">
                <option value="201">201</option>
                <option value="202">202</option>
                <option value="203">203</option>
                <option value="204">204</option>
            </optgroup>
            <optgroup label="Third Floor">
                <option value="301">301</option>
                <option value="302">302</option>
                <option value="303">303</option>
                <option value="304">304</option>
            </optgroup>
            <!-- Add options for other floors -->
        </select>

        <label for="paymentAmount">Payment Amount:</label>
        <input type="number" id="paymentAmount" name="paymentAmount" min="700" step="5" required>

        <label>Extra Fees:</label>

        <label for="sinkingFees">Sinking Fees:</label>
        <input type="text" id="sinkingFees" name="sinkingFees" value="100" readonly>

        <label for="parkingFees">1)Parking Fees</label>
        <input type="checkbox" id="parkingFees" name="parkingFees">

        <label for="lateFees">2)Late Fees</label>
        <input type="checkbox" id="lateFees" name="lateFees">

        <br><br>
        <label for="finalAmount">Final Amount:</label>
        <input type="text" id="finalAmount" name="finalAmount" readonly>

        <input type="submit" value="Submit" name="submit">

    </form>



    <h2>Recent Maintenance Records</h2>
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

// Process form submission
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST["submit"])) {
    // Prepare data for insertion
    $date = $_POST["date"];
    $flatNo = $_POST["flatNo"];
    $paymentAmount = $_POST["paymentAmount"]; // Retrieve payment amount from the form

    // Extract month and year from the date
    $dateTime = new DateTime($date);
    $monthYear = $dateTime->format('Y-m');

    // Check if the maintenance record already exists for the specified flat_no and month-year
    $checkQuery = "SELECT * FROM maintenance_records WHERE flat_no = '$flatNo' AND month_year = '$monthYear'";
    $checkResult = $conn->query($checkQuery);

    if ($checkResult->num_rows > 0) {
        // Display error message if the maintenance has already been paid for the same month-year and flat
        echo "Maintenance for flat number $flatNo has already been paid for the month of " . $dateTime->format('F Y') . ".";
    } else {
        // Proceed with inserting the maintenance record into the database
        $sinkingFees = 100;
        $parkingFees = isset($_POST["parkingFees"]) ? 1 : 0;
        $lateFees = isset($_POST["lateFees"]) ? 1 : 0;
        $finalAmount = $paymentAmount + $sinkingFees + ($parkingFees ? 25 : 0) + ($lateFees ? 25 : 0); // Calculate final amount

        // SQL query to insert data into the database
        $sql = "INSERT INTO maintenance_records (date, month_year, flat_no, payment_amount, sinking_fees, parking_fees, late_fees, final_amount)
                VALUES ('$date', '$monthYear', '$flatNo', '$paymentAmount', '$sinkingFees', '$parkingFees', '$lateFees', '$finalAmount')";

        if ($conn->query($sql) === TRUE) {
            echo "New record created successfully";
        } else {
            echo "Error: " . $sql . "<br>" . $conn->error;
        }
    }
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

// Fetch recent maintenance records
$sql = "SELECT * FROM maintenance_records ORDER BY id DESC LIMIT 5"; // Change the LIMIT as per your requirement
$result = $conn->query($sql);

// Display records in a table
if ($result->num_rows > 0) {
    echo "<table>";
    echo "<tr><th>Date</th><th>Flat No</th><th>Payment Amount</th><th>Final Amount</th><th>Action</th></tr>";
    while ($row = $result->fetch_assoc()) {
        echo "<tr>";
        echo "<td>" . $row["date"] . "</td>";
        echo "<td>" . $row["flat_no"] . "</td>";
        echo "<td>" . $row["payment_amount"] . "</td>";
        echo "<td>" . $row["final_amount"] . "</td>";
        echo "<td><button class='delete-btn' data-id='" . $row["id"] . "'>Delete</button></td>";
        echo "</tr>";
    }
    echo "</table>";
} else {
    echo "No recent maintenance records found.";
}

// Close database connection
$conn->close();
?>

</div>

<script>
document.addEventListener("DOMContentLoaded", function() {
    // Selecting necessary elements
    const parkingFeesCheckbox = document.getElementById("parkingFees");
    const lateFeesCheckbox = document.getElementById("lateFees");
    const finalAmountInput = document.getElementById("finalAmount");
    const paymentAmountInput = document.getElementById("paymentAmount");
    const sinkingFeesInput = document.getElementById("sinkingFees");

    // Function to calculate and update final amount
    function updateFinalAmount() {
        let finalAmount = parseFloat(paymentAmountInput.value);

        if (parkingFeesCheckbox.checked) {
            finalAmount += 25;
        }

        if (lateFeesCheckbox.checked) {
            finalAmount += 25;
        }

        // Adding sinking fees always
        finalAmount += 100;

        finalAmountInput.value = finalAmount.toFixed(2);
    }

    // Initial update of final amount
    updateFinalAmount();

    // Event listeners for checkbox changes and payment amount input change
    parkingFeesCheckbox.addEventListener("change", updateFinalAmount);
    lateFeesCheckbox.addEventListener("change", updateFinalAmount);
    paymentAmountInput.addEventListener("input", updateFinalAmount);

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
});

</script>


</body>
</html>
