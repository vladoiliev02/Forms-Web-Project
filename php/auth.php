<?php

session_start();

if ($_SERVER['REQUEST_METHOD'] == 'GET') {
    $userId = $_SESSION["userId"] ?? null;
    $username = $_SESSION["username"] ?? null;
    $email = $_SESSION["email"] ?? null;

    $user =  (isset($userId)) ? array(
        "id" =>  $userId,
        "username" =>  $username,
        "email" => $email
    ) : null;


    echo json_encode($user);
}
