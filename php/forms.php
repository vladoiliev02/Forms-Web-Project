<?php

require_once "db.php";

session_start();

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
    public $values;
    public $type;
    public $min;
    public $max;
    public $step;

    public function __construct(
        $id,
        $formId,
        $value,
        $userId,
        $username,
        $values,
        $type,
        $min,
        $max,
        $step,
        $answers = []
    ) {
        $this->id = $id;
        $this->formId = $formId;
        $this->value = $value;
        $this->userId = $userId;
        $this->username = $username;
        $this->answers = $answers;
        $this->values = $values;
        $this->type = $type;
        $this->min = $min;
        $this->max = $max;
        $this->step = $step;
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

function deleteForm($formId, $deleteForm = true, $useTransaction = true)
{
    global $db;

    try {
        if ($useTransaction) {
            $db->beginTransaction();
        }

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

        if ($deleteForm) {
            $db->query('
                delete from form
                where id = :form_id',
                ['form_id' => $formId]
            );
        }

        if ($useTransaction) {
            $db->commit();
        }
    } catch (Exception $e) {
        if ($useTransaction) {
            $db->rollBack();
        }
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
                if (isset($_GET['formId'])) {
                    handlePostRequest($_GET['formId']);
                } else {
                    handlePostRequest();
                }
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

function createForm($form)
{
    global $db;

    $db->query('
        insert into form (title, user_id)
        values (:title, :user_id)',
        ['title' => $form->title, 'user_id' => $form->userId]
    );

    $form->id = $db->lastInsertId();
    return $form;
}

function createQuestion($question)
{
    global $db;

    $encodedValues = array_map('base64_encode', $question->values);
    $concatenatedValues = implode(',', $encodedValues);

    if ($question->id != 0) {
        $db->query('
            update question
            set `value` = :value, `min` = :min, `max` = :max, `step` = :step, `values` = :values, `type` = :type
            where id = :id',
            [
                'value' => $question->value,
                'min' => $question->min,
                'max' => $question->max,
                'step' => $question->step,
                'values' => $concatenatedValues,
                'type' => $question->type,
                'id' => $question->id
            ]
        );
    } else {
        $db->query('
        insert into question (`form_id`, `value`, `min`, `max`, `step`, `values`, `type`)
        values (:form_id, :value, :min, :max, :step, :values, :type)',
            [
                'form_id' => $question->formId,
                'value' => $question->value,
                'min' => $question->min,
                'max' => $question->max,
                'step' => $question->step,
                'values' => $concatenatedValues,
                'type' => $question->type
            ]
        );
    }

    $question->id = $db->lastInsertId();
    return $question;
}

function getForm($formId)
{
    global $db;

    $query = $db->query('
        select f.id as form_id, f.title as form_title,
            f.user_id as form_user_id, q.id as questionId,
            q.form_id as question_form_id, q.value as question_value,
            q.type as question_type, q.min as question_min,
            q.max as question_max, q.step as question_step,
            q.values as question_values
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
        $parts = explode(',', $row["question_values"]);
        $values = [];

        foreach ($parts as $part) {
            array_push($values, base64_decode($part));
        }

        array_push(
            $questions,
            new Question(
                $row['questionId'],
                $row['question_form_id'],
                $row['question_value'],
                $row['form_user_id'],
                '',
                $values,
                $row['question_type'],
                $row['question_min'],
                $row['question_max'],
                $row['question_step'],
                []
            )
        );
    }

    return new Form($results[0]['form_id'], $results[0]['form_title'], $results[0]['form_user_id'], $questions);
}

function getQuestion($questionId)
{
    global $db;

    $query = $db->query('
        select q.id as question_id, q.form_id as question_form_id,
            q.value as question_value,
            q.type as question_type, q.min as question_min,
            q.max as question_max, q.step as question_step,
            q.values as question_values, u.id as user_id,
            u.username as user_username, a.id as answer_id,
            a.value as answer_value
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
        array_push(
            $answers,
            new Answer(
                $row['answer_id'],
                $row['question_id'],
                $row['answer_value'],
                $row['user_id'],
                $row['user_username']
            )
        );
    }

    return new Question(
        $results[0]['question_id'],
        $results[0]['question_form_id'],
        $results[0]['question_value'],
        $results[0]['user_id'],
        $results[0]['user_username'],
        $results[0]['question_values'],
        $results[0]['question_type'],
        $results[0]['question_min'],
        $results[0]['question_max'],
        $results[0]['question_step'],
        $answers
    );
}

function updateForm($form)
{
    global $db;

    $db->query('
        update form
        set title = :title
        where id = :id',
        ['title' => $form->title, 'id' => $form->id]
    );
}

function handlePostRequest($formId = null)
{
    $data = json_decode(file_get_contents('php://input'), true);
    if (!isset($_SESSION['userId'])) {
        http_response_code(401);
        echo json_encode(['error' => 'Invalid request. You are not logged in.']);
        return;
    } elseif (isset($data['title']) && isset($data['questions'])) {
        $userId = $_SESSION['userId'];
        $title = $data['title'];
        $form = new Form(0, $title, $userId, []);

        global $db;
        try {
            $db->beginTransaction();

            if (isset($formId)) {
                $form->id = $formId;
                $existingForm = getForm($formId);

                if (!isset($existingForm)) {
                    $form = createForm($form);
                } elseif ($existingForm->userId != $userId) {
                    http_response_code(400);
                    echo json_encode(['error' => 'Invalid request. This is not your form.']);
                    return;
                } else {
                    updateForm($form);
                }
            } else {
                $form = createForm($form);
            }

            $questions = [];
            foreach ($data['questions'] as $question) {
                if (isset($question['value'])) {

                    $questionObj = new Question(
                        isset($question['id']) ? $question['id'] : 0,
                        $form->id,
                        $question['value'],
                        $userId,
                        '',
                        $question['values'],
                        $question['type'],
                        $question['min'],
                        $question['max'],
                        $question['step'],
                        []
                    );
                    $questionObj = createQuestion($questionObj);
                    array_push($questions, $questionObj);
                }
            }
            $form->questions = $questions;

            $db->commit();

            header('Content-Type: application/json');
            echo json_encode($form);
        } catch (Exception $e) {
            $db->rollBack();
            throw $e;
        }
    } else {
        http_response_code(400);
        echo json_encode(['error' => 'Invalid request']);
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
    if (isset($data) && isset($_SESSION['userId'])) {
        createAnswers($data);
    }
}

function handleGetRequest()
{
    if (isset($_GET['userId'])) {
        $userId = $_GET['userId'];
        if ($userId == $_SESSION['userId']) {
            $forms = getForms($userId);
            header('Content-Type: application/json');
            echo json_encode($forms);
        } else {
            http_response_code(404);
            echo json_encode(['error' => 'Forms not found']);
        }
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
        $form = getForm($formId);
        if ($form->userId == $_SESSION['userId']) {
            deleteForm($formId);
        }
    }
}

handleRequest();
