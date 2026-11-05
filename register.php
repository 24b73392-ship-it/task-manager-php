
<?php
$pageTitle = "Тіркелу | Task Manager";

$errors = [];
$success = "";
$name = "";
$email = "";

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $name = trim($_POST["name"] ?? "");
    $email = trim($_POST["email"] ?? "");
    $password = $_POST["password"] ?? "";
    $confirm = $_POST["confirmPassword"] ?? "";

    if (mb_strlen($name) < 2) {
        $errors[] = "Аты-жөні кемінде 2 таңба болуы керек.";
    }

    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $errors[] = "Email дұрыс енгізілмеген.";
    }

    if (strlen($password) < 6) {
        $errors[] = "Құпиясөз кемінде 6 таңба болуы керек.";
    }

    if ($password !== $confirm) {
        $errors[] = "Құпиясөздер сәйкес келмейді.";
    }

    if (empty($errors)) {
        $success = "Форма серверде сәтті тексерілді! "
                 . "Аккаунт әлі сақталмайды.";
    }
}

include __DIR__ . "/includes/header.php";
?>

<div class="auth-layout">

    <section class="auth-intro">
        <p class="eyebrow">TASK MANAGER</p>
        <h2>Жаңа аккаунт <span>құрыңыз</span></h2>

        <p>
            Тапсырмаларыңызды жоспарлап,
            күнделікті жұмысыңызды басқарыңыз.
        </p>

        <div class="feature">✓ Тапсырмаларды жоспарлау</div>
        <div class="feature">✓ Орындалуын бақылау</div>
        <div class="feature">✓ Уақытты тиімді пайдалану</div>
    </section>

    <section class="auth-card">
        <h2>Тіркелу</h2>
        <p class="subtitle">Мәліметтеріңізді енгізіңіз.</p>

        <?php foreach ($errors as $error): ?>
            <p class="form-error">
                <?= htmlspecialchars($error, ENT_QUOTES, "UTF-8") ?>
            </p>
        <?php endforeach; ?>

        <?php if ($success): ?>
            <p class="form-success">
                <?= htmlspecialchars($success, ENT_QUOTES, "UTF-8") ?>
            </p>
        <?php endif; ?>

        <form method="POST" action="register.php">

            <div class="form-group">
                <label for="name">Аты-жөні</label>
                <input
                    type="text"
                    name="name"
                    id="name"
                    value="<?= htmlspecialchars($name, ENT_QUOTES, 'UTF-8') ?>"
                    required>
            </div>

            <div class="form-group">
                <label for="email">Email</label>
                <input
                    type="email"
                    name="email"
                    id="email"
                    value="<?= htmlspecialchars($email, ENT_QUOTES, 'UTF-8') ?>"
                    required>
            </div>

            <div class="form-group">
                <label for="password">Құпиясөз</label>
                <input type="password"
                       name="password"
                       id="password"
                       minlength="6" required>
            </div>

            <div class="form-group">
                <label for="confirmPassword">
                    Құпиясөзді растау
                </label>
                <input type="password"
                       name="confirmPassword"
                       id="confirmPassword"
                       minlength="6" required>
            </div>

            <button class="primary-btn" type="submit">
                Тіркелу
            </button>
        </form>

        <p class="auth-bottom">
            Аккаунтыңыз бар ма?
            <a href="login.php">Кіру</a>
        </p>
    </section>
</div>

<?php include __DIR__ . "/includes/footer.php"; ?>
