<?php

$id = (int) $_GET['id'];
if ($id < 1) {
    header('Location: 404.php');
}

require('../utils/db.php');

$query = single_query('
    select q.value as title, u.username as username, a.value as value
    from question as q
    left join answer as a on q.id = a.question_id
    left join user as u on a.user_id = u.id
    where q.id = :question_id',
    ['question_id' => $id]
);

$answers = $query->fetchAll();
$title = $answers[0]['title'];

?>

<!DOCTYPE html>
<html>

<head>
    <title><?= $title ?></title>
    <meta charset="UTF-8">
    <link href="../styles/question.css" rel="stylesheet" />
</head>

<body>
    <section id="title-section">
        <header class="content"><?= $title?></header>
    </section>
    <main class="content">
        <?php foreach($answers as $answer) { ?>
            <article>
                <p><?= $answer['username'] ?>:</p>
                <h3><?= $answer['value'] ?></h3>
                <hr />
            </article>
        <?php } ?>
    </main>
</body>

</html>