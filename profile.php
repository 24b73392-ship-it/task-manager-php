
<?php
// 11-апта: MySQL арқылы жеке профиль

require_once __DIR__ . "/includes/auth.php";
require_once __DIR__ . "/config/database.php";

// Пайдаланушы кірмесе, кіру бетіне жіберу
if (empty($_SESSION["user_id"])) {
    header("Location: login.php");
    exit;
}

$pageTitle = "Менің профилім | Task Manager";
$userId = (int) $_SESSION["user_id"];

$error = "";
$success = "";

// Пайдаланушы мәліметтерін MySQL-дан алу
$stmt = $pdo->prepare(
    "SELECT id, name, email, created_at
     FROM users
     WHERE id = ?"
);

$stmt->execute([$userId]);
$user = $stmt->fetch();

// Егер аккаунт дерекқорда табылмаса
if (!$user) {
    $_SESSION = [];
    session_destroy();

    header("Location: login.php");
    exit;
}

$name = $user["name"];
$email = $user["email"];
$createdAt = $user["created_at"];

// Профильдегі атты өзгерту
if ($_SERVER["REQUEST_METHOD"] === "POST") {

    $newName = trim($_POST["name"] ?? "");

    if (!csrfValid()) {
        $error = "Қауіпсіздік қатесі. Бетті жаңартыңыз.";

    } elseif (
        mb_strlen($newName) < 2 ||
        mb_strlen($newName) > 100
    ) {
        $error = "Аты-жөні 2-100 таңба арасында болуы керек.";

    } else {

        // MySQL кестесіндегі атты жаңарту
        $stmt = $pdo->prepare(
            "UPDATE users
             SET name = ?
             WHERE id = ?"
        );

        $stmt->execute([$newName, $userId]);

        // Сессиядағы ақпаратты жаңарту
        $_SESSION["user_name"] = $newName;

        $name = $newName;
        $success = "Профиль сәтті жаңартылды!";
    }
}

include __DIR__ . "/includes/header.php";
?>

<main class="profile-page">

    <!-- Профильдің жоғарғы бөлігі -->
    <section class="profile-banner">

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

    </section>

    <!-- Жеке мәліметтер -->
    <section class="profile-card">

        <h3>Жеке мәліметтер</h3>

        <div class="profile-detail">
            <strong>Аты-жөні</strong>

            <span>
                <?= htmlspecialchars(
                    $name,
                    ENT_QUOTES,
                    "UTF-8"
                ) ?>
            </span>
        </div>

        <div class="profile-detail">
            <strong>Email</strong>

            <span>
                <?= htmlspecialchars(
                    $email,
                    ENT_QUOTES,
                    "UTF-8"
                ) ?>
            </span>
        </div>

        <div class="profile-detail">
            <strong>Тіркелген күні</strong>

            <span>
                <?= htmlspecialchars(
                    $createdAt,
                    ENT_QUOTES,
                    "UTF-8"
                ) ?>
            </span>
        </div>

        <div class="profile-detail">
            <strong>Аккаунт түрі</strong>
            <span>Пайдаланушы</span>
        </div>

        <hr>

        <!-- Профильді өңдеу -->
        <h3>Профильді өзгерту</h3>

        <?php if ($error): ?>

            <p class="form-error">
                <?= htmlspecialchars(
                    $error,
                    ENT_QUOTES,
                    "UTF-8"
                ) ?>
            </p>

        <?php endif; ?>

        <?php if ($success): ?>

            <p class="form-success">
                <?= htmlspecialchars(
                    $success,
                    ENT_QUOTES,
                    "UTF-8"
                ) ?>
            </p>

        <?php endif; ?>

        <form method="POST" action="profile.php">

            <!-- CSRF қорғанысы -->
            <input
                type="hidden"
                name="csrf_token"
                value="<?= htmlspecialchars(
                    $_SESSION["csrf_token"],
                    ENT_QUOTES,
                    "UTF-8"
                ) ?>">

            <div class="form-group">

                <label for="profileName">
                    Аты-жөні
                </label>

                <input
                    type="text"
                    id="profileName"
                    name="name"
                    maxlength="100"
                    value="<?= htmlspecialchars(
                        $name,
                        ENT_QUOTES,
                        "UTF-8"
                    ) ?>"
                    required>
            </div>

            <button
                type="submit"
                class="primary-btn">
                Өзгерістерді сақтау
            </button>

        </form>

    </section>

</main>

<?php
include __DIR__ . "/includes/footer.php";
?>
