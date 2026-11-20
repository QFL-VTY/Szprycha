<?php
session_start();
require 'baza.php';

if (!empty($_SESSION['uzytkownik'])) {
    header('Location: admin.php');
    exit;
}

$blad = '';
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $login = trim($_POST['login'] ?? '');
    $haslo = $_POST['haslo'] ?? '';

    $z = $pdo->prepare('SELECT login, haslo_hash FROM uzytkownicy WHERE login = :login');
    $z->execute(['login' => $login]);
    $u = $z->fetch();

    if ($u && password_verify($haslo, $u['haslo_hash'])) {
        session_regenerate_id(true);
        $_SESSION['uzytkownik'] = $u['login'];
        header('Location: admin.php');
        exit;
    }
    $blad = 'Błędny login lub hasło.';
}
?>
<!DOCTYPE html>
<html lang="pl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Logowanie – Szprycha</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css"
          integrity="sha384-QWTKZyjpPEjISv5WaRU9OFeRpok6YctnYmDr5pNlyT2bRjXh0JMhjY6hW+ALEwIH"
          crossorigin="anonymous">
    <link rel="stylesheet" href="panel.css">
</head>
<body class="panel-body">
<nav class="navbar navbar-dark navbar-szprycha">
    <div class="container">
        <a class="navbar-brand d-flex align-items-center gap-2" href="index.php">
            <svg viewBox="0 0 120 64" width="54" height="30" aria-hidden="true">
                <circle cx="28" cy="42" r="16" fill="none" stroke="#ffcc00" stroke-width="4"/>
                <circle cx="92" cy="42" r="16" fill="none" stroke="#ffcc00" stroke-width="4"/>
                <polyline points="28,42 50,18 60,42 28,42 50,18 76,18 60,42" fill="none" stroke="#fff" stroke-width="4" stroke-linejoin="round"/>
                <polyline points="76,18 92,42" fill="none" stroke="#fff" stroke-width="4"/>
            </svg>
            Szprycha
        </a>
    </div>
</nav>
<main class="container py-5">
    <div class="row justify-content-center">
        <div class="col-md-6 col-lg-5">
            <h1 class="h3 panel-title mb-4">Logowanie do panelu</h1>
            <div class="card panel-card shadow-sm">
                <div class="card-body p-4">
                    <?php if ($blad): ?>
                        <div class="alert alert-danger" role="alert"><?= htmlspecialchars($blad, ENT_QUOTES, 'UTF-8') ?></div>
                    <?php endif; ?>
                    <form method="post">
                        <div class="mb-3">
                            <label for="login" class="form-label">Login</label>
                            <input id="login" name="login" class="form-control" required autocomplete="username">
                        </div>
                        <div class="mb-4">
                            <label for="haslo" class="form-label">Hasło</label>
                            <input id="haslo" name="haslo" type="password" class="form-control" required autocomplete="current-password">
                        </div>
                        <button type="submit" class="btn btn-primary w-100">Zaloguj</button>
                    </form>
                </div>
            </div>
            <p class="mt-3"><a class="link-dark" href="index.php">Powrót na stronę</a></p>
        </div>
    </div>
</main>
</body>
</html>
