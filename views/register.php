<?php

require_once "../php/db.php";
session_start();

$db = new DB();

$username = "";
$email = "";
$password = "";

if (isset($_POST['register'])) {
    $username = $_POST["username"];
    $email = $_POST["email"];
    $password = $_POST["password"];
    $hashPassword = password_hash($password, PASSWORD_DEFAULT);

    try {
        $db->query(
            '
        INSERT INTO user (username, email, password) 
        VALUES (?, ?, ?);',
            [
                $username,
                $email,
                $hashPassword
            ]
        );

        $userId = $db->lastInsertId();
        $_SESSION["userId"] = $userId;
        $_SESSION["username"] = $username;
        $_SESSION["email"] = $email;

        header('Location: ../index.html');
    } catch (PDOException $e) {
        echo $e;
    }
}

?>

<!DOCTYPE html>
<html>

<head>
    <title>Register Form</title>
    <meta charset="UTF-8">
    <link href="../styles/common.css" rel="stylesheet" />
</head>

<body>
    <header>
        <h1>Register</h1>
    </header>
    <form id="registerForm" action="register.php" method="POST">
        <label for="username">Username:</label>
        <input type="text" id="username" name="username" required minlength="3">

        <label for="email">Email:</label>
        <input type="email" id="email" name="email" required>

        <label for="password">Password:</label>
        <input type="password" id="password" name="password" required minlength="5" pattern="^(?=.*[A-Za-z])(?=.*\d)[A-Za-z\d]+$">

        <input type="submit" class="registerButton" name="register">
    </form>
</body>

</html>