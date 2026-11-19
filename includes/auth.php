
<?php

if (session_status() === PHP_SESSION_NONE) {
    session_start([
        "use_strict_mode" => true,
        "cookie_httponly" => true,
        "cookie_samesite" => "Lax"
    ]);
}

// Формаларды қорғауға арналған CSRF токені
if (empty($_SESSION["csrf_token"])) {
    $_SESSION["csrf_token"] = bin2hex(random_bytes(32));
}

function csrfValid(): bool
{
    return isset($_POST["csrf_token"])
        && is_string($_POST["csrf_token"])
        && hash_equals(
            $_SESSION["csrf_token"],
            $_POST["csrf_token"]
        );
}
