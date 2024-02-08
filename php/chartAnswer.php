<?php
require_once "../php/db.php";


if (isset($_GET['formId'])) {
    $db = new DB();
    $formId = $_GET['formId'];
    $answersMap = array();

    try {
        $query = $db->query(
            '
            select title 
            from form
            where id = ?',
            [
                $formId
            ]
        );

        $title = $query->fetch()['title'];

        $query = $db->query(
            '
            select q.value as question, a.value as answer, count(a.value) as answerCount
            from answer a
            right join question q on a.question_id = q.id
            where q.form_id = ?
            group by q.value, a.value
            order by a.question_id;',
            [
                $formId
            ]
        );

        while ($row = $query->fetch()) {
            $question = $row['question'];
            $answer = $row['answer'];
            $answerCount = $row['answerCount'];

            if (!isset($answersMap[$question])) {
                $answersMap[$question] = array('answers' => array(), 'answersCount' => array());
            }

            $answersMap[$question]['answers'][] = $answer;
            $answersMap[$question]['answersCount'][] = $answerCount;
        }

        $result = [
            'title' => $title,
            'answers' => $answersMap
        ];

        echo json_encode($result);
    } catch (PDOException $e) {
        echo $e;
    }
}
