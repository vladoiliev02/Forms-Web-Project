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
    <title>
        <?= $form->title ?>
    </title>
    <meta charset="UTF-8">
    <link href="../styles/common.css" rel="stylesheet" />
    <link href="../styles/form.css" rel="stylesheet" />
</head>

<body>
    <header>
        <h2><?= $form->title ?></h2>
        <a href="../index.html">
            <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                <path d="M3 9l9-7 9 7v11a2 2 0 0 1-2 2h-5v-7H10v7H5a2 2 0 0 1-2-2z"></path>
                <polyline points="9 22 9 12 15 12 15 22"></polyline>
            </svg>
        </a>
    </header>

    <main id="main-body">
        <?php foreach ($form->questions as $question) { ?>
            <article>
                <h3>
                    <?= $question->value ?>
                </h3>
                <div class="button-container">
                    <a href='question.php?id=<?= $question->id ?>'>See Answers</a>
                </div>
                <hr />
            </article>
        <?php } ?>
    </main>


    <footer id='export-footer'>
        <div>
            <button type="button" id="exportButton">Export</button>
        </div>
        <div>
            <button type="button" id="chartButton">Chart</button>
        </div>

        <script>
            var urlParams = new URLSearchParams(window.location.search);
            var formId = urlParams.get('id');

            document.getElementById('chartButton').addEventListener('click', function() {
                window.location.href = `./chart.php?formId=${formId}`;
            })

            document.getElementById('exportButton').addEventListener('click', function() {

                fetch(`../php/export.php?formId=${formId}`)
                    .then(response => response.blob())
                    .then(blob => {
                        var url = window.URL.createObjectURL(blob);
                        var a = document.createElement('a');
                        a.href = url;
                        a.download = 'answers.csv';
                        document.body.appendChild(a);
                        a.click();
                        a.remove();
                    });
            });
        </script>
    </footer>
</body>

</html>