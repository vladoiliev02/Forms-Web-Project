<?php

function single_query($sql, $values) {
    $host = 'localhost';
    $name = 'forms';
    $username = 'root';
    $password = '';

    $db = new PDO("mysql:host=$host;dbname=$name", $username, $password, [
        PDO::MYSQL_ATTR_INIT_COMMAND => 'SET NAMES utf8',
        PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
    ]);

    $query = $db->prepare($sql);
    $query->execute($values);
    return $query;
}

?>