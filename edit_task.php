
<?php
require_once __DIR__ . '/includes/auth.php';
require_once __DIR__ . '/config/database.php';

if (empty($_SESSION['user_id'])) {
    header('Location: login.php');
    exit;
}

function h($value): string {
    return htmlspecialchars(
        (string)$value,
        ENT_QUOTES | ENT_SUBSTITUTE,
        'UTF-8'
    );
}

// URL-ден тапсырманың ID нөмірін алу
$taskId = filter_input(
    INPUT_GET,
    'id',
    FILTER_VALIDATE_INT,
    ['options' => ['min_range' => 1]]
);

if (!$taskId) {
    http_response_code(400);
    exit('Тапсырма нөмірі дұрыс емес.');
}

// Тек өз тапсырмасын өңдей алады
$stmt = $pdo->prepare(
    'SELECT * FROM tasks WHERE id = ? AND user_id = ?'
);

$stmt->execute([
    $taskId,
    (int)$_SESSION['user_id']
]);

$task = $stmt->fetch();

if (!$task) {
    http_response_code(404);
    exit('Тапсырма табылмады.');
}

$pageTitle = 'Тапсырманы өңдеу | Task Manager';

$error = $_SESSION['task_error'] ?? '';
unset($_SESSION['task_error']);

include __DIR__ . '/includes/header.php';
?>

<main class="dashboard">

    <section class="task-panel">

        <h2>Тапсырманы өңдеу</h2>

        <?php if ($error): ?>
            <p class="form-error"><?= h($error) ?></p>
        <?php endif; ?>

        <form method="POST"
              action="task_action.php"
              class="task-form">

            <input type="hidden"
                   name="csrf_token"
                   value="<?= h($_SESSION['csrf_token']) ?>">

            <input type="hidden"
                   name="action"
                   value="update">

            <input type="hidden"
                   name="task_id"
                   value="<?= (int)$task['id'] ?>">

            <div class="form-group">
                <label for="title">Атауы</label>

                <input id="title"
                       name="title"
                       value="<?= h($task['title']) ?>"
                       maxlength="150"
                       required>
            </div>

            <div class="form-group">
                <label for="due_date">Мерзімі</label>

                <input id="due_date"
                       type="date"
                       name="due_date"
                       value="<?= h($task['due_date'] ?? '') ?>">
            </div>

            <div class="form-group">
                <label for="priority">Басымдық</label>

                <select id="priority" name="priority">
                    <?php foreach (
                        ['low'=>'Төмен',
                         'medium'=>'Орташа',
                         'high'=>'Жоғары']
                        as $value => $label
                    ): ?>
                        <option value="<?= h($value) ?>"
                            <?= $task['priority'] === $value ? 'selected' : '' ?>>
                            <?= h($label) ?>
                        </option>
                    <?php endforeach; ?>
                </select>
            </div>

            <div class="form-group">
                <label for="color">Түсі</label>

                <select id="color" name="color">
                    <?php foreach (
                        ['blue'=>'Көк',
                         'red'=>'Қызыл',
                         'green'=>'Жасыл',
                         'orange'=>'Сары',
                         'purple'=>'Күлгін']
                        as $value => $label
                    ): ?>
                        <option value="<?= h($value) ?>"
                            <?= $task['color'] === $value ? 'selected' : '' ?>>
                            <?= h($label) ?>
                        </option>
                    <?php endforeach; ?>
                </select>
            </div>

            <div class="form-group">
                <label for="status">Күйі</label>

                <select id="status" name="status">
                    <option value="pending"
                        <?= $task['status'] === 'pending' ? 'selected' : '' ?>>
                        Орындалмаған
                    </option>

                    <option value="completed"
                        <?= $task['status'] === 'completed' ? 'selected' : '' ?>>
                        Орындалған
                    </option>
                </select>
            </div>

            <div class="form-group wide-field">
                <label for="description">Сипаттама</label>

                <textarea id="description"
                          name="description"
                          maxlength="2000"
                          rows="3"><?= h($task['description'] ?? '') ?></textarea>
            </div>

            <label class="flag-checkbox">
                <input type="checkbox"
                       name="is_flagged"
                       value="1"
                       <?= $task['is_flagged'] ? 'checked' : '' ?>>
                ⚑ Маңызды
            </label>

            <button type="submit" class="primary-btn">
                Өзгерістерді сақтау
            </button>

        </form>

        <p>
            <a href="tasks.php">← Тапсырмаларға қайту</a>
        </p>

    </section>
</main>

<?php include __DIR__ . '/includes/footer.php'; ?>
