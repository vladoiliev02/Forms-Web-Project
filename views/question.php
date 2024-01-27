<?php

require_once '../php/redirect.php';

$questionId = (int) $_GET['id'];
if ($questionId < 1) {
    redirectNotFound();
}

require_once '../php/forms.php';

$question = getQuestion($questionId);
if (!$question) {
    redirectNotFound();
}

?>

<!DOCTYPE html>
<html>

<head>
    <title>
        <?= $question->value ?>
    </title>
    <meta charset="UTF-8">
    <link href="../styles/common.css" rel="stylesheet" />
    <link href="../styles/question.css" rel="stylesheet" />
</head>

<body>
    <header>
        <h2>
            <?= $question->value ?>
        </h2>
        <a href="../index.html">
            <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"
                stroke-linecap="round" stroke-linejoin="round">
                <path d="M3 9l9-7 9 7v11a2 2 0 0 1-2 2h-5v-7H10v7H5a2 2 0 0 1-2-2z"></path>
                <polyline points="9 22 9 12 15 12 15 22"></polyline>
            </svg>
        </a>
    </header>

    <main id="main-body">
        <?php foreach ($question->answers as $answer) { ?>
            <article class="answer">
                <p>
                    <?= $answer->username ?>:
                </p>
                <h3>
                    <?= $answer->value ?>
                </h3>
                <hr />
            </article>
        <?php } ?>
    </main>
</body>

</html>