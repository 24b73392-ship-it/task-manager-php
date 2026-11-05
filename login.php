
<?php
session_start();

$pageTitle = "Кіру | Task Manager";
$error = "";

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $email = trim($_POST["email"] ?? "");
    $password = $_POST["password"] ?? "";

    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $error = "Email дұрыс енгізілмеген.";
    } elseif ($password === "") {
        $error = "Құпиясөзді енгізіңіз.";
    } else {
        session_regenerate_id(true);
        $_SESSION["demo_user"] = $email;

        header("Location: tasks.php");
        exit;
    }
}

include __DIR__ . "/includes/header.php";
?>

<div class="auth-layout">

    <section class="auth-intro">
        <p class="eyebrow">TASK MANAGER</p>
        <h2>Қайтадан <span>қош келдіңіз!</span></h2>

        <p>
            Жүйеге кіріп, тапсырмаларыңызды
            басқаруды жалғастырыңыз.
        </p>

        <div class="feature">✓ Жеке жұмыс аймағы</div>
        <div class="feature">✓ Күнделікті жоспар</div>
        <div class="feature">✓ Тапсырмаларды бақылау</div>
    </section>

    <section class="auth-card">
        <h2>Жүйеге кіру</h2>
        <p class="subtitle">
            Email және құпиясөз енгізіңіз.
        </p>

        <?php if ($error): ?>
            <p class="form-error">
                <?= htmlspecialchars($error, ENT_QUOTES, "UTF-8") ?>
            </p>
        <?php endif; ?>

        <form method="POST" action="login.php">
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
