<!DOCTYPE html>
<html>

<head>
    <title>
        <?= $title ?>
    </title>
    <meta charset="UTF-8">
    <link href="../styles/common.css" rel="stylesheet" />
    <link href="../styles/404.css" rel="stylesheet" />
</head>

<body>
    <main>
        <h1>404</h1>
        <a href="../index.html">
            <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"
                stroke-linecap="round" stroke-linejoin="round">
                <path d="M3 9l9-7 9 7v11a2 2 0 0 1-2 2h-5v-7H10v7H5a2 2 0 0 1-2-2z"></path>
                <polyline points="9 22 9 12 15 12 15 22"></polyline>
            </svg>
        </a>
        <a href="./logout.php" id="logout-button">Logout</a>
    </main>
</body>

</html>