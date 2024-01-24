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
    <link href="../styles/auth.css" rel="stylesheet" />
</head>

<body>
    <section id='title-section'>
        <section id='title-subsection'>
            <header>
                Register
            </header>

            <a href="../index.html">
                <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                    <path d="M3 9l9-7 9 7v11a2 2 0 0 1-2 2h-5v-7H10v7H5a2 2 0 0 1-2-2z"></path>
                    <polyline points="9 22 9 12 15 12 15 22"></polyline>
                </svg>
            </a>
        </section>
    </section>
    <form id="register-form" action="register.php" method="POST">
        <label for="username">Username:</label>
        <input type="text" id="username" name="username" required minlength="3">

        <label for="email">Email:</label>
        <input type="email" id="email" name="email" required>

        <label for="password">Password:</label>
        <input type="password" id="password" name="password" required minlength="5" pattern="^(?=.*[A-Za-z])(?=.*\d)[A-Za-z\d]+$">

        <button type="submit" name="register">Register</button>
    </form>
</body>

</html>