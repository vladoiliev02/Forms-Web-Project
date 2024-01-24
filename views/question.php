<?php
    $id = (int) $_GET['id'];
    if ($id < 1) {
        header('Location: 404.php');
    }

    $title = 'This is a very important question that you must definitely answer';
    $answers = [
        ['username' => 'Satan', 'value' => 'Lorem ipsum text bla bla yeay latin is that it'],
        ['username' => 'Bob', 'value' => 'Amidst the swirling mists of an ancient forest, a lone traveler stumbled upon a hidden grove'],
        ['username' => 'Kuche', 'value' => 'Bathed in an ethereal glow, the grove was home to a mystical tree, its branches adorned with shimmering leaves and its trunk pulsating with an otherworldly energy'],
        ['username' => 'Breza', 'value' => 'Overwhelmed by this newfound power, the traveler embarked on a journey of exploration, using their newfound magic to unravel the secrets of the forest'],
    ];
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