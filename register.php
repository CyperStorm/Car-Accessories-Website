<?php
// Connect to the database
$conn = new mysqli("localhost", "root", "", "workshopdb");

// Check connection
if ($conn->connect_error) {
  die("Connection failed: " . $conn->connect_error);
}

// Collect data from form
$username = $_POST['username'];
$email = $_POST['email'];
$password = $_POST['password'];

// Insert into table
$sql = "INSERT INTO users (username, email, password) 
        VALUES ('$username', '$email', '$password')";

if ($conn->query($sql) === TRUE) {
  echo "User registered successfully!";
} else {
  echo "Error: " . $sql . "<br>" . $conn->error;
}

$conn->close();
?>