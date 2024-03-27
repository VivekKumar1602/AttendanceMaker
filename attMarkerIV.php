<?php
// Start the session
// session_start();

// Database connection details
include_once('db_connection.php');

// Fetch the last event from attendanceRecord
$sqlEvent = "SELECT * FROM tempattendancerecordiv ORDER BY id DESC LIMIT 1";
$resultEvent = $conn->query($sqlEvent);

$event = null;

if ($resultEvent->num_rows > 0) {
    $event = $resultEvent->fetch_assoc();
    $maxLimit = $event['max_students'];
    $currCount = $event['curr_students'];
}

$presentRollNumbers = [];
if ($event) {
    foreach ($event as $key => $value) {
        if ($key !== 'id' && $value === 'P') {
            $presentRollNumbers[] = $key;
        }
    }
}

// Define the roll numbers
$rollNumbers = array_merge(range(1, 60), array(301, 302, 303, 351, 352, 353, 354, 355, 356, 357, 401, 402, 602, 603, 604, 606));

// Check if form is submitted


// Close the database connection
$conn->close();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Mark Attendance</title>
    <script>
        <?php
        // Check for message or error parameters in the URL and display as alert
        if (isset($_GET['message'])) {
            $message = urldecode($_GET['message']);
            echo "alert('$message');";
        }
        if (isset($_GET['error'])) {
            $error = urldecode($_GET['error']);
            echo "alert('$error');";
        }
        ?>
    </script>
    <style>
        body {
            font-family: Arial, sans-serif;
            margin: 0;
            padding: 20px;
            display: flex;
            justify-content: center;
            align-items: center;
            min-height: 100vh;
        }

        .container{
            display: flex;
            flex-direction:column;
        }

        form {
            width: 300px;
            margin: auto;
            padding: 20px;
            border: 1px solid #ccc;
            border-radius: 5px;
            background-color: #f9f9f9;
        }

        label {
            display: block;
            margin-bottom: 10px;
        }

        select {
            width: 100%;
            padding: 10px;
            margin-bottom: 15px;
            box-sizing: border-box;
        }

        button {
            width: 100%;
            padding: 10px;
            background-color: #007bff;
            color: white;
            border: none;
            cursor: pointer;
        }

        button:hover {
            background-color: #0056b3;
        }

        .marked-rolls {
            margin-top: 20px;
            padding: 10px;
            border: 1px solid #ccc;
            border-radius: 5px;
            background-color: #f9f9f9;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 10px;
        }

        th, td {
            border: 1px solid #ccc;
            padding: 8px;
            text-align: left;
        }

        th {
            background-color: #f2f2f2;
        }
    </style>
</head>
<body>
    <div class="container">
        <a href="index.html">
            <svg xmlns="http://www.w3.org/2000/svg" x="0px" y="0px" height="30" viewBox="0 0 32 32">
                <path d="M 16 2.59375 L 15.28125 3.28125 L 2.28125 16.28125 L 3.71875 17.71875 L 5 16.4375 L 5 28 L 14 28 L 14 18 L 18 18 L 18 28 L 27 28 L 27 16.4375 L 28.28125 17.71875 L 29.71875 16.28125 L 16.71875 3.28125 Z M 16 5.4375 L 25 14.4375 L 25 26 L 20 26 L 20 16 L 12 16 L 12 26 L 7 26 L 7 14.4375 Z"></path>
            </svg>
        </a>
        <h2 style="text-align: center;">Mark Attendance</h2>
        <form method="POST" action="markProcessIV.php">
            <label for="event">Select Event:</label>
            <select name="event" id="event" disabled>
                <?php if ($event) : ?>
                    <option value="<?php echo $event['id']; ?>"><?php echo $event['subject'] . " - " . $event['date']; ?></option>
                <?php else : ?>
                    <option>No events found</option>
                <?php endif; ?>
            </select>

            <label for="roll">Select Roll Number:</label>
            <select name="roll" id="roll">
                <option value="" disabled selected>Select your Roll No.</option>
                <?php foreach ($rollNumbers as $roll) : ?>
                    <option value="<?php echo $roll; ?>"><?php echo $roll; ?></option>
                <?php endforeach; ?>
            </select>

            <button type="submit">Mark Attendance</button>
        </form>

        <!-- Display marked rolls -->
        <div class="marked-rolls">
            <h3>Roll Numbers Marked as Present:</h3>
            <?php if (!empty($presentRollNumbers)) : ?>
                <table>
                    <tr>
                        <th>Roll Number</th>
                    </tr>
                    <?php foreach ($presentRollNumbers as $roll) : ?>
                        <tr>
                            <td><?php echo $roll; ?></td>
                        </tr>
                    <?php endforeach; ?>
                </table>
            <?php else : ?>
                <p>No roll numbers marked yet.</p>
            <?php endif; ?>
        </div>
    </div>
</body>
</html>