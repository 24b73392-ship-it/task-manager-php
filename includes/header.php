
<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

$pageTitle = $pageTitle ?? "Task Manager";
$isLoggedIn = !empty($_SESSION["demo_user"]);
?>

<!DOCTYPE html>
<html lang="kk">
<head>
    <meta charset="UTF-8">
    <meta name="viewport"
          content="width=device-width, initial-scale=1.0">

    <title>
        <?= htmlspecialchars($pageTitle, ENT_QUOTES, "UTF-8") ?>
    </title>

    <link rel="stylesheet" href="css/style.css">
</head>
<body>

<header class="site-header">
    <a href="index.php" class="site-logo">
        <span class="logo-icon">✓</span>
        Task Manager
    </a>

    <nav class="site-nav">
        <a href="index.php">Басты бет</a>

        <?php if ($isLoggedIn): ?>

            <a href="tasks.php">Менің тапсырмаларым</a>
            <a href="profile.php">Профиль</a>

            <a href="logout.php" class="nav-logout">
                Шығу
            </a>

        <?php else: ?>

            <a href="login.php">Кіру</a>

            <a href="register.php" class="nav-register">
                Тіркелу
            </a>

        <?php endif; ?>
    </nav>
</header>
