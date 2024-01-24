<?php

require_once '../php/redirect.php';

$formId = (int) $_GET['id'];
if ($formId < 1) {
    redirectNotFound();
}

require_once '../php/forms.php';

$form = getForm($formId);
if (!$form) {
    redirectNotFound();
}

?>

<!DOCTYPE html>
<html>

<head>
    <title><?= $form->title ?></title>
    <meta charset="UTF-8">
    <link href="../styles/common.css" rel="stylesheet" />
    <link href="../styles/form.css" rel="stylesheet" />
</head>

<body>
    <section id="title-section">
        <header class="content"><?= $form->title ?></header>
    </section>
    <main class="content">
        <?php foreach($form->questions as $question) { ?>
            <article>
                <h3><?= $question->value ?></h3>
                <div class="button-container">
                    <a href='question.php?id=<?= $question->id ?>'>See Answers</a>
                </div>
                <hr />
            </article>
        <?php } ?>
    </main>
</body>

</html>