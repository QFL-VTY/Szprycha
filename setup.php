<?php
session_start();

$blad = '';
$gotowe = false;
$zainstalowano = false;
$pdo = null;

if (!in_array('sqlite', PDO::getAvailableDrivers(), true)) {
    $blad = 'Brakuje rozszerzenia pdo_sqlite w PHP. Poproś administratora hostingu o jego włączenie.';
} else {
    try {
        require 'baza.php';
        $czyJestTabela = $pdo->query("SELECT name FROM sqlite_master WHERE type = 'table' AND name = 'uzytkownicy'")->fetchColumn();
        if ($czyJestTabela) {
            $zainstalowano = (int) $pdo->query('SELECT COUNT(*) FROM uzytkownicy')->fetchColumn() > 0;
        }
    } catch (PDOException $e) {
        $blad = 'Nie można otworzyć bazy. Sprawdź uprawnienia zapisu w katalogu projektu.';
    }
}

if (!$zainstalowano && $blad === '' && $_SERVER['REQUEST_METHOD'] === 'POST') {
    $haslo = is_string($_POST['haslo'] ?? null) ? $_POST['haslo'] : '';
    $powtorz = is_string($_POST['powtorz'] ?? null) ? $_POST['powtorz'] : '';
    $token = is_string($_POST['token'] ?? null) ? $_POST['token'] : '';

    if (!hash_equals($_SESSION['setup_token'] ?? '', $token) || $token === '') {
        $blad = 'Formularz wygasł. Odśwież stronę i spróbuj ponownie.';
    } elseif (strlen($haslo) < 8) {
        $blad = 'Hasło musi mieć przynajmniej 8 znaków.';
    } elseif ($haslo !== $powtorz) {
        $blad = 'Podane hasła są różne.';
    } else {
        try {
            $pdo->beginTransaction();

            $pdo->exec('CREATE TABLE IF NOT EXISTS uslugi (
                id INTEGER PRIMARY KEY AUTOINCREMENT,
                nazwa TEXT NOT NULL,
                czas TEXT NOT NULL DEFAULT "Do ustalenia",
                cena INTEGER NOT NULL CHECK (cena > 0)
            )');
            $pdo->exec('CREATE TABLE IF NOT EXISTS uzytkownicy (
                id INTEGER PRIMARY KEY AUTOINCREMENT,
                login TEXT NOT NULL UNIQUE,
                haslo_hash TEXT NOT NULL
            )');

            if ((int) $pdo->query('SELECT COUNT(*) FROM uzytkownicy')->fetchColumn() > 0) {
                $pdo->rollBack();
                $zainstalowano = true;
            } else {
                // Te same cztery uslugi co w projekcie z poprzedniego laboratorium.
                if ((int) $pdo->query('SELECT COUNT(*) FROM uslugi')->fetchColumn() === 0) {
                    $z = $pdo->prepare('INSERT INTO uslugi (nazwa, czas, cena) VALUES (:nazwa, :czas, :cena)');
                    $startowe = [
                        ['nazwa' => 'Przegląd podstawowy', 'czas' => '1 dzień', 'cena' => 120],
                        ['nazwa' => 'Przegląd generalny', 'czas' => '2–3 dni', 'cena' => 250],
                        ['nazwa' => 'Centrowanie koła', 'czas' => 'Od ręki / 1 dzień', 'cena' => 50],
                        ['nazwa' => 'Wymiana dętki / opony', 'czas' => 'Od ręki', 'cena' => 30]
                    ];
                    foreach ($startowe as $usluga) {
                        $z->execute($usluga);
                    }
                }

                $z = $pdo->prepare('INSERT INTO uzytkownicy (login, haslo_hash) VALUES (:login, :hash)');
                $z->execute([
                    'login' => 'admin',
                    'hash' => password_hash($haslo, PASSWORD_DEFAULT)
                ]);
                $pdo->commit();
                $gotowe = true;
                $zainstalowano = true;
                unset($_SESSION['setup_token']);
            }
        } catch (PDOException $e) {
            if ($pdo->inTransaction()) {
                $pdo->rollBack();
            }
            $blad = 'Nie udało się utworzyć bazy. Sprawdź uprawnienia zapisu do folderu projektu.';
        }
    }
}

