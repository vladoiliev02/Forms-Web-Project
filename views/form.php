<?php

$id = (int) $_GET['id'];
if ($id < 1) {
    header('Location: 404.php');
}

require('../utils/db.php');

$query = single_query('
    select f.title as title, q.id as id, q.value as value
    from form as f
    left join question as q on f.id = q.form_id
    where f.id = :form_id',
    ['form_id' => $id]
);

$questions = $query->fetchAll();
$title = $questions[0]['title'];

?>

<!DOCTYPE html>
<html>

<head>
    <title><?= $title ?></title>
    <meta charset="UTF-8">
    <link href="../styles/form.css" rel="stylesheet" />
</head>

<body>
    <section id="title-section">
        <header class="content"><?= $title?></header>
    </section>
    <main class="content">
        <?php foreach($questions as $question) { ?>
            <article>
                <h3><?= $question['value'] ?></h3>
                <div class="button-container">
                    <a href='question.php?id=<?= $question['id'] ?>'>See Answers</a>
                </div>
                <hr />
            </article>
        <?php } ?>
    </main>
</body>

</html>