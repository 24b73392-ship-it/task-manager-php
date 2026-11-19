<?php
$pageTitle = "Басты бет | Task Manager";

$section = $_GET["section"] ?? "home";

if (!in_array($section, ["home", "features"], true)) {
    $section = "home";
}

include __DIR__ . "/includes/header.php";
?>


<main class="landing-page">

    
<div class="get-demo">
    <a href="index.php?section=home">Басты бет</a>
    <a href="index.php?section=features">Мүмкіндіктер</a>

    <?php if ($section === "features"): ?>
        <p>GET нәтижесі: сайттың мүмкіндіктері таңдалды.</p>
    <?php else: ?>
        <p>GET нәтижесі: басты бет таңдалды.</p>
    <?php endif; ?>
</div>


    <section class="hero">
        <div class="hero-content">

            <span class="hero-label">
                УАҚЫТЫҢЫЗДЫ ТИІМДІ БАСҚАРЫҢЫЗ
            </span>

            <h1>
                Әр күніңізді
                <span>жоспарлаңыз.</span>
                Мақсатыңызға жетіңіз.
            </h1>

            <p>
                Task Manager — күнделікті
                тапсырмаларыңызды реттеп,
                маңызды істерді бақылауға арналған
                ыңғайлы веб-қосымша.
            </p>

            <div class="hero-buttons">
                <?php if ($isLoggedIn): ?>

                    <a href="tasks.php" class="hero-primary">
                        Менің тапсырмаларым →
                    </a>

                    <a href="profile.php" class="hero-secondary">
                        Жеке профиль
                    </a>

                <?php else: ?>

                    <a href="register.php" class="hero-primary">
                        Тегін бастау →
                    </a>

                    <a href="login.php" class="hero-secondary">
                        Аккаунтқа кіру
                    </a>

                <?php endif; ?>
            </div>
        </div>

        <div class="hero-preview">
            <div class="preview-header">
                <span>Менің бүгінгі жоспарым</span>
                <span>✓</span>
            </div>

            <div class="preview-task">
                <span class="preview-dot blue"></span>
                <span>PHP зертханасын орындау</span>
                <span>Маңызды</span>
            </div>

            <div class="preview-task">
                <span class="preview-dot green"></span>
                <span>Жоба құжаттарын дайындау</span>
                <span>Орындалды</span>
            </div>

            <div class="preview-task">
                <span class="preview-dot orange"></span>
                <span>Емтиханға дайындалу</span>
                <span>Жоспарда</span>
            </div>

            <div class="preview-progress">
                <strong>Күндік прогресс</strong>
                <div class="progress-track">
                    <div class="progress-fill"></div>
                </div>
                <small>Мысал: тапсырмалардың 65%-ы орындалды</small>
            </div>
        </div>
    </section>

    <section class="landing-features">
        <div class="section-heading">
            <span>МҮМКІНДІКТЕР</span>
            <h2>Барлығы бір жерде</h2>
            <p>Жоспарлаудан нәтижеге дейін.</p>
        </div>

        <div class="landing-grid">

            <article class="landing-feature">
                <div class="feature-symbol">✓</div>
                <h3>Тапсырмаларды басқару</h3>
                <p>
                    Тапсырмаларды қосып,
                    олардың орындалуын бақылаңыз.
                </p>
            </article>

            <article class="landing-feature">
                <div class="feature-symbol">⚑</div>
                <h3>Басымдықтар</h3>
                <p>
                    Маңызды тапсырмаларды түс
                    және жалаушамен белгілеңіз.
                </p>
            </article>

            <article class="landing-feature">
                <div class="feature-symbol">▣</div>
                <h3>Статистика</h3>
                <p>
                    Орындалған және қалған
                    тапсырмаларды көріңіз.
                </p>
            </article>

        </div>
    </section>

    <section class="landing-cta">
        <h2>Жоспарлауды бүгін бастаңыз!</h2>
        <p>Өзіңізге ыңғайлы тапсырмалар тізімін құрыңыз.</p>

        <?php if ($isLoggedIn): ?>
            <a href="tasks.php" class="hero-primary">
                Тапсырмаларыма өту
            </a>
        <?php else: ?>
            <a href="register.php" class="hero-primary">
                Бастау
            </a>
        <?php endif; ?>
    </section>

</main>

<?php include __DIR__ . "/includes/footer.php"; ?>
