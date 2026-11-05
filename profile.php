
<?php
session_start();

if (empty($_SESSION["demo_user"])) {
    header("Location: login.php");
    exit;
}

$pageTitle = "Менің профилім | Task Manager";
$email = $_SESSION["demo_user"];

if (empty($_SESSION["demo_name"])) {
    $_SESSION["demo_name"] = explode("@", $email)[0];
}

$error = "";
$success = "";

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $name = trim($_POST["name"] ?? "");

    if (mb_strlen($name) < 2 || mb_strlen($name) > 50) {
        $error = "Аты 2-50 таңба аралығында болуы керек.";
    } else {
        $_SESSION["demo_name"] = $name;
        $success = "Профиль аты уақытша жаңартылды!";
    }
}

$name = $_SESSION["demo_name"];

include __DIR__ . "/includes/header.php";
?>

<main class="profile-page">

    <div class="profile-banner">
        <div class="profile-avatar">
            <?= htmlspecialchars(
                mb_strtoupper(mb_substr($name, 0, 1)),
                ENT_QUOTES,
                "UTF-8"
            ) ?>
        </div>

        <h2>
            <?= htmlspecialchars($name, ENT_QUOTES, "UTF-8") ?>
        </h2>

        <p>Task Manager пайдаланушысы</p>
    </div>

    <div class="profile-card">
        <h3>Жеке мәліметтер</h3>

        <div class="profile-detail">
            <strong>Email</strong>
            <span>
                <?= htmlspecialchars($email, ENT_QUOTES, "UTF-8") ?>
            </span>
        </div>

        <div class="profile-detail">
            <strong>Аккаунт түрі</strong>
            <span>Демонстрациялық аккаунт</span>
        </div>

        <hr>

        <h3>Профильді өзгерту</h3>

        <?php if ($error): ?>
            <p class="form-error">
                <?= htmlspecialchars($error, ENT_QUOTES, "UTF-8") ?>
            </p>
        <?php endif; ?>

        <?php if ($success): ?>
            <p class="form-success">
                <?= htmlspecialchars($success, ENT_QUOTES, "UTF-8") ?>
            </p>
        <?php endif; ?>

        <form method="POST" action="profile.php">
            <div class="form-group">
                <label for="profileName">Аты-жөні</label>
                <input
                    type="text"
                    id="profileName"
                    name="name"
                    maxlength="50"
                    value="<?= htmlspecialchars($name, ENT_QUOTES, 'UTF-8') ?>"
                    required>
            </div>

            <button type="submit" class="primary-btn">
                Өзгерістерді сақтау
            </button>
        </form>

        <p class="profile-note">
            Ескерту: бұл өзгерістер әзірге PHP
            сессиясында сақталады.
            Нақты дерекқор 11-аптада қосылады.
        </p>

    </div>

</main>

<?php include __DIR__ . "/includes/footer.php"; ?>
