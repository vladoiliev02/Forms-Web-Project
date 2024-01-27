<?php

require_once "db.php";

session_start();

$db = new DB();

if (isset($_GET['formId'])) {
  $formId = $_GET['formId'];

  $query = $db->query('
      SELECT a.id as id,
        a.user_id as user_id,
        q.value as \'question_value\',
        a.value as \'answer_value\'
      FROM answer a
      JOIN question q ON a.question_id = q.id
      WHERE q.form_id = :form_id
    ', ['form_id' => $formId]);

    $answers = $query->fetchAll(PDO::FETCH_ASSOC);

    if (count($answers) === 0) {
      echo "This form has no answers yet.";
      exit;
    }
    
    header('Content-Type: application/csv');
    header('Content-Disposition: attachment; filename=answers.csv');
    header('Pragma: no-cache');
    
    $file = fopen('php://output', 'w');
    
    fputcsv($file, array_keys($answers[0]));
    
    foreach ($answers as $answer) {
      fputcsv($file, $answer);
    }
    
    fclose($file);
}