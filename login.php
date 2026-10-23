<?php
session_start();

$conn = new mysqli("localhost", "root", "", "workshopdb");

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

$username = trim($_POST['username'] ?? '');
$email = trim($_POST['email'] ?? '');
$password = $_POST['password'] ?? '';

if ($username === '' || $email === '' || $password === '') {
    die("Please complete all login fields.");
}

$stmt = $conn->prepare(
    "SELECT user_id, username, email, password
     FROM users
     WHERE username = ? AND email = ?
     LIMIT 1"
);

$stmt->bind_param("ss", $username, $email);
$stmt->execute();

$result = $stmt->get_result();
$user = $result->fetch_assoc();

// 1. Check if user exists first
if (!$user) {
    die("Invalid username, email, or password. <a href='login.html'>Try again</a>");
}

// 2. Check password safely after confirming $user is not null
$passwordMatches = password_verify($password, $user['password']) || ($password === $user['password']);

if (!$passwordMatches) {
    die("Invalid username, email, or password. <a href='login.html'>Try again</a>");
}

/* Create the logged-in session */
session_regenerate_id(true);

$_SESSION['user_id'] = $user['user_id'];
$_SESSION['username'] = $user['username'];
$_SESSION['email'] = $user['email'];
$_SESSION['logged_in'] = true;

$stmt->close();
$conn->close();

/* Send the user back to the home page */
header("Location: index.html");
exit;
?>