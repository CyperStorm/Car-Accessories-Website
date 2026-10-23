<?php
// Enable error display for debugging
ini_set('display_errors', 1);
error_reporting(E_ALL);

session_start();

$successMsg = "";
$errorMsg   = "";

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $firstName = trim($_POST['first_name'] ?? '');
    $lastName  = trim($_POST['last_name'] ?? '');
    $email     = trim($_POST['email'] ?? '');
    $message   = trim($_POST['message'] ?? '');

    if (empty($firstName) || empty($lastName) || empty($email) || empty($message)) {
        $errorMsg = "Please fill in all required fields.";
    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $errorMsg = "Please enter a valid email address.";
    } else {
        // Connect to MySQL (Host, User, Password, Database Name)
        $conn = new mysqli("localhost", "root", "", "workshopdb");

        if ($conn->connect_error) {
            $errorMsg = "Database Connection Failed: " . $conn->connect_error;
        } else {
            $stmt = $conn->prepare("INSERT INTO feedback (first_name, last_name, email, message) VALUES (?, ?, ?, ?)");
            
            if (!$stmt) {
                $errorMsg = "SQL Prepare Failed: " . $conn->error;
            } else {
                $stmt->bind_param("ssss", $firstName, $lastName, $email, $message);

                if ($stmt->execute()) {
                    $successMsg = "Thank you! Your message has been sent successfully.";
                } else {
                    $errorMsg = "SQL Execution Error: " . $stmt->error;
                }
                $stmt->close();
            }
            $conn->close();
        }
    }
}
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <title>Contact Us - MR Car Auto Accessories</title>

    <!-- Google Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;500;600;700;800&display=swap" rel="stylesheet">

    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            background-image:
                linear-gradient(rgba(0, 0, 0, 0.78), rgba(0, 0, 0, 0.78)),
                url("img/white_background.jpg");
            background-repeat: no-repeat;
            background-position: center;
            background-size: cover;
            background-attachment: fixed;
            font-family: 'Poppins', sans-serif;
            color: white;
            line-height: 1.7;
            min-height: 100vh;
            display: flex;
            flex-direction: column;
        }

        /* Navigation Bar */
        nav {
            background-color: #111;
            min-height: 70px;
            display: flex;
            align-items: center;
            justify-content: space-between;
            padding: 12px 8%;
            position: sticky;
            top: 0;
            z-index: 1000;
            border-bottom: 1px solid #333;
        }

        .logo {
            color: white;
            font-size: 25px;
            font-weight: 800;
            text-decoration: none;
        }

        .logo span {
            color: #ff4d00;
        }

        nav ul {
            list-style: none;
            display: flex;
            gap: 30px;
        }

        nav ul li a {
            color: white;
            text-decoration: none;
            font-size: 15px;
            transition: 0.3s;
        }

        nav ul li a:hover {
            color: #ff4d00;
        }

        .nav-account {
            display: flex;
            align-items: center;
            gap: 12px;
        }

        .login-btn, .logout-btn {
            background-color: transparent;
            color: white;
            border: 2px solid #ff4d00;
            padding: 8px 22px;
            border-radius: 5px;
            font-size: 15px;
            font-weight: 600;
            cursor: pointer;
            transition: all .3s ease;
        }

        .login-btn:hover, .logout-btn:hover {
            background-color: #ff4d00;
            color: #111;
            transform: translateY(-2px);
        }

        .profile-link {
            width: 42px;
            height: 42px;
            border: 2px solid #ff4d00;
            border-radius: 50%;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            color: white;
            text-decoration: none;
            font-size: 22px;
            transition: all .3s ease;
        }

        .profile-link:hover {
            background: #ff4d00;
            color: #111;
        }

        /* Page Title */
        .page-title {
            text-align: center;
            font-size: 55px;
            margin-bottom: 40px;
            font-family: 'Poppins', sans-serif;
            color: white;
            text-shadow: 0 0 15px rgba(255, 77, 0, 0.5);
        }

        .page-title span {
            color: #ff4d00;
        }

        /* Main Container */
        .container {
            max-width: 1300px;
            width: 100%;
            margin: 0 auto;
            padding: 50px 30px 80px;
            flex: 1;
        }

        /* Split Card Layout */
        .contact-card {
            background-color: #1a1a1a;
            border-radius: 12px;
            border: 1px solid #222222;
            box-shadow: 0 8px 30px rgba(0, 0, 0, 0.5);
            display: flex;
            flex-wrap: wrap;
            overflow: hidden;
            min-height: 550px;
        }

        /* Left Side: Information */
        .contact-info {
            flex: 1 1 360px;
            background-color: #161616;
            padding: 50px;
            border-right: 1px solid #222222;
            display: flex;
            flex-direction: column;
            gap: 32px;
        }

        .contact-info h2 {
            font-size: 22px;
            color: #ff5500;
            margin-bottom: 8px;
        }

        .info-block h3 {
            font-size: 12px;
            text-transform: uppercase;
            letter-spacing: 1px;
            color: #888888;
            margin-bottom: 4px;
        }

        .info-block p {
            color: #dddddd;
            font-size: 15px;
            line-height: 1.5;
        }

        /* Right Side: Form */
        .contact-form-container {
            flex: 2 1 550px;
            padding: 50px;
        }

        .contact-form-container h2 {
            font-size: 22px;
            color: #ffffff;
            margin-bottom: 24px;
        }

        .contact-form {
            display: flex;
            flex-direction: column;
            gap: 18px;
        }

        .form-row {
            display: flex;
            gap: 16px;
        }

        .form-group {
            display: flex;
            flex-direction: column;
            gap: 6px;
            flex: 1;
        }

        .form-group label {
            font-size: 13px;
            color: #aaaaaa;
        }

        .form-group input,
        .form-group textarea {
            width: 100%;
            padding: 12px 14px;
            background-color: #111111;
            border: 1px solid #333333;
            border-radius: 6px;
            color: #ffffff;
            font-size: 14px;
            outline: none;
            transition: border-color 0.2s;
            font-family: 'Poppins', sans-serif;
        }

        .form-group input:focus,
        .form-group textarea:focus {
            border-color: #ff5500;
        }

        .btn-submit {
            align-self: flex-start;
            background-color: #ff5500;
            color: #ffffff;
            padding: 12px 36px;
            border: none;
            border-radius: 6px;
            font-size: 14px;
            font-weight: bold;
            cursor: pointer;
            margin-top: 10px;
            transition: background-color 0.2s;
        }

        .btn-submit:hover {
            background-color: #e04a00;
        }

        /* Alert Messages */
        .alert {
            padding: 12px 16px;
            border-radius: 6px;
            font-size: 14px;
            margin-bottom: 18px;
        }

        .alert-success {
            background-color: rgba(40, 167, 69, 0.2);
            border: 1px solid #28a745;
            color: #28a745;
        }

        .alert-danger {
            background-color: rgba(220, 53, 69, 0.2);
            border: 1px solid #dc3545;
            color: #dc3545;
        }

        /* Footer */
        footer {
            text-align: center;
            padding: 24px 20px;
            background-color: #000000;
            color: #666666;
            font-size: 13px;
            border-top: 1px solid #222222;
        }

        @media (max-width: 700px) {
            .form-row {
                flex-direction: column;
            }
            .page-title {
                font-size: 40px;
            }
        }
    </style>
