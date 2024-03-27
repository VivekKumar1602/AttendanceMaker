<?php
session_start();

// Check if already logged in, redirect to attendance approval if true
if (isset($_SESSION['loggedin']) && $_SESSION['loggedin'] === true && $_SESSION["username"] === "vivek") {
    header("Location: eventCreater.php");
    exit;
}

else if (isset($_SESSION['loggedin']) && $_SESSION['loggedin'] === true && $_SESSION["username"] === "semiv") {
    header("Location: eventCreaterIV.php");
    exit;
}

// Define valid credentials
$valid_username1 = "vivek";
$valid_password1 = "1602";

$valid_username2 = "semiv";
$valid_password2 = "semiv2022";

// Check if form is submitted
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $username = $_POST["username"];
    $password = $_POST["password"];

    // Check if credentials match
    if ($username === $valid_username1 && $password === $valid_password1) {
        // Set session variables
        $_SESSION["loggedin"] = true;
        $_SESSION["username"] = $username;

        // Redirect to attendance approval page
        header("Location: eventCreater.php");
        exit;
    }
    
    else if ($username === $valid_username2 && $password === $valid_password2) {
        // Set session variables
        $_SESSION["loggedin"] = true;
        $_SESSION["username"] = $username;

        // Redirect to attendance approval page
        header("Location: eventCreaterIV.php");
        exit;
    }
    else {
        $error = "Invalid username or password.";
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            margin: 0;
            height: 100vh;
            display: flex;
            justify-content: center;
            align-items: center;
            background-color: #f7f7f7;
        }

        .login-container {
            width: 300px;
            padding: 20px;
            background-color: #fff;
            border-radius: 5px;
            box-shadow: 0 2px 5px rgba(0, 0, 0, 0.1);
        }

        label {
            display: block;
            margin-bottom: 10px;
        }

        input {
            padding: 8px;
            margin-bottom: 15px;
            width: 100%;
            box-sizing: border-box;
        }

        button {
            padding: 10px 20px;
            background-color: #007bff;
            color: white;
            border: none;
            cursor: pointer;
            width: 100%;
        }

        button:hover {
            background-color: #0056b3;
        }

        .error {
            color: red;
        }
    </style>
</head>
<body>
    <div class="login-container">
    <a href="index.html">
            <svg xmlns="http://www.w3.org/2000/svg" x="0px" y="0px" height="30" viewBox="0 0 32 32">
                <path d="M 16 2.59375 L 15.28125 3.28125 L 2.28125 16.28125 L 3.71875 17.71875 L 5 16.4375 L 5 28 L 14 28 L 14 18 L 18 18 L 18 28 L 27 28 L 27 16.4375 L 28.28125 17.71875 L 29.71875 16.28125 L 16.71875 3.28125 Z M 16 5.4375 L 25 14.4375 L 25 26 L 20 26 L 20 16 L 12 16 L 12 26 L 7 26 L 7 14.4375 Z"></path>
            </svg>
        </a>
        <h2>Login</h2>
        <form method="POST" action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>">
            <label for="username">Username:</label>
            <input type="text" id="username" name="username" required>

            <label for="password">Password:</label>
            <input type="password" id="password" name="password" required>

            <button type="submit">Login</button>
        </form>

        <?php if (isset($error)) : ?>
            <p class="error"><?php echo $error; ?></p>
        <?php endif; ?>
    </div>
</body>
</html>
