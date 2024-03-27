<?php
session_start();

// Check if not logged in, redirect to login page
if (!isset($_SESSION['loggedin']) || $_SESSION['loggedin'] !== true) {
    header("Location: evelogin.php");
    exit;
}
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

// Check if form is submitted
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $subjectCode = $_POST["subject"];
    $date = $_POST["date"];
    $maxStudents = $_POST["max_students"];

    // Prepare SQL statement to insert data into attendanceRecord
    $sql = "INSERT INTO tempattendancerecord (subject, date";

    // Adding columns 1 to 60 for "A" (absent)
    for ($i = 1; $i <= 60; $i++) {
        $sql .= ", `$i`";
    }

    // Adding columns 301, 302, 303, 351, 352 for "A" (absent)
    $sql .= ", `301`, `302`, `303`, `351`, `352`, `604`, `max_students`) VALUES ('$subjectCode', '$date'";

    for ($i = 1; $i <= 60; $i++) {
        $sql .= ", 'A'";
    }

    // Adding columns 301, 302, 303, 351, 352 for "A" (absent)
    $sql .= ", 'A', 'A', 'A', 'A', 'A', 'A', '$maxStudents')";

    // Execute the SQL statement
    if ($conn->query($sql) === TRUE) {
        echo "<script>alert('New record created successfully');</script>";

    } else {
        echo "Error: " . $sql . "<br>" . $conn->error;
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
    <title>Attendance Form</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/datepicker/1.0.10/datepicker.min.css">
    <style>
        body {
            font-family: Arial, sans-serif;
            margin: 0;
            padding: 0;
            display: flex;
            justify-content: center;
            align-items: center;
            height: 100vh;
            background-color: #f7f7f7;
        }

        .form-container {
            width: 400px;
            padding: 20px;
            background-color: #fff;
            border-radius: 8px;
            box-shadow: 0px 2px 10px rgba(0, 0, 0, 0.1);
        }

        label {
            display: block;
            margin-bottom: 10px;
        }

        input[type="text"],
        select,
        input[type="date"],
        button {
            width: 100%;
            padding: 10px;
            margin-bottom: 15px;
            box-sizing: border-box;
        }

        button {
            background-color: #007bff;
            color: white;
            border: none;
            cursor: pointer;
        }

        button:hover {
            background-color: #0056b3;
        }
    </style>
</head>

<body>
    <div class="form-container">
        <a href="index.html">
            <svg xmlns="http://www.w3.org/2000/svg" x="0px" y="0px" height="30" viewBox="0 0 32 32">
                <path d="M 16 2.59375 L 15.28125 3.28125 L 2.28125 16.28125 L 3.71875 17.71875 L 5 16.4375 L 5 28 L 14 28 L 14 18 L 18 18 L 18 28 L 27 28 L 27 16.4375 L 28.28125 17.71875 L 29.71875 16.28125 L 16.71875 3.28125 Z M 16 5.4375 L 25 14.4375 L 25 26 L 20 26 L 20 16 L 12 16 L 12 26 L 7 26 L 7 14.4375 Z"></path>
            </svg>
        </a>
        <a class="logout-link" href="evelogout.php">Logout</a>
        <h2 style="text-align: center;">Create Attendance Event</h2>
        <form method="POST" action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>">
            <label for="subject">Subject:</label>
            <select name="subject" id="subject">
                <?php
                // Display options for subjects
                foreach ($subjects as $code => $name) {
                    echo "<option value=\"$code\">$name</option>";
                }
                ?>
            </select>

            <label for="date">Date:</label>
            <input type="date" name="date" id="date" autocomplete="off">

            <label for="max_students">Max Students:</label>
            <input type="number" name="max_students" id="max_students" min="1" required>

            <button type="submit">Create Event</button>
        </form>
    </div>

    <!-- Include datepicker library -->
    <script src="https://cdnjs.cloudflare.com/ajax/libs/datepicker/1.0.10/datepicker.min.js"></script>
    <script>
        // Initialize datepicker
        const datepicker = new Datepicker(document.getElementById('date'), {
            autohide: true,
            format: 'yyyy-mm-dd'
        });
    </script>
</body>

</html>
