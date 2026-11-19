
<?php
require_once __DIR__ . '/includes/auth.php';
require_once __DIR__ . '/config/database.php';

// Аккаунтты тексеру
if (empty($_SESSION['user_id'])) {
    header('Location: login.php');
    exit;
}

// Өзгеріс тек POST арқылы жасалады
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    exit('Тек POST сұранысына рұқсат.');
}

// CSRF қорғанысы
if (!csrfValid()) {
    http_response_code(403);
    exit('CSRF тексеруі өтпеді.');
}

$userId = (int) $_SESSION['user_id'];
$action = $_POST['action'] ?? '';

$taskId = filter_input(
    INPUT_POST,
    'task_id',
    FILTER_VALIDATE_INT,
    ['options' => ['min_range' => 1]]
);

// DELETE және статусты өзгерту
if ($action === 'delete' || $action === 'toggle') {

    if (!$taskId) {
        http_response_code(400);
        exit('Тапсырма ID дұрыс емес.');
    }

    if ($action === 'delete') {

        // DELETE: тапсырманы жою
        $stmt = $pdo->prepare(
            'DELETE FROM tasks
             WHERE id = ? AND user_id = ?'
        );

        $stmt->execute([$taskId, $userId]);

        $_SESSION['task_message'] = 'Тапсырма жойылды.';

    } else {

        // UPDATE: орындалу статусын өзгерту
        $stmt = $pdo->prepare(
            "UPDATE tasks
             SET status = CASE
                WHEN status = 'pending'
                THEN 'completed'
                ELSE 'pending'
             END
             WHERE id = ? AND user_id = ?"
        );

        $stmt->execute([$taskId, $userId]);

        $_SESSION['task_message'] =
            'Тапсырманың күйі өзгерді.';
    }

    header('Location: tasks.php');
    exit;
}

// CREATE және UPDATE
if ($action !== 'create' && $action !== 'update') {
    http_response_code(400);
    exit('Белгісіз әрекет.');
}

if ($action === 'update' && !$taskId) {
    http_response_code(400);
    exit('Тапсырма ID дұрыс емес.');
}

// Формадан мәліметтерді алу
$title = trim($_POST['title'] ?? '');
$description = trim($_POST['description'] ?? '');
$dueDate = trim($_POST['due_date'] ?? '');
$priority = $_POST['priority'] ?? 'medium';
$color = $_POST['color'] ?? 'blue';
$status = $_POST['status'] ?? 'pending';
$flag = isset($_POST['is_flagged']) ? 1 : 0;

// Серверлік валидация
$errors = [];

if (mb_strlen($title) < 1 || mb_strlen($title) > 150) {
    $errors[] = 'Атауы 1–150 таңба болуы керек.';
}

if (mb_strlen($description) > 2000) {
    $errors[] = 'Сипаттама тым ұзын.';
}

if ($dueDate !== '') {

    $date = DateTimeImmutable::createFromFormat(
        '!Y-m-d',
        $dueDate
    );

    if (!$date || $date->format('Y-m-d') !== $dueDate) {
        $errors[] = 'Күн дұрыс көрсетілмеген.';
    }

} else {
    $dueDate = null;
}

if (!in_array($priority, ['low', 'medium', 'high'], true)) {
    $errors[] = 'Басымдық дұрыс емес.';
}

if (!in_array(
    $color,
    ['blue', 'red', 'green', 'orange', 'purple'],
    true
)) {
    $errors[] = 'Түс дұрыс емес.';
}

if (!in_array($status, ['pending', 'completed'], true)) {
    $errors[] = 'Күй дұрыс емес.';
}

// Қате болса, формаға қайту
if ($errors) {

    $_SESSION['task_error'] = implode(' ', $errors);

    $redirect = $action === 'update'
        ? 'edit_task.php?id=' . $taskId
        : 'tasks.php';

    header('Location: ' . $redirect);
    exit;
}

// CREATE: жаңа тапсырма қосу
if ($action === 'create') {

    $stmt = $pdo->prepare(
        'INSERT INTO tasks
         (user_id, category_id, title, description,
          due_date, status, priority, color, is_flagged)
         VALUES (?, NULL, ?, ?, ?, ?, ?, ?, ?)'
    );

    $stmt->execute([
        $userId,
        $title,
        $description,
        $dueDate,
        'pending',
        $priority,
        $color,
        $flag
    ]);

    $_SESSION['task_message'] =
        'Жаңа тапсырма MySQL-ға сақталды.';

// UPDATE: бар тапсырманы өңдеу
} else {

    $stmt = $pdo->prepare(
        'UPDATE tasks
         SET title = ?,
             description = ?,
             due_date = ?,
             status = ?,
             priority = ?,
             color = ?,
             is_flagged = ?
         WHERE id = ? AND user_id = ?'
    );

    $stmt->execute([
        $title,
        $description,
        $dueDate,
        $status,
        $priority,
        $color,
        $flag,
        $taskId,
        $userId
    ]);

    $_SESSION['task_message'] =
        'Өзгерістер MySQL-ға сақталды.';
}

header('Location: tasks.php');
exit;
