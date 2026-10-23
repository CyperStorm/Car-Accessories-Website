<?php

session_start();

// Connect to database
$conn = new mysqli("localhost", "root", "", "workshopdb");

// Check database connection
if ($conn->connect_error) {
    die("Database connection failed: " . $conn->connect_error);
}

// Check if form was submitted
if ($_SERVER["REQUEST_METHOD"] === "POST") {

    // Get email from form
    $email = trim($_POST["email"]);

    // Check whether email exists
    $stmt = $conn->prepare(
        "SELECT user_id FROM users WHERE email = ?"
    );

    $stmt->bind_param("s", $email);
    $stmt->execute();

    $result = $stmt->get_result();

    if ($result->num_rows === 1) {

        // Store verified email in session
        $_SESSION["reset_email"] = $email;

        // Redirect to reset password page
        header("Location: reset_password.php");
        exit();

    } else {

        $error = "No account was found with that email address.";
    }

    $stmt->close();
}

$conn->close();

?>

<!DOCTYPE html>
<html lang="en">

<head>

    <meta charset="UTF-8">

    <meta name="viewport"
          content="width=device-width, initial-scale=1.0">

    <title>Forgot Password - MR Car Auto Accessories</title>

    <style>

        body {
            background-color: #111;
            color: white;
            font-family: Arial, sans-serif;
            text-align: center;
            padding-top: 100px;
        }

        .message {
            width: 500px;
            max-width: 90%;
            margin: auto;
            padding: 30px;
            background-color: #222;
            border-radius: 10px;
            border-left: 5px solid #ff4d00;
        }

        .error {
            color: #ff4d00;
            margin-bottom: 20px;
        }

        a {
            color: #ff4d00;
            text-decoration: none;
        }

        a:hover {
            text-decoration: underline;
        }

    </style>

</head>

<body>

    <div class="message">

        <?php if (isset($error)): ?>

            <h2>Reset Password</h2>

            <p class="error">
                <?php echo htmlspecialchars($error); ?>
            </p>

            <p>
                <a href="forgot_password.html">
                    Try Again
                </a>
            </p>

        <?php endif; ?>

    </div>

</body>

</html>