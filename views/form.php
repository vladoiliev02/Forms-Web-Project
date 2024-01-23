<?php
    $id = (int) $_GET['id'];
    if ($id < 1) {
        header('Location: 404.php');
    }

    $title = 'Form about some important stuff I forgot about';
    $questions = [
        ['id' => 1, 'value' => 'Lorem ipsum text bla bla yeay latin is that it'],
        ['id' => 2, 'value' => 'Amidst the swirling mists of an ancient forest, a lone traveler stumbled upon a hidden grove'],
        ['id' => 3, 'value' => 'Bathed in an ethereal glow, the grove was home to a mystical tree, its branches adorned with shimmering leaves and its trunk pulsating with an otherworldly energy'],
        ['id' => 4, 'value' => 'Overwhelmed by this newfound power, the traveler embarked on a journey of exploration, using their newfound magic to unravel the secrets of the forest'],
    ];
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