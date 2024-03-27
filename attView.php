<?php
// Database connection details
include_once('db_connection.php');

// Define subject codes and names
$subjects = array(
    2000601 => "E&SU",
    2018602 => "SE",
    2018603 => "DW&DM",
    2018604 => "CNS",
    2000605 => "IoT Adv."
);

$selectedSubject = null;
$subjectName = null;

// Check if form is submitted
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $selectedSubject = $_POST["subject"];
    $subjectName = $subjects[$selectedSubject] ?? null;

    // Fetch attendance record for the selected subject
    $sqlAttendance = "SELECT * FROM attendancerecord WHERE subject = '$selectedSubject' ORDER BY date";
    $resultAttendance = $conn->query($sqlAttendance);
}

// Close the database connection
$conn->close();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Attendance Record</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            margin: 20px;
        }

        label {
            display: block;
            margin-bottom: 10px;
        }

        select {
            padding: 10px;
            margin-bottom: 15px;
            box-sizing: border-box;
        }

        .scrollable-table-container {
            max-width: 100%;
            overflow-x: auto;
        }

        table {
            border-collapse: collapse;
            width: 100%;
        }

        th, td {
            border: 1px solid #ddd;
            padding: 8px;
            text-align: left;
            vertical-align: top;
            min-width: 100px; /* Set a minimum width for columns */
        }

        th {
            background-color: #f2f2f2;
        }

        /* First column sticky */
        th:first-child,
        td:first-child {
            position: sticky;
            left: 0;
            z-index: 1;
            background-color: #fff;
            min-width: 65px;
        }

        /* First column sticky styling */
        th:first-child {
            box-shadow: 5px 0 5px -5px rgba(0, 0, 0, 0.5);
        }

        /* Other styles */
        .sticky-container {
            overflow-x: auto;
        }
    </style>
</head>
<body>
<a href="index.html">
    <svg xmlns="http://www.w3.org/2000/svg" x="0px" y="0px" height="30" viewBox="0 0 32 32">
        <path d="M 16 2.59375 L 15.28125 3.28125 L 2.28125 16.28125 L 3.71875 17.71875 L 5 16.4375 L 5 28 L 14 28 L 14 18 L 18 18 L 18 28 L 27 28 L 27 16.4375 L 28.28125 17.71875 L 29.71875 16.28125 L 16.71875 3.28125 Z M 16 5.4375 L 25 14.4375 L 25 26 L 20 26 L 20 16 L 12 16 L 12 26 L 7 26 L 7 14.4375 Z"></path>
    </svg>
</a>
<h2>Attendance Record</h2>
<form method="POST" action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>">
    <label for="subject">Select Subject:</label>
    <select name="subject" id="subject">
        <option value="">Select a subject</option>
        <?php foreach ($subjects as $code => $name) : ?>
            <option value="<?php echo $code; ?>" <?php if ($code == $selectedSubject) echo "selected"; ?>><?php echo $name; ?></option>
        <?php endforeach; ?>
    </select>

    <button type="submit">Show Attendance</button>
</form>

<?php if ($subjectName) : ?>
    <h3>Attendance for <?php echo $subjectName; ?></h3>
<?php endif; ?>

<div class="scrollable-table-container">
    <?php
    if (isset($resultAttendance) && $resultAttendance->num_rows > 0) {
        echo "<table border='1'>";
        echo "<tr><th>Roll No.</th>";

        // Display dates as headers
        while ($row = $resultAttendance->fetch_assoc()) {
            echo "<th>" . $row['date'] . "</th>";
        }
        echo "<th>Present Fraction</th><th>Present Percentage</th></tr>";

        // Display vertical roll numbers
        for ($i = 1; $i <= 60; $i++) {
            echo "<tr>";
            echo "<td> $i</td>";

            // Reset pointer to the beginning of the result set
            $resultAttendance->data_seek(0);

            // Initialize variables for calculating total days and days present
            $totalDaysRoll = 0;
            $daysPresentRoll = 0;

            while ($row = $resultAttendance->fetch_assoc()) {
                echo "<td>" . $row[$i] . "</td>";
                if ($row[$i] === "P") {
                    $daysPresentRoll++;
                }
                $totalDaysRoll++;
            }

            // Calculate fraction and percentage
            $fraction = $daysPresentRoll . "/" . $totalDaysRoll;
            $percentage = round(($daysPresentRoll / $totalDaysRoll) * 100, 2);
            
            // Format the percentage display
            if ($percentage == intval($percentage)) {
                // If the percentage is an integer, no decimal places
                $percentageDisplay = number_format($percentage, 0);
            } else if ($percentage == round($percentage, 1)) {
                // If one decimal place is zero, show one decimal place
                $percentageDisplay = number_format($percentage, 1);
            } else {
                // Otherwise, show two decimal places
                $percentageDisplay = number_format($percentage, 2);
            }
            
            echo "<td>$fraction</td>";
            echo "<td>$percentageDisplay%</td>";
        }

        // Display attendance for 301, 302, 303, 351, 352
        $extraRollNumbers = array(301, 302, 303, 351, 352, 604);
        foreach ($extraRollNumbers as $roll) {
            echo "<tr>";
            echo "<td>$roll</td>";

            // Reset pointer to the beginning of the result set
            $resultAttendance->data_seek(0);

            // Initialize variables for calculating total days and days present
            $totalDaysRoll = 0;
            $daysPresentRoll = 0;

            while ($row = $resultAttendance->fetch_assoc()) {
                echo "<td>" . $row[$roll] . "</td>";
                if ($row[$roll] === "P") {
                    $daysPresentRoll++;
                }
                $totalDaysRoll++;
            }

            // Calculate fraction and percentage
            $fraction = $daysPresentRoll . "/" . $totalDaysRoll;
            $percentage = round(($daysPresentRoll / $totalDaysRoll) * 100, 2);
            
            // Format the percentage display
            if ($percentage == intval($percentage)) {
                // If the percentage is an integer, no decimal places
                $percentageDisplay = number_format($percentage, 0);
            } else if ($percentage == round($percentage, 1)) {
                // If one decimal place is zero, show one decimal place
                $percentageDisplay = number_format($percentage, 1);
            } else {
                // Otherwise, show two decimal places
                $percentageDisplay = number_format($percentage, 2);
            }
            
            echo "<td>$fraction</td>";
            echo "<td>$percentageDisplay%</td>";
        }

        echo "</table>";
    } else {
        echo "No attendance records found for the selected subject.";
    }
    ?>
</div>
</body>
</html>
