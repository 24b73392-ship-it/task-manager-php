
<?php
require_once __DIR__ . '/includes/auth.php';
require_once __DIR__ . '/config/database.php';

// Аккаунтқа кіргенін тексеру
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

$userId = (int) $_SESSION['user_id'];
$pageTitle = 'Менің тапсырмаларым | Task Manager';

// READ: MySQL-дан өз тапсырмаларын оқу
$stmt = $pdo->prepare(
    'SELECT id, title, description, due_date,
            status, priority, color, is_flagged
     FROM tasks
     WHERE user_id = ?
     ORDER BY is_flagged DESC, id DESC'
);

$stmt->execute([$userId]);
$tasks = $stmt->fetchAll();

// Статистика
$total = count($tasks);

$completed = count(array_filter(
    $tasks,
    fn($t) => $t['status'] === 'completed'
));

$pending = $total - $completed;

// Хабарламалар
$message = $_SESSION['task_message'] ?? '';
$error = $_SESSION['task_error'] ?? '';

unset(
    $_SESSION['task_message'],
    $_SESSION['task_error']
);

$colorCodes = [
    'blue' => '#3b82f6',
    'red' => '#ef4444',
    'green' => '#22c55e',
    'orange' => '#f59e0b',
    'purple' => '#8b5cf6'
];

$priorityLabels = [
    'low' => 'Төмен',
    'medium' => 'Орташа',
    'high' => 'Жоғары'
];

include __DIR__ . '/includes/header.php';
?>

<main class="dashboard">

    <div class="dashboard-heading">
        <p class="eyebrow">МЕНІҢ ЖҰМЫС АЙМАҒЫМ</p>
        <h2>Менің тапсырмаларым</h2>
        <p>Тапсырмаларыңыз MySQL дерекқорында сақталады.</p>
    </div>

    <?php if ($message): ?>
        <p class="form-success"><?= h($message) ?></p>
    <?php endif; ?>

    <?php if ($error): ?>
        <p class="form-error"><?= h($error) ?></p>
    <?php endif; ?>

    <section class="stats-grid">
        <div class="stat-card">
            <p>Барлығы</p>
            <h3><?= $total ?></h3>
        </div>

        <div class="stat-card">
            <p>Орындалмаған</p>
            <h3><?= $pending ?></h3>
        </div>

        <div class="stat-card">
            <p>Орындалған</p>
            <h3><?= $completed ?></h3>
        </div>
    </section>

    <section class="task-panel">

        <h3>Жаңа тапсырма</h3>

        <form method="POST"
              action="task_action.php"
              class="task-form">

            <input type="hidden"
                   name="csrf_token"
                   value="<?= h($_SESSION['csrf_token']) ?>">

            <input type="hidden" name="action" value="create">

            <div class="form-group">
                <label for="title">Тапсырма атауы</label>
                <input id="title"
                       name="title"
                       maxlength="150"
                       required
                       placeholder="Мысалы: PHP зертханасын орындау">
            </div>

            <div class="form-group">
                <label for="due_date">Мерзімі</label>
                <input id="due_date"
                       type="date"
                       name="due_date">
            </div>

            <div class="form-group">
                <label for="priority">Басымдық</label>

                <select id="priority" name="priority">
                    <option value="low">Төмен</option>
                    <option value="medium" selected>Орташа</option>
                    <option value="high">Жоғары</option>
                </select>
            </div>

            <div class="form-group">
                <label for="color">Түсі</label>

                <select id="color" name="color">
                    <option value="blue">Көк</option>
                    <option value="red">Қызыл</option>
                    <option value="green">Жасыл</option>
                    <option value="orange">Сары</option>
                    <option value="purple">Күлгін</option>
                </select>
            </div>

            <div class="form-group wide-field">
                <label for="description">Сипаттама</label>

                <textarea id="description"
                          name="description"
                          maxlength="2000"
                          rows="3"></textarea>
            </div>

            <label class="flag-checkbox">
                <input type="checkbox"
                       name="is_flagged"
                       value="1">
                ⚑ Маңызды тапсырма
            </label>

            <button type="submit" class="primary-btn">
                + Тапсырма қосу
            </button>

        </form>
    </section>

    <section class="task-panel">

        <h3>Тапсырмалар тізімі</h3>

        <?php if (!$tasks): ?>
            <p class="empty-message">
                Әзірге тапсырма жоқ. Жоғарыдағы формадан қосыңыз.
            </p>
        <?php endif; ?>

        <div class="task-list">

        <?php foreach ($tasks as $task): ?>

            <article
                class="task-item <?= $task['status'] === 'completed' ? 'completed' : '' ?>"
                style="border-left: 5px solid <?= h($colorCodes[$task['color']] ?? $colorCodes['blue']) ?>">

                <div class="task-info">

                    <div class="task-title-row">
                        <h4><?= h($task['title']) ?></h4>

                        <?php if ($task['is_flagged']): ?>
                            <span class="task-flag"
                                  title="Маңызды">⚑</span>
                        <?php endif; ?>
                    </div>

                    <p>
                        Мерзімі:
                        <?= h($task['due_date'] ?: 'Көрсетілмеген') ?>
                    </p>

                    <?php if ($task['description']): ?>
                        <p><?= nl2br(h($task['description'])) ?></p>
                    <?php endif; ?>

                    <span class="priority-badge priority-<?= h($task['priority']) ?>">
                        <?= h($priorityLabels[$task['priority']] ?? 'Орташа') ?>
                    </span>

                </div>

                <div class="task-actions">

                    <form method="POST" action="task_action.php">
                        <input type="hidden"
                               name="csrf_token"
                               value="<?= h($_SESSION['csrf_token']) ?>">

                        <input type="hidden"
                               name="action"
                               value="toggle">

                        <input type="hidden"
                               name="task_id"
                               value="<?= (int)$task['id'] ?>">

                        <button type="submit" class="complete-btn">
                            <?= $task['status'] === 'completed'
                                ? 'Қайтару'
                                : 'Орындалды' ?>
                        </button>
                    </form>

                    <a class="edit-btn"
                       href="edit_task.php?id=<?= (int)$task['id'] ?>">
                        Өңдеу
                    </a>

                    <form method="POST"
                          action="task_action.php"
                          class="delete-form">

                        <input type="hidden"
                               name="csrf_token"
                               value="<?= h($_SESSION['csrf_token']) ?>">

                        <input type="hidden"
                               name="action"
                               value="delete">

                        <input type="hidden"
                               name="task_id"
                               value="<?= (int)$task['id'] ?>">

                        <button type="submit" class="delete-btn">
                            Жою
                        </button>
                    </form>

                </div>
            </article>

        <?php endforeach; ?>

        </div>
    </section>

</main>

<?php include __DIR__ . '/includes/footer.php'; ?>