if (!$zainstalowano && !isset($_SESSION['setup_token'])) {
    $_SESSION['setup_token'] = bin2hex(random_bytes(32));
}
?>
<!doctype html>
<html lang="pl">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Instalacja mini-CMS – Szprycha</title>
    <style>
        * { box-sizing: border-box; }
        body { margin: 0; font-family: Arial, sans-serif; background: #f7f7f7; color: #111; }
        header { background: #111; color: #ffcc00; border-bottom: 5px solid #ffcc00; padding: 20px; font-weight: 800; letter-spacing: 2px; text-transform: uppercase; }
        main { width: min(100% - 32px, 480px); margin: 64px auto; }
        h1 { border-left: 6px solid #ffcc00; padding-left: 12px; font-size: 25px; text-transform: uppercase; }
        .card { padding: 24px; background: #fff; border: 2px solid #111; }
        label { display: block; margin: 16px 0 6px; font-weight: bold; }
        input { width: 100%; padding: 11px; border: 1px solid #999; font: inherit; }
        button { width: 100%; margin-top: 22px; padding: 12px; background: #ffcc00; color: #111; border: 2px solid #111; font: inherit; font-weight: bold; cursor: pointer; }
        a { color: #111; }
        .alert { padding: 12px; margin-bottom: 16px; background: #fce2e2; border-left: 4px solid #b42318; }
        .success { background: #e4f4e9; border-left-color: #218848; }
        .muted { color: #555; line-height: 1.5; font-size: 14px; }
    </style>
</head>
<body>
<header>Szprycha</header>
<main>
    <h1>Instalacja mini-CMS</h1>
    <div class="card">
        <?php if ($blad !== ''): ?>
            <div class="alert" role="alert"><?= htmlspecialchars($blad, ENT_QUOTES, 'UTF-8') ?></div>
        <?php endif; ?>

        <?php if ($gotowe): ?>
            <div class="alert success">Baza gotowa. Konto <strong>admin</strong> zostało utworzone.</div>
            <p><a href="login.php">Przejdź do logowania</a></p>
            <p class="muted">Instalator został zablokowany. Na publicznym serwerze usuń lub ogranicz dostęp do setup.php oraz chroń plik szprycha.sqlite przed pobieraniem.</p>
        <?php elseif ($zainstalowano): ?>
            <div class="alert success">Baza jest już zainstalowana. Instalator został zablokowany.</div>
            <p><a href="login.php">Przejdź do logowania</a></p>
        <?php elseif ($blad !== '' && $pdo === null): ?>
            <p class="muted">Najpierw trzeba skonfigurować środowisko PHP.</p>
        <?php else: ?>
            <p>Ustaw hasło dla konta <strong>admin</strong>. Baza SQLite i cztery początkowe usługi zostaną utworzone automatycznie.</p>
            <form method="post">
                <input type="hidden" name="token" value="<?= htmlspecialchars($_SESSION['setup_token'], ENT_QUOTES, 'UTF-8') ?>">
                <label for="haslo">Hasło administratora (min. 8 znaków)</label>
                <input id="haslo" name="haslo" type="password" minlength="8" required autocomplete="new-password">
                <label for="powtorz">Powtórz hasło</label>
                <input id="powtorz" name="powtorz" type="password" minlength="8" required autocomplete="new-password">
                <button type="submit">Utwórz bazę i konto admin</button>
            </form>
            <p class="muted">Instalację wykonaj od razu po uruchomieniu strony. Po pierwszym utworzeniu konta formularz przestanie być dostępny.</p>
        <?php endif; ?>
    </div>
    <p><a href="index.php">← Wróć do strony</a></p>
</main>
</body>
</html>
