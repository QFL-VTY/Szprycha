<?php
require 'auth.php';
require 'baza.php';

$blad = '';
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $akcja = $_POST['akcja'] ?? '';

    if ($akcja === 'dodaj') {
        $nazwa = trim($_POST['nazwa'] ?? '');
        $cena = (int) ($_POST['cena'] ?? 0);

        if ($nazwa !== '' && strlen($nazwa) <= 100 && $cena > 0) {
            $z = $pdo->prepare('INSERT INTO uslugi (nazwa, cena) VALUES (:nazwa, :cena)');
            $z->execute(['nazwa' => $nazwa, 'cena' => $cena]);
            header('Location: admin.php?ok=dodano');
            exit;
        }
        $blad = 'Podaj nazwę (do 100 znaków) oraz cenę większą od zera.';
    }
}

$uslugi = $pdo->query('SELECT id, nazwa, czas, cena FROM uslugi ORDER BY nazwa')->fetchAll();
?>
<!DOCTYPE html>
<html lang="pl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Panel admina – Szprycha</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>
<header>
    <a class="logo" href="index.php"><span>Szprycha</span></a>
    <form action="wyloguj.php" method="post"><button type="submit">Wyloguj</button></form>
</header>
<main>
    <section>
        <h2>Panel administratora</h2>
        <p>Witaj, <?= htmlspecialchars($_SESSION['uzytkownik'], ENT_QUOTES, 'UTF-8') ?>.</p>
        <?php if (($_GET['ok'] ?? '') === 'dodano'): ?>
            <p role="status">Usługa dodana.</p>
        <?php endif; ?>
        <?php if ($blad): ?>
            <p role="alert"><?= htmlspecialchars($blad, ENT_QUOTES, 'UTF-8') ?></p>
        <?php endif; ?>
    </section>
    <section>
        <h2>Lista usług</h2>
        <table>
            <thead><tr><th>Usługa</th><th>Czas</th><th>Cena</th></tr></thead>
            <tbody>
            <?php foreach ($uslugi as $usluga): ?>
                <tr>
                    <td><?= htmlspecialchars($usluga['nazwa'], ENT_QUOTES, 'UTF-8') ?></td>
                    <td><?= htmlspecialchars($usluga['czas'], ENT_QUOTES, 'UTF-8') ?></td>
                    <td><?= (int) $usluga['cena'] ?> zł</td>
                </tr>
            <?php endforeach; ?>
            </tbody>
        </table>
    </section>
    <section>
        <h2>Dodaj usługę</h2>
        <form method="post">
            <input type="hidden" name="akcja" value="dodaj">
            <p><label for="nazwa">Nazwa</label><br><input id="nazwa" name="nazwa" required maxlength="100"></p>
            <p><label for="cena">Cena (zł)</label><br><input id="cena" name="cena" type="number" min="1" required></p>
            <p><button type="submit">Dodaj</button></p>
        </form>
    </section>
</main>
</body>
</html>
