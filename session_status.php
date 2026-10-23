<?php
session_start();

header('Content-Type: application/json');

if (!empty($_SESSION['logged_in']) && !empty($_SESSION['user_id'])) {
    echo json_encode([
        'loggedIn' => true,
        'username' => $_SESSION['username'] ?? '',
        'email' => $_SESSION['email'] ?? ''
    ]);
} else {
    echo json_encode([
        'loggedIn' => false
    ]);
}
?>
