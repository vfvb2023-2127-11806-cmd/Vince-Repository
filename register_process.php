<?php
session_start();
include_once "conn.php";

// Check if form is submitted
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Get and sanitize form inputs
    $firstName = trim($_POST['firstname']);
    $lastName = trim($_POST['lastname']);
    $email = trim($_POST['email']);
    $address = trim($_POST['address']);
    $password = trim($_POST['password']);
    $confirm_password = trim($_POST['confirm_password']);

    // Basic validation
    if ($password !== $confirm_password) {
        $_SESSION['error'] = "Passwords do not match.";
        header("Location: register.php");
        exit();
    }

    // Hash the password
    $hashedPassword = password_hash($password, PASSWORD_DEFAULT);
    $isAdmin = 0;
    $isActive = 1;
    // Prepare and execute insert query
    $sql = "INSERT INTO users (firstName, lastName, email, password, address, isAdmin, isActive) 
            VALUES (?, ?, ?, ?, ?, ?, ?)";
    $stmt = $conn->prepare($sql);
    if ($stmt) {
        $stmt->bind_param("sssssii", $firstName, $lastName, $email, $hashedPassword, $address, $isAdmin, $isActive);
        if ($stmt->execute()) {
            $_SESSION['success'] = "Registration successful! You can now log in.";
            header("Location: login.php");
            exit();
        } else {
            $_SESSION['error'] = "Database error: " . $stmt->error;
            header("Location: register.php");
            exit();
        }
    } else {
        $_SESSION['error'] = "Database error: " . $conn->error;
        header("Location: register.php");
        exit();
    }
} else {
    header("Location: register.php");
    exit();
}
