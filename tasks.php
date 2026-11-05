
<?php
session_start();

if (empty($_SESSION["demo_user"])) {
    header("Location: login.php");
    exit;
}

$pageTitle = "Менің тапсырмаларым | Task Manager";
$email = $_SESSION["demo_user"];

include __DIR__ . "/includes/header.php";
?>

<main class="dashboard"
      id="taskDashboard"
      data-user="<?= htmlspecialchars($email, ENT_QUOTES, 'UTF-8') ?>">

    <div class="dashboard-heading">
        <p class="eyebrow">ЖЕКЕ ЖҰМЫС АЙМАҒЫ</p>
        <h2>Менің тапсырмаларым</h2>
        <p>
            Тапсырмаларды жоспарлаңыз,
            түс пен басымдық арқылы реттеңіз.
        </p>
    </div>

    <section class="stats-grid">
        <div class="stat-card">
            <p>Барлық тапсырма</p>
            <h3 id="totalCount">0</h3>
        </div>
        <div class="stat-card">
            <p>Орындалмаған</p>
            <h3 id="pendingCount">0</h3>
        </div>
        <div class="stat-card">
            <p>Орындалған</p>
            <h3 id="completedCount">0</h3>
        </div>
    </section>

    <section class="task-panel">
        <h3>Жаңа тапсырма қосу</h3>

        <form id="taskForm" class="task-form">

            <div class="form-group">
                <label for="taskTitle">Тапсырма атауы</label>
                <input
                    type="text"
                    id="taskTitle"
                    maxlength="100"
                    placeholder="Мысалы: PHP зертханасы"
                    required>
            </div>

            <div class="form-group">
                <label for="taskDate">Мерзімі</label>
                <input type="date" id="taskDate" required>
            </div>

            <div class="form-group">
                <label for="taskPriority">Басымдық</label>
                <select id="taskPriority">
                    <option value="low">Төмен</option>
                    <option value="medium" selected>Орташа</option>
                    <option value="high">Жоғары</option>
                </select>
            </div>

            <div class="form-group">
                <label for="taskColor">Түсі</label>
                <select id="taskColor">
                    <option value="blue">Көк</option>
                    <option value="red">Қызыл</option>
                    <option value="green">Жасыл</option>
                    <option value="orange">Сары</option>
                    <option value="purple">Күлгін</option>
                </select>
            </div>

            <label class="flag-checkbox">
                <input type="checkbox" id="taskFlag">
                ⚑ Маңызды тапсырма
            </label>

            <button class="primary-btn" type="submit">
                + Тапсырма қосу
            </button>
        </form>
    </section>

    <section class="task-panel">
        <h3>Тапсырмалар тізімі</h3>

        <div class="filter-row">
            <input
                type="search"
                id="searchTask"
                placeholder="Тапсырманы іздеу...">

            <select id="statusFilter">
                <option value="all">Барлығы</option>
                <option value="pending">Орындалмаған</option>
                <option value="completed">Орындалған</option>
            </select>
        </div>

        <div id="taskList" class="task-list"></div>
    </section>

</main>

<?php include __DIR__ . "/includes/footer.php"; ?>
