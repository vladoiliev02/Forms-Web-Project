<?php

require_once "./db.php";

$db = new DB();

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
    global $db;

    $query = $db->query('
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

function deleteForm($formId) {
    global $db;

    $db->query('
        delete from answear
        where question_id IN (select id from question where form_id = :form_id)',
        ['form_id' => $formId]
    );

    $db->query('
        delete from question
        where form_id = :form_id',
        ['form_id' => $formId]
    );

    $db->query('
        delete from form
        where id = :form_id',
        ['form_id' => $formId]
    );
}

function handleRequest()
{
    switch ($_SERVER['REQUEST_METHOD']) {
        case 'GET':
            handleGetRequest();
            break;
        case 'DELETE':
            handleDeleteRequest();
            break;
        default:
            header('Location /forms/views/404.php');
            break;
    }
}

function handleGetRequest()
{
    if (isset($_GET['userId'])) {
        $userId = $_GET['userId'];
        $forms = getForms($userId);
        header('Content-Type: application/json');
        echo json_encode($forms);
    }
}

function handleDeleteRequest()
{
    if (isset($_GET['formId'])) {
        $formId = $_GET['formId'];
        deleteForm($formId);
    }
}

handleRequest();
