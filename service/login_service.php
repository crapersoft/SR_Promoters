<?php
include('../database/database.php');
session_start();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = trim($_POST['username']);
    $password = trim($_POST['password']);

    $hardcoded_username = "user";
    $hardcoded_password = "user"; 
    // Check if user is admin with hardcoded password
    if ($username === $hardcoded_username && $password === $hardcoded_password) {
        $_SESSION['userName'] = $hardcoded_username;
        $_SESSION['role'] = 'admin';

        header('Location: ../index.php');
        exit();
    }

 
    $query = 'SELECT * FROM login WHERE userName = ?';
    $stmt = mysqli_prepare($connection, $query);
    mysqli_stmt_bind_param($stmt, 's', $username);
    mysqli_stmt_execute($stmt);
    $result = mysqli_stmt_get_result($stmt);

    if (mysqli_num_rows($result) > 0) {
        $data = mysqli_fetch_assoc($result);

        // Verify password from database
        if (password_verify($password, $data['password'])) {
            $_SESSION['userName'] = $data['userName'];
            $_SESSION['role'] = $data['role'];

            header('Location: ../index.php');
            exit();
        }
    }

    // Authentication failed
    $_SESSION['error'] = 'Invalid username or password';
    header('Location: ../authentication-login.php');
    exit();
}
?>
