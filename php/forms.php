<?php

require_once("./db.php");

class Form
{
    public $id;
    public $title;
    public $userId;
    public function __construct($id, $title, $userId)
    {
        $this->id = $id;
        $this->title = $title;
        $this->userId = $userId;
    }
}

function getForms($userId)
{
    $query = single_query('
        select id, title, user_id
        from form f
        where user_id = :user_id',
        ['user_id' => $userId]
    );

    $forms = [];
    foreach ($query->fetchAll() as $row) {
        array_push($forms, new Form($row['id'], $row['title'], $row['user_id']));
    }

    return $forms;
}

function handleRequest()
{
    switch ($_SERVER['REQUEST_METHOD']) {
        case 'GET':
            if (isset($_GET['userId'])) {
                $userId = $_GET['userId'];
                handleGetRequest($userId);
                break;
            }
            break;
        default:
            header('Location /forms/views/404.php');
            break;
    }
}

function handleGetRequest($userId)
{
    $forms = getForms($userId);
    header('Content-Type: application/json');
    echo json_encode($forms);
}

handleRequest();
