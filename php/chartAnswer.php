<?php
require_once "../php/db.php";


if (isset($_GET['formId'])) {
    $db = new DB();
    $formId = $_GET['formId'];
    $answersMap = array();

    try {
        $query = $db->query(
            '
            SELECT q.value as question, a.value as answer, COUNT(a.value) as answerCount
            FROM answer a
            RIGHT JOIN question q on a.question_id = q.id
            where q.form_id = ?
            GROUP by q.value, a.value
            order by a.question_id;',
            [
                $formId
            ]
        );

        while ($row = $query->fetch(PDO::FETCH_ASSOC)) {
            $question = $row['question'];
            $answer = $row['answer'];
            $answerCount = $row['answerCount'];

            if (!isset($answersMap[$question])) {
                $answersMap[$question] = array('answers' => array(), 'answersCount' => array());
            }

            $answersMap[$question]['answers'][] = $answer;
            $answersMap[$question]['answersCount'][] = $answerCount;
        }

        echo json_encode($answersMap);
    } catch (PDOException $e) {
        echo $e;
    }
}
