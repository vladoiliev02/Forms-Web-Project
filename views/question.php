<?php

require('../php/redirect.php');

$questionId = (int) $_GET['id'];
if ($questionId < 1) {
    redirectNotFound();
}

require('../php/forms.php');

$question = getQuestion($formId);
if (!$question) {
    redirectNotFound();
}

?>

<!DOCTYPE html>
<html>

<head>
    <title><?= $title ?></title>
    <meta charset="UTF-8">
    <link href="../styles/common.css" rel="stylesheet" />
    <link href="../styles/question.css" rel="stylesheet" />
</head>

<body>
    <section id="title-section">
        <header class="content"><?= $title ?></header>
    </section>
    <main class="content">
        <?php foreach($question->answers as $answer) { ?>
            <article>
                <p><?= $answer->username ?>:</p>
                <h3><?= $answer->value ?></h3>
                <hr />
            </article>
        <?php } ?>
    </main>
</body>

</html>