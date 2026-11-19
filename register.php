
<?php
require_once __DIR__ . "/includes/auth.php";
require_once __DIR__ . "/config/database.php";

if (!empty($_SESSION["user_id"])) {
    header("Location: tasks.php");
    exit;
}

$pageTitle = "Тіркелу | Task Manager";

$errors = [];
$name = "";
$email = "";

if ($_SERVER["REQUEST_METHOD"] === "POST") {

    $name = trim($_POST["name"] ?? "");
    $email = trim($_POST["email"] ?? "");
    $password = $_POST["password"] ?? "";
    $confirm = $_POST["confirmPassword"] ?? "";

    if (!csrfValid()) {
        $errors[] = "Форманы жаңартып, қайта жіберіңіз.";
    }

    if (mb_strlen($name) < 2 || mb_strlen($name) > 100) {
        $errors[] = "Аты-жөні 2-100 таңба болуы керек.";
    }

    if (
        !filter_var($email, FILTER_VALIDATE_EMAIL)
        || strlen($email) > 190
    ) {
        $errors[] = "Email дұрыс енгізілмеген.";
    }

    if (strlen($password) < 8) {
        $errors[] = "Құпиясөз кемінде 8 таңба болуы керек.";
    }

    if ($password !== $confirm) {
        $errors[] = "Құпиясөздер сәйкес келмейді.";
    }

    if (empty($errors)) {

        // Email бұрын тіркелген бе?
        $stmt = $pdo->prepare(
            "SELECT id FROM users WHERE email = ?"
        );
        $stmt->execute([$email]);

        if ($stmt->fetch()) {
            $errors[] = "Бұл email бұрын тіркелген.";
        } else {

            // Құпиясөзді хэштеу
            $hash = password_hash(
                $password,
                PASSWORD_DEFAULT
            );

            try {
                $stmt = $pdo->prepare(
                    "INSERT INTO users (name, email, password_hash)
                     VALUES (?, ?, ?)"
                );

                $stmt->execute([$name, $email, $hash]);

                header("Location: login.php?registered=1");
                exit;

            } catch (PDOException $e) {
                if ($e->getCode() === "23000") {
                    $errors[] = "Бұл email бұрын тіркелген.";
                } else {
                    throw $e;
                }
            }
        }
    }
}

include __DIR__ . "/includes/header.php";
?>

<div class="auth-layout">

    <section class="auth-intro">
        <p class="eyebrow">TASK MANAGER</p>

        <h2>Жаңа аккаунт <span>құрыңыз</span></h2>

        <p>
            Өз тапсырмаларыңызды жоспарлап,
            күнделікті жұмысыңызды тиімді басқарыңыз.
        </p>

        <div class="feature">✓ Жеке аккаунт</div>
        <div class="feature">✓ Тапсырмаларды жоспарлау</div>
        <div class="feature">✓ Орындалуын бақылау</div>
    </section>

    <section class="auth-card">

        <h2>Тіркелу</h2>
        <p class="subtitle">
            Жеке аккаунт ашу үшін мәлімет енгізіңіз.
        </p>

        <?php foreach ($errors as $error): ?>
            <p class="form-error">
                <?= htmlspecialchars($error, ENT_QUOTES, "UTF-8") ?>
            </p>
        <?php endforeach; ?>

        <form method="POST" action="register.php">

            <input type="hidden"
                   name="csrf_token"
                   value="<?= htmlspecialchars(
                       $_SESSION["csrf_token"],
                       ENT_QUOTES,
                       "UTF-8"
                   ) ?>">

            <div class="form-group">
                <label for="name">Аты-жөні</label>
                <input type="text"
                       id="name"
                       name="name"
                       maxlength="100"
                       value="<?= htmlspecialchars(
                           $name,
                           ENT_QUOTES,
                           "UTF-8"
                       ) ?>"
                       required>
            </div>

            <div class="form-group">
                <label for="email">Email</label>
                <input type="email"
                       id="email"
                       name="email"
                       value="<?= htmlspecialchars(
                           $email,
                           ENT_QUOTES,
                           "UTF-8"
                       ) ?>"
                       required>
            </div>

            <div class="form-group">
                <label for="password">Құпиясөз</label>
                <input type="password"
                       id="password"
                       name="password"
                       minlength="8"
                       required>
            </div>

            <div class="form-group">
                <label for="confirmPassword">
                    Құпиясөзді растау
                </label>
                <input type="password"
                       id="confirmPassword"
                       name="confirmPassword"
                       minlength="8"
                       required>
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
