
<?php
require_once __DIR__ . "/includes/auth.php";
require_once __DIR__ . "/config/database.php";

if (!empty($_SESSION["user_id"])) {
    header("Location: tasks.php");
    exit;
}

$pageTitle = "Кіру | Task Manager";
$error = "";

if ($_SERVER["REQUEST_METHOD"] === "POST") {

    $email = trim($_POST["email"] ?? "");
    $password = $_POST["password"] ?? "";

    if (!csrfValid()) {
        $error = "Форманы жаңартып, қайта жіберіңіз.";

    } elseif (
        !filter_var($email, FILTER_VALIDATE_EMAIL)
        || $password === ""
    ) {
        $error = "Email немесе құпиясөз дұрыс емес.";

    } else {

        $stmt = $pdo->prepare(
            "SELECT id, name, email, password_hash
             FROM users
             WHERE email = ?"
        );

        $stmt->execute([$email]);
        $user = $stmt->fetch();

        if (
            $user &&
            password_verify($password, $user["password_hash"])
        ) {

            session_regenerate_id(true);

            $_SESSION["user_id"] = (int) $user["id"];
            $_SESSION["user_name"] = $user["name"];
            $_SESSION["user_email"] = $user["email"];

            header("Location: tasks.php");
            exit;

        } else {
            $error = "Email немесе құпиясөз дұрыс емес.";
        }
    }
}

include __DIR__ . "/includes/header.php";
?>

<div class="auth-layout">

    <section class="auth-intro">
        <p class="eyebrow">TASK MANAGER</p>

        <h2>Қайтадан <span>қош келдіңіз!</span></h2>

        <p>
            Жеке аккаунтыңызға кіріп,
            күнделікті тапсырмаларыңызды басқарыңыз.
        </p>

        <div class="feature">✓ Жеке профиль</div>
        <div class="feature">✓ Тапсырмаларды бақылау</div>
        <div class="feature">✓ Уақытты тиімді жоспарлау</div>
    </section>

    <section class="auth-card">

        <h2>Жүйеге кіру</h2>

        <p class="subtitle">
            Email және құпиясөзіңізді енгізіңіз.
        </p>

        <?php if (isset($_GET["registered"])): ?>
            <p class="form-success">
                Тіркелу сәтті аяқталды! Енді кіріңіз.
            </p>
        <?php endif; ?>

        <?php if ($error): ?>
            <p class="form-error">
                <?= htmlspecialchars($error, ENT_QUOTES, "UTF-8") ?>
            </p>
        <?php endif; ?>

        <form method="POST" action="login.php">

            <input type="hidden"
                   name="csrf_token"
                   value="<?= htmlspecialchars(
                       $_SESSION["csrf_token"],
                       ENT_QUOTES,
                       "UTF-8"
                   ) ?>">

            <div class="form-group">
                <label for="loginEmail">Email</label>
                <input type="email"
                       id="loginEmail"
                       name="email"
                       required>
            </div>

            <div class="form-group">
                <label for="loginPassword">Құпиясөз</label>
                <input type="password"
                       id="loginPassword"
                       name="password"
                       required>
            </div>

            <button class="primary-btn" type="submit">
                Кіру
            </button>
        </form>

        <p class="auth-bottom">
            Аккаунтыңыз жоқ па?
            <a href="register.php">Тіркелу</a>
        </p>

    </section>
</div>

<?php include __DIR__ . "/includes/footer.php"; ?>