</head>

<body>

    <header>
        <nav>
            <a class="logo" href="index.html">MR Car<span> Auto</span></a>

            <ul>
                <li><a href="index.html">Home</a></li>
                <li><a href="purchase.html">Purchase</a></li>
                <li><a href="about.html">About Us</a></li>
                <li><a href="contact.php">Contact</a></li>
            </ul>

            <div class="nav-account">
                <button id="loginButton" class="login-btn" onclick="window.location.href='login.html'">Login</button>
                <a id="profileButton" class="profile-link" href="profile.php" title="Profile" style="display:none;">👤</a>
                <button id="logoutButton" class="logout-btn" type="button" style="display:none;" onclick="logoutUser()">Logout</button>
            </div>
        </nav>
    </header>

    <main class="container">
        <h1 class="page-title">Contact <span>Us</span></h1>

        <div class="contact-card">
            <!-- Left Column: Details -->
            <div class="contact-info">
                <h2>Contact Information</h2>

                <div class="info-block">
                    <h3>Phone</h3>
                    <p>+60 12-345 6789</p>
                </div>

                <div class="info-block">
                    <h3>Email</h3>
                    <p>info@mrcarauto.com</p>
                </div>

                <div class="info-block">
                    <h3>Office Location</h3>
                    <p>123 Auto Park Road,<br>30000 Ipoh, Perak, Malaysia</p>
                </div>

                <div class="info-block">
                    <h3>Business Hours</h3>
                    <p>Mon - Sat: 9:00 AM - 7:00 PM<br>Sun: Closed</p>
                </div>
            </div>

            <!-- Right Column: Form -->
            <div class="contact-form-container">
                <h2>Ask us anything</h2>

                <?php if (!empty($successMsg)): ?>
                    <div class="alert alert-success"><?= htmlspecialchars($successMsg) ?></div>
                <?php endif; ?>

                <?php if (!empty($errorMsg)): ?>
                    <div class="alert alert-danger"><?= htmlspecialchars($errorMsg) ?></div>
                <?php endif; ?>

                <form class="contact-form" action="contact.php" method="POST">
                    <div class="form-row">
                        <div class="form-group">
                            <label for="first_name">First name</label>
                            <input type="text" id="first_name" name="first_name" required>
                        </div>
                        <div class="form-group">
                            <label for="last_name">Last name</label>
                            <input type="text" id="last_name" name="last_name" required>
                        </div>
                    </div>

                    <div class="form-group">
                        <label for="email">Email</label>
                        <input type="email" id="email" name="email" required>
                    </div>

                    <div class="form-group">
                        <label for="message">How can we help?</label>
                        <textarea id="message" name="message" rows="4" required></textarea>
                    </div>

                    <button type="submit" class="btn-submit">Submit</button>
                </form>
            </div>
        </div>
    </main>

    <footer>
        &copy; 2026 MR Car Auto Accessories. All Rights Reserved.
    </footer>

    <script src="auth_nav.js"></script>

</body>

</html>