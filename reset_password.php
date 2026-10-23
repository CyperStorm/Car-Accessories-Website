<?php

session_start();

// Check whether user has verified their email
if (!isset($_SESSION["reset_email"])) {
    header("Location: forgot_password.html");
    exit();
}

// Get email from session
$email = $_SESSION["reset_email"];

// Connect to database
$conn = new mysqli("localhost", "root", "", "workshopdb");

// Check connection
if ($conn->connect_error) {
    die("Database connection failed: " . $conn->connect_error);
}

$message = "";
$error = "";

// Check whether password form was submitted
if ($_SERVER["REQUEST_METHOD"] === "POST") {

    $password = $_POST["password"];
    $confirm_password = $_POST["confirm_password"];

    // Check password length
    if (strlen($password) < 8) {

        $error = "Password must be at least 8 characters.";

    } elseif ($password !== $confirm_password) {

        $error = "Passwords do not match.";

    } else {

        // Update password as plaintext
$stmt = $conn->prepare(
    "UPDATE users SET password = ? WHERE email = ?"
);

$stmt->bind_param(
    "ss",
    $password,
    $email
);
        if ($stmt->execute()) {

            // Remove reset session
            unset($_SESSION["reset_email"]);

            // Redirect to login
            header("Location: login.html?reset=success");
            exit();

        } else {

            $error = "Unable to reset password. Please try again.";
        }

        $stmt->close();
    }
}

$conn->close();

?>

<!DOCTYPE html>
<html lang="en">

<head>

    <meta charset="UTF-8">

    <meta name="viewport"
          content="width=device-width, initial-scale=1.0">

    <title>Set New Password - MR Car Auto Accessories</title>

    <style>

        body {
            background-color: #111;
            color: white;
            font-family: Arial, sans-serif;
            text-align: center;
            padding-top: 80px;
        }

        .card {
            width: 450px;
            max-width: 90%;
            margin: auto;
            padding: 35px;
            background-color: #222;
            border-left: 5px solid #ff4d00;
            border-radius: 10px;
        }

        h1 {
            margin-bottom: 10px;
        }

        .email {
            color: #999;
            margin-bottom: 25px;
        }

        label {
            display: block;
            text-align: left;
            margin-bottom: 8px;
        }

        input {
            width: 100%;
            padding: 12px;
            margin-bottom: 20px;
            box-sizing: border-box;
            background-color: #333;
            border: 1px solid #555;
            border-radius: 5px;
            color: white;
        }

        button {
            width: 100%;
            padding: 13px;
            background-color: #ff4d00;
            border: none;
            border-radius: 5px;
            color: white;
            font-size: 16px;
            cursor: pointer;
        }

        button:hover {
            background-color: #e64500;
        }

        .error {
            color: #ff4d00;
            margin-bottom: 20px;
        }

    </style>

</head>

<body>

    <div class="card">

        <h1>Set New Password</h1>

        <p class="email">
            Account: <?php echo htmlspecialchars($email); ?>
        </p>

        <?php if ($error): ?>

            <p class="error">
                <?php echo htmlspecialchars($error); ?>
            </p>

        <?php endif; ?>

        <form method="POST">

            <label for="password">
                New Password
            </label>

            <input
                type="password"
                id="password"
                name="password"
                placeholder="Enter new password"
                minlength="8"
                required>

            <label for="confirm_password">
                Confirm New Password
            </label>

            <input
                type="password"
                id="confirm_password"
                name="confirm_password"
                placeholder="Confirm new password"
                minlength="8"
                required>

            <button type="submit">
                Reset Password
            </button>

        </form>

    </div>

</body>

</html>