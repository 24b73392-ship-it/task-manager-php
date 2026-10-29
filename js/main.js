
document.addEventListener("DOMContentLoaded", function () {

    // 1. Демо жүйеге кіру күйін тексеру
    const isLoggedIn =
        sessionStorage.getItem("demoLoggedIn") === "true";

    const currentPage = window.location.pathname
        .split("/")
        .pop()
        .toLowerCase();

    // Кірмеген пайдаланушы тапсырмаларға өте алмайды
    if (currentPage === "tasks.html" && !isLoggedIn) {
        window.location.replace("login.html");
        return;
    }

    // 2. Тіркелу формасы
    const registerForm = document.getElementById("registerForm");

    if (registerForm) {
        registerForm.addEventListener("submit", function (event) {
            event.preventDefault();

            const name = document.getElementById("name").value.trim();
            const email = document.getElementById("email").value.trim();
            const password = document.getElementById("password").value;
            const confirmPassword =
                document.getElementById("confirmPassword").value;

            if (!name || !email || !password || !confirmPassword) {
                alert("Барлық өрістерді толтырыңыз!");
                return;
            }

            if (password.length < 6) {
                alert("Құпиясөз кемінде 6 таңбадан тұруы керек!");
                return;
            }

            if (password !== confirmPassword) {
                alert("Құпиясөздер сәйкес келмейді!");
                return;
            }

            // Бұл жерде аккаунт сақталмайды
            alert("Тіркелу формасы дұрыс толтырылды! Бұл — демо.");
            window.location.href = "login.html";
        });
    }

    // 3. Кіру формасы
    const loginForm = document.getElementById("loginForm");

    if (loginForm) {
        loginForm.addEventListener("submit", function (event) {
            event.preventDefault();

            const email =
                document.getElementById("loginEmail").value.trim();

            const password =
                document.getElementById("loginPassword").value;

            if (!email || !password) {
                alert("Email және құпиясөзді енгізіңіз!");
                return;
            }

            // 9-апта: тек демонстрациялық кіру
            // Нақты аккаунтты тексеру 11-аптада жасалады
            sessionStorage.setItem("demoLoggedIn", "true");

            window.location.href = "tasks.html";
        });
    }

    // 4. Аккаунттан шығу
    const logoutBtn = document.getElementById("logoutBtn");

    if (logoutBtn) {
        logoutBtn.addEventListener("click", function () {
            sessionStorage.removeItem("demoLoggedIn");
            window.location.href = "login.html";
        });
    }

    // 5. Тапсырмалар беті
    const taskForm = document.getElementById("taskForm");

    if (!taskForm) {
        return;
    }

    const taskList = document.getElementById("taskList");
    const searchInput = document.getElementById("searchTask");
    const statusFilter = document.getElementById("statusFilter");

    // Сақталған тапсырмаларды алу
    let tasks = [];

    try {
        const savedTasks = JSON.parse(
            localStorage.getItem("tasks")
        );
        tasks = Array.isArray(savedTasks) ? savedTasks : [];
    } catch (error) {
        tasks = [];
    }

    // Тапсырмаларды браузерде сақтау
    function saveTasks() {
        localStorage.setItem("tasks", JSON.stringify(tasks));
    }

    // 6. Тапсырмаларды экранға шығару
    function renderTasks() {
        taskList.innerHTML = "";

        const search = searchInput.value.toLowerCase().trim();
        const filter = statusFilter.value;

        const filteredTasks = tasks.filter(function (task) {
            const matchesSearch =
                task.title.toLowerCase().includes(search);

            const matchesStatus =
                filter === "all" || task.status === filter;

            return matchesSearch && matchesStatus;
        });

        if (filteredTasks.length === 0) {
            const emptyMessage = document.createElement("p");
            emptyMessage.className = "empty-message";
            emptyMessage.textContent = "Тапсырмалар табылмады";
            taskList.appendChild(emptyMessage);
        }

        filteredTasks.forEach(function (task) {
            const item = document.createElement("div");
            item.className = "task-item";

            if (task.status === "completed") {
                item.classList.add("completed");
            }

            const info = document.createElement("div");
            info.className = "task-info";

            const title = document.createElement("h4");
            title.textContent = task.title;

            const date = document.createElement("p");
            date.textContent = "Мерзімі: " + task.date;

            info.append(title, date);

            const actions = document.createElement("div");
            actions.className = "task-actions";

            // Орындалды / Қайтару
            const completeBtn = document.createElement("button");
            completeBtn.type = "button";
            completeBtn.className = "complete-btn";

            completeBtn.textContent =
                task.status === "completed"
                    ? "Қайтару"
                    : "Орындалды";

            completeBtn.addEventListener("click", function () {
                task.status =
                    task.status === "completed"
                        ? "pending"
                        : "completed";

                saveTasks();
                renderTasks();
            });

            // Жою батырмасы
            const deleteBtn = document.createElement("button");
            deleteBtn.type = "button";
            deleteBtn.className = "delete-btn";
            deleteBtn.textContent = "Жою";

            deleteBtn.addEventListener("click", function () {
                if (confirm("Тапсырманы жоясыз ба?")) {
                    tasks = tasks.filter(function (t) {
                        return t.id !== task.id;
                    });

                    saveTasks();
                    renderTasks();
                }
            });

            actions.append(completeBtn, deleteBtn);
            item.append(info, actions);
            taskList.appendChild(item);
        });

        // Статистиканы жаңарту
        document.getElementById("totalCount").textContent =
            tasks.length;

        document.getElementById("pendingCount").textContent =
            tasks.filter(t => t.status === "pending").length;

        document.getElementById("completedCount").textContent =
            tasks.filter(t => t.status === "completed").length;
    }

    // 7. Тапсырма қосу
    taskForm.addEventListener("submit", function (event) {
        event.preventDefault();

        const title =
            document.getElementById("taskTitle").value.trim();

        const date =
            document.getElementById("taskDate").value;

        if (!title || !date) {
            alert("Барлық өрістерді толтырыңыз!");
            return;
        }

        const newTask = {
            id: Date.now().toString() +
                Math.random().toString(36).slice(2),
            title: title,
            date: date,
            status: "pending"
        };

        tasks.push(newTask);

        saveTasks();
        renderTasks();
        taskForm.reset();
    });

    // 8. Іздеу және сүзу
    searchInput.addEventListener("input", renderTasks);
    statusFilter.addEventListener("change", renderTasks);

    // Алғашқы көрсету
    renderTasks();
});
