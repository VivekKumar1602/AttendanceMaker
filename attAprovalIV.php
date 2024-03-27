<?php
session_start();

// Check if not logged in, redirect to login page
if (!isset($_SESSION['loggedin']) || $_SESSION['loggedin'] !== true) {
    header("Location: applogin.php");
    exit;
}

// Database connection details
include_once('db_connection.php');

// Fetch the last event from attendanceRecord
$sqlEvent = "SELECT * FROM tempattendancerecordiv ORDER BY id DESC LIMIT 1";
$resultEvent = $conn->query($sqlEvent);

$event = null;

if ($resultEvent->num_rows > 0) {
    $event = $resultEvent->fetch_assoc();
}

// Find all roll numbers marked as "P" for the last event
$presentRollNumbers = [];
if ($event) {
    foreach ($event as $key => $value) {
        if ($key !== 'id' && $value === 'P') {
            $presentRollNumbers[] = $key;
        }
    }
}

// Check if Approve button is clicked
if (isset($_POST['approve'])) {
    if ($event) {
        // Copy the row to another table
        $sqlCopy = "INSERT INTO attendancerecordiv (subject, date, ";
        
        for ($i = 1; $i <= 60; $i++) {
            $sqlCopy .= "`$i`, ";
        }
        
        $sqlCopy .= "`301`, `302`, `303`, `351`, `352`, `353`, `354`, `355`, `356`, `357`, `401`, `402`, `602`, `603`, `604`, `606`) SELECT subject, date, ";
        
        for ($i = 1; $i <= 60; $i++) {
            $sqlCopy .= "`$i`, ";
        }
        
        $sqlCopy .= "`301`, `302`, `303`, `351`, `352`, `353`, `354`, `355`, `356`, `357`, `401`, `402`, `602`, `603`, `604`, `606` FROM tempattendancerecordiv WHERE id = " . $event['id'];
        
        if ($conn->query($sqlCopy) === TRUE) {
            // Delete the row from attendanceRecord
            $sqlDelete = "DELETE FROM tempattendancerecordiv WHERE id = " . $event['id'];
            
            if ($conn->query($sqlDelete) === TRUE) {
                header("Location: ".$_SERVER['PHP_SELF']);
                exit();
            } else {
                echo "Error deleting record: " . $conn->error;
            }
        } else {
            echo "Error copying record: " . $conn->error;
        }
    }
}

// Check if Remove button is clicked
if (isset($_POST['remove'])) {
    $rollToRemove = $_POST['rollToRemove'];

    if ($event) {
        // Prepare SQL statement to update attendanceRecord for the selected roll
        $sqlRemove = "UPDATE tempattendancerecordiv SET `$rollToRemove` = 'A', curr_students = curr_students - 1 WHERE id = " . $event['id'];

        // Execute the SQL statement
        if ($conn->query($sqlRemove) === TRUE) {
            header("Location: ".$_SERVER['PHP_SELF']);
            exit();
        } else {
            echo "Error removing attendance: " . $conn->error;
        }
    }
}

// Close the database connection
$conn->close();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Approve Attendance</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            margin: 20px;
        }

        label {
            display: block;
            margin-bottom: 10px;
        }

        table {
            width: 50%;
            border-collapse: collapse;
            margin-bottom: 20px;
        }

        table, th, td {
            border: 1px solid #ccc;
            padding: 8px;
            text-align: left;
        }

        th {
            background-color: #f2f2f2;
        }

        button {
            padding: 10px 20px;
            background-color: #007bff;
            color: white;
            border: none;
            cursor: pointer;
        }

        button:hover {
            background-color: #0056b3;
        }

        .remove-form {
            display: inline-block;
        }

        .remove-form button {
            background-color: #dc3545;
        }

        .remove-form button:hover {
            background-color: #bd2130;
        }
    </style>
</head>
<body>
    <a href="index.html">
        <svg xmlns="http://www.w3.org/2000/svg" x="0px" y="0px" height="30" viewBox="0 0 32 32">
            <path d="M 16 2.59375 L 15.28125 3.28125 L 2.28125 16.28125 L 3.71875 17.71875 L 5 16.4375 L 5 28 L 14 28 L 14 18 L 18 18 L 18 28 L 27 28 L 27 16.4375 L 28.28125 17.71875 L 29.71875 16.28125 L 16.71875 3.28125 Z M 16 5.4375 L 25 14.4375 L 25 26 L 20 26 L 20 16 L 12 16 L 12 26 L 7 26 L 7 14.4375 Z"></path>
        </svg>
    </a>
    <a class="logout-link" href="applogout.php">Logout</a>
    <h2>Approve Attendance</h2>

    <?php if ($event && count($presentRollNumbers) > 0) : ?>
        <table>
            <tr>
                <th>Event</th>
                <td><?php echo $event['subject'] . " - " . $event['date']; ?></td>
            </tr>
            <tr>
                <th>Roll Numbers Marked as "P"</th>
                <td><?php echo implode(', ', $presentRollNumbers); ?></td>
            </tr>
            <tr>
                <th>Number of Roll Numbers Marked as "P"</th>
                <td><?php echo count($presentRollNumbers); ?></td>
            </tr>
        </table>

        <form method="post">
            <button type="submit" name="approve">Approve</button>
        </form>

        <h3>Remove Roll Number from "P" Marked Attendance:</h3>
        <form class="remove-form" method="post">
            <label for="rollToRemove">Select Roll Number to Remove:</label>
            <select name="rollToRemove" id="rollToRemove">
                <?php foreach ($presentRollNumbers as $rollNumber) : ?>
                    <option value="<?php echo $rollNumber; ?>"><?php echo $rollNumber; ?></option>
                <?php endforeach; ?>
            </select>
            <button type="submit" name="remove">Remove</button>
        </form>
    <?php else : ?>
        <p>No attendance data found to approve.</p>
    <?php endif; ?>
</body>
</html>
