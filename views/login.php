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
    <link href="../styles/auth.css" rel="stylesheet" />
</head>

<body>
    <header>
        <h1>Login</h1>
        <a href="../index.html">
            <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"
                stroke-linecap="round" stroke-linejoin="round">
                <path d="M3 9l9-7 9 7v11a2 2 0 0 1-2 2h-5v-7H10v7H5a2 2 0 0 1-2-2z"></path>
                <polyline points="9 22 9 12 15 12 15 22"></polyline>
            </svg>
        </a>
    </header>

    <form id="login-form" action="login.php" method="POST">
        <label for="username">Username:</label>
        <input type="text" id="username" name="username" required minlength="3">

        <label for="password">Password:</label>
        <input type="password" id="password" name="password" required minlength="5"
            pattern="^(?=.*[A-Za-z])(?=.*\d)[A-Za-z\d]+$">

        <button type="submit" name="login"> Login </button>
    </form>
</body>

</html>