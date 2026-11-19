
<?php
require_once __DIR__ . "/auth.php";

$pageTitle = $pageTitle ?? "Task Manager";
$isLoggedIn = !empty($_SESSION["user_id"]);
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

            <form action="logout.php" method="POST"
                  class="logout-form">

                <input type="hidden"
                       name="csrf_token"
                       value="<?= htmlspecialchars(
                           $_SESSION["csrf_token"],
                           ENT_QUOTES,
                           "UTF-8"
                       ) ?>">

                <button type="submit" class="nav-logout">
                    Шығу
                </button>
            </form>

        <?php else: ?>

            <a href="login.php">Кіру</a>

            <a href="register.php" class="nav-register">
                Тіркелу
            </a>

        <?php endif; ?>
    </nav>

</header>
