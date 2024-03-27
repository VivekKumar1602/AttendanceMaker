<?php
// Start the session
session_start();

// Check if form has already been submitted
if (isset($_SESSION['form_submitted']) && $_SESSION['form_submitted'] === true) {
    // Check if 15 minutes have passed since last submission
    if (isset($_SESSION['last_submission_time']) && time() - $_SESSION['last_submission_time'] < 600) {
        // $remainingTime = 600 - (time() - $_SESSION['last_submission_time']);
        $error = urlencode("You cannot mark the attendance again from this device!");
        header("Location: attMarkeriv.php?error=$error");
        exit();
    }
}

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

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $selectedRoll = $_POST["roll"];
    $eventId = $event['id'];

    // Check if current count is less than the max limit
    if ($currCount < $maxLimit) {

        if ($event[$selectedRoll] === "P") {
            // Redirect back to the form page with an error message
            $error = urlencode('Attendance already marked for Roll Number: ' . $selectedRoll);
            header("Location: attMarkeriv.php?error=$error");
            exit();
        } else {
            // Prepare SQL statement to update attendanceRecord for the selected roll
            $sqlUpdate = "UPDATE tempattendancerecordiv 
              SET `$selectedRoll` = 'P', curr_students = curr_students + 1 
              WHERE id = $eventId";

            // Execute the SQL statement
            if ($conn->query($sqlUpdate) === TRUE) {
                // Set session variable to indicate form has been submitted
                $_SESSION['form_submitted'] = true;
                $_SESSION['last_submission_time'] = time(); // Store the current time
                // Redirect back to the form page with a success message
                $message = urlencode('Attendance marked successfully for Roll Number: ' . $selectedRoll);
                header("Location: attMarkeriv.php?message=$message");
                exit();
            } else {
                // Redirect back to the form page with an error message
                $error = urlencode("Error: " . $sqlUpdate . "<br>" . $conn->error);
                header("Location: attMarkeriv.php?error=$error");
                exit();
            }
        }
    } else {
        // Redirect back to the form page with an error message
        $error = urlencode('Max Limit Crossed');
        header("Location: attMarkeriv.php?error=$error");
        exit();
    }
}

// Close the database connection
$conn->close();

header("Location: attMarkeriv.php");
exit();
?>
