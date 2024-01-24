<?php

require_once "./db.php";

$db = new DB();

class Question
{
    public $id;
    public $formId;
    public $value;
    public function __construct($id, $formId, $value)
    {
        $this->id = $id;
        $this->formId = $formId;
        $this->value = $value;
    }
}

class Form
{
    public $id;
    public $title;
    public $userId;
    public $questions;
    public function __construct($id, $title, $userId, $questions = [])
    {
        $this->id = $id;
        $this->title = $title;
        $this->userId = $userId;
        $this->questions = $questions;
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
        array_push($forms, new Form($row['id'], $row['title'], $row['user_id'], []));
    }

    return $forms;
}

function deleteForm($formId)
{
    global $db;

    try {
        $db->beginTransaction();

        $db->query('
        delete from answer
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

        $db->commit();
    } catch (Exception $e) {
        $db->rollBack();
        throw $e;
    }
}

function handleRequest()
{
    try {
        switch ($_SERVER['REQUEST_METHOD']) {
            case 'GET':
                handleGetRequest();
                break;
            case 'POST':
                handlePostRequest();
                break;
            case 'DELETE':
                handleDeleteRequest();
                break;
            case 'PATCH':
                handlePatchRequest();
                break;
            default:
                header('Location /forms/views/404.php');
                break;
        }
    } catch (Exception $e) {
        http_response_code(400);
        echo json_encode(['error' => $e->getMessage()]);
    }
}

function createForm($title, $userId)
{
    global $db;

    $db->query('
        insert into form (title, user_id)
        values (:title, :user_id)',
        ['title' => $title, 'user_id' => $userId]
    );

    return new Form($db->lastInsertId(), $title, $userId, []);
}

function createQuestion($formId, $value)
{
    global $db;

    $db->query('
        insert into question (form_id, value)
        values (:form_id, :value)',
        ['form_id' => $formId, 'value' => $value]
    );

    return new Question($db->lastInsertId(), $formId, $value);
}

function getForm($formId)
{
    global $db;

    $query = $db->query('
        select id, title, user_id
        from form
        where id = :form_id',
        ['form_id' => $formId]
    );

    $form = $query->fetch();
    if (!$form) {
        return null;
    }

    $form = new Form($form['id'], $form['title'], $form['user_id'], []);

    $query = $db->query('
        select id, form_id, value
        from question
        where form_id = :form_id',
        ['form_id' => $formId]
    );

    foreach ($query->fetchAll() as $row) {
        array_push($form->questions, new Question($row['id'], $row['form_id'], $row['value']));
    }

    return $form;
}

function handlePostRequest()
{
    $data = json_decode(file_get_contents('php://input'), true);
    if (isset($data['userId']) && isset($data['title']) && isset($data['questions'])) {
        $userId = $data['userId'];
        $title = $data['title'];

        global $db;

        try {
            $db->beginTransaction();

            $form = createForm($title, $userId);

            $questions = [];
            foreach ($data['questions'] as $question) {
                if (isset($question['value'])) {
                    $question = createQuestion($form->id, $question['value']);
                    array_push($questions, $question);
                }
            }
            $form->questions = $questions;

            $db->commit();
        } catch (Exception $e) {
            $db->rollBack();
            throw $e;
        }

        header('Content-Type: application/json');
        echo json_encode($form);
    }
}

function createAnswers($answers)
{
    global $db;

    
    try {
        $db->beginTransaction();

        $stmt = $db->prepare('
            insert into answer (question_id, user_id, value)
            values (:question_id, :user_id, :value)
        ');

        foreach ($answers as $answer) {
            $stmt->execute([
                'question_id' => $answer['questionId'],
                'user_id' => $answer['userId'],
                'value' => $answer['value'],
            ]);
        }

        $db->commit();
    } catch (Exception $e) {
        $db->rollBack();
        throw $e;
    }
}

function handlePatchRequest()
{
    $data = json_decode(file_get_contents('php://input'), true);
    if (isset($data)) {
        createAnswers($data);
    }
}

function handleGetRequest()
{
    if (isset($_GET['userId'])) {
        $userId = $_GET['userId'];
        $forms = getForms($userId);
        header('Content-Type: application/json');
        echo json_encode($forms);
    } elseif (isset($_GET['formId'])) {
        $formId = $_GET['formId'];
        $form = getForm($formId);
        if ($form != null) {
            header('Content-Type: application/json');
            echo json_encode($form);
        } else {
            http_response_code(404);
            echo json_encode(['error' => 'Form not found']);
        }
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
