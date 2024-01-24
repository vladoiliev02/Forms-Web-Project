<?php

require_once "db.php";

$db = new DB();

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

class Question
{
    public $id;
    public $formId;
    public $value;
    public $userId;
    public $username;
    public $answers;
    public function __construct($id, $formId, $value, $userId, $username, $answers = [])
    {
        $this->id = $id;
        $this->formId = $formId;
        $this->value = $value;
        $this->userId = $userId;
        $this->username = $username;
        $this->answers = $answers;
    }
}

class Answer
{
    public $id;
    public $questionId;
    public $value;
    public $userId;
    public $username;
    public function __construct($id, $questionId, $value, $userId, $username)
    {
        $this->id = $id;
        $this->questionId = $questionId;
        $this->value = $value;
        $this->userId = $userId;
        $this->username = $username;
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

    return new Question($db->lastInsertId(), $formId, $value, 0, '', []);
}

function getForm($formId)
{
    global $db;

    $query = $db->query('
        select f.id as form_id, f.title as form_title, f.user_id as form_user_id, q.id as questionId, q.form_id as question_form_id, q.value as question_value
        from form as f
        left join question as q on f.id = q.form_id
        where f.id = :form_id',
        ['form_id' => $formId]
    );

    $results = $query->fetchAll();
    if (!$results) {    
        return null;
    }

    $questions = [];
    foreach ($results as $row) {
        array_push($questions, new Question($row['questionId'], $row['question_form_id'], $row['question_value'], '', '', []));
    }

    return new Form($results[0]['form_id'], $results[0]['form_title'], $results[0]['form_user_id'], $questions);
}

function getQuestion($questionId)
{
    global $db;

    $query = $db->query('
        select q.id as question_id, q.form_id as question_form_id, q.value as question_value, u.id as user_id, u.username as user_username, a.id as answer_id, a.value as answer_value
        from question as q
        left join answer as a on q.id = a.question_id
        left join user as u on a.user_id = u.id
        where q.id = :question_id',
        ['question_id' => $questionId]
    );

    $results = $query->fetchAll();
    if (!$results) {
        return null;
    }

    $answers = [];
    foreach ($results as $row) {
        array_push($answers, new Answer($row['answer_id'], $row['question_id'], $row['answer_value'], $row['user_id'], $row['user_username']));
    }

    return new Question($results[0]['question_id'], $results[0]['question_form_id'], $results[0]['question_value'], '', '', $answers);
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
