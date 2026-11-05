
document.addEventListener("DOMContentLoaded", function () {

    const dashboard = document.getElementById("taskDashboard");

    // Тапсырмалар бетінде болмаса, код орындалмайды
    if (!dashboard) return;

    const taskForm = document.getElementById("taskForm");
    const taskList = document.getElementById("taskList");
    const searchInput = document.getElementById("searchTask");
    const statusFilter = document.getElementById("statusFilter");

    // Әр демо email үшін бөлек сақтау кілті
    const userEmail = dashboard.dataset.user;
    const storageKey =
        "taskmanager_demo_" + encodeURIComponent(userEmail);

    let tasks = [];

    try {
        const saved = JSON.parse(localStorage.getItem(storageKey));
        tasks = Array.isArray(saved) ? saved : [];
    } catch (error) {
        tasks = [];
    }

    const colors = {
        blue: "#3b82f6",
        red: "#ef4444",
        green: "#22c55e",
        orange: "#f59e0b",
        purple: "#8b5cf6"
    };

    const priorities = {
        low: "Төмен",
        medium: "Орташа",
        high: "Жоғары"
    };

    function saveTasks() {
        localStorage.setItem(storageKey, JSON.stringify(tasks));
    }

    function renderTasks() {
        taskList.innerHTML = "";

        const query = searchInput.value.toLowerCase().trim();
        const filter = statusFilter.value;

        const filteredTasks = tasks.filter(function (task) {
            const titleMatches =
                task.title.toLowerCase().includes(query);

            const statusMatches =
                filter === "all" || task.status === filter;

            return titleMatches && statusMatches;
        });

        // Маңызды тапсырмалар бірінші шығады
        filteredTasks.sort(function (a, b) {
            if (a.flag && !b.flag) return -1;
            if (!a.flag && b.flag) return 1;
            return 0;
        });

        if (filteredTasks.length === 0) {
            const empty = document.createElement("p");
            empty.className = "empty-message";
            empty.textContent = "Тапсырмалар табылмады";
            taskList.appendChild(empty);
        }

        filteredTasks.forEach(function (task) {
            const item = document.createElement("div");
            item.className = "task-item";

            const taskColor = colors[task.color] || colors.blue;
            item.style.borderLeft = "5px solid " + taskColor;

            if (task.status === "completed") {
                item.classList.add("completed");
            }

            const info = document.createElement("div");
            info.className = "task-info";

            const titleRow = document.createElement("div");
            titleRow.className = "task-title-row";

            const title = document.createElement("h4");
            title.textContent = task.title;

            titleRow.appendChild(title);

            if (task.flag) {
                const flag = document.createElement("span");
                flag.className = "task-flag";
                flag.textContent = "⚑";
                flag.title = "Маңызды тапсырма";
                titleRow.appendChild(flag);
            }

            const date = document.createElement("p");
            date.textContent = "Мерзімі: " + task.date;

            const priority = document.createElement("span");
            priority.className =
                "priority-badge priority-" + (
                    ["low", "medium", "high"].includes(task.priority)
                        ? task.priority
                        : "medium"
                );

            priority.textContent =
                priorities[task.priority] || "Орташа";

            info.append(titleRow, date, priority);

            const actions = document.createElement("div");
            actions.className = "task-actions";

            const completeBtn = document.createElement("button");
            completeBtn.type = "button";
            completeBtn.className = "complete-btn";
            completeBtn.textContent =
                task.status === "completed"
                    ? "Қайтару"
                    : "Орындалды";

            completeBtn.addEventListener("click", function () {
                task.status = task.status === "completed"
                    ? "pending"
                    : "completed";

                saveTasks();
                renderTasks();
            });

            const deleteBtn = document.createElement("button");
            deleteBtn.type = "button";
            deleteBtn.className = "delete-btn";
            deleteBtn.textContent = "Жою";

            deleteBtn.addEventListener("click", function () {
                if (window.confirm("Тапсырманы жоясыз ба?")) {
                    tasks = tasks.filter(t => t.id !== task.id);
                    saveTasks();
                    renderTasks();
                }
            });

            actions.append(completeBtn, deleteBtn);
            item.append(info, actions);
            taskList.appendChild(item);
        });

        document.getElementById("totalCount").textContent =
            tasks.length;

        document.getElementById("pendingCount").textContent =
            tasks.filter(t => t.status === "pending").length;

        document.getElementById("completedCount").textContent =
            tasks.filter(t => t.status === "completed").length;
    }

    // Жаңа тапсырма қосу
    taskForm.addEventListener("submit", function (event) {
        event.preventDefault();

        const title =
            document.getElementById("taskTitle").value.trim();

        const date =
            document.getElementById("taskDate").value;

        const priority =
            document.getElementById("taskPriority").value;

        const color =
            document.getElementById("taskColor").value;

        const flag =
            document.getElementById("taskFlag").checked;

        if (!title || !date) {
            alert("Атауы мен мерзімін толтырыңыз!");
            return;
        }

        const newTask = {
            id: Date.now().toString() +
                Math.random().toString(36).slice(2),
            title: title,
            date: date,
            priority: priority,
            color: color,
            flag: flag,
            status: "pending"
        };

        tasks.push(newTask);
        saveTasks();
        renderTasks();
        taskForm.reset();
    });

    searchInput.addEventListener("input", renderTasks);
    statusFilter.addEventListener("change", renderTasks);

    renderTasks();
});
