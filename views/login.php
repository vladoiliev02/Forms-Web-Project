<?php

require_once "../php/db.php";
session_start();

$db = new DB();

$username = "";
$email = "";
$password = "";

if (isset($_POST['login'])) {
    $username = $_POST["username"];
    $password = $_POST["password"];

    try {
        $query = $db->query(
            '
        SELECT id, username, email, password FROM user 
        where username = ?',
            [
                $username
            ]
        );

        $user = $query->fetch();

        if (!empty($user)) {
            if (password_verify($password, $user['password'])) {
                $_SESSION["username"] = $username;
                $_SESSION["userId"] = $user['id'];
                $_SESSION["email"] = $user['email'];

                header("Location: ../index.html");
            } else {
                echo "Wrong password";
            }
        }
    } catch (PDOException $e) {
        echo $e;
    }
}
?>

<!DOCTYPE html>
<html>

<head>
    <title>Login</title>
    <meta charset="UTF-8">
    <link href="../styles/common.css" rel="stylesheet" />
</head>

<body>
    <header>
        <h1>Login</h1>
    </header>
    <form id="loginForm" action="login.php" method="POST">
        <label for="username">Username:</label>
        <input type="text" id="username" name="username" required minlength="3">

        <label for="password">Password:</label>
        <input type="password" id="password" name="password" required minlength="5" pattern="^(?=.*[A-Za-z])(?=.*\d)[A-Za-z\d]+$">

        <input type="submit" class="loginButton" name="login">
    </form>
</body>

</html>