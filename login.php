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
    <link rel="stylesheet" href="style.css">
</head>
<body>
<header><a class="logo" href="index.php"><span>Szprycha</span></a></header>
<main>
    <section>
        <h2>Logowanie do panelu</h2>
        <?php if ($blad): ?><p role="alert"><?= htmlspecialchars($blad, ENT_QUOTES, 'UTF-8') ?></p><?php endif; ?>
        <form method="post">
            <p><label for="login">Login</label><br><input id="login" name="login" required autocomplete="username"></p>
            <p><label for="haslo">Hasło</label><br><input id="haslo" name="haslo" type="password" required autocomplete="current-password"></p>
            <p><button type="submit">Zaloguj</button></p>
        </form>
    </section>
</main>
</body>
</html>
