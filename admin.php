<?php
require 'auth.php';
require 'baza.php';

$blad = '';
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $akcja = $_POST['akcja'] ?? '';

    if ($akcja === 'dodaj') {
        $nazwa = trim($_POST['nazwa'] ?? '');
        $czas = trim(is_string($_POST['czas'] ?? null) ? $_POST['czas'] : '');
        $cena = (int) ($_POST['cena'] ?? 0);
        if ($czas === '') {
            $czas = 'Do ustalenia';
        }

        if ($nazwa !== '' && strlen($nazwa) <= 100 && strlen($czas) <= 80 && $cena > 0) {
            $z = $pdo->prepare('INSERT INTO uslugi (nazwa, czas, cena) VALUES (:nazwa, :czas, :cena)');
            $z->execute(['nazwa' => $nazwa, 'czas' => $czas, 'cena' => $cena]);
            header('Location: admin.php?ok=dodano');
            exit;
        }
        $blad = 'Podaj nazwę (do 100 znaków), czas (do 80 znaków) i cenę większą od zera.';
    }

    if ($akcja === 'edytuj_czas') {
        $id = (int) ($_POST['id'] ?? 0);
        $czas = trim(is_string($_POST['czas'] ?? null) ? $_POST['czas'] : '');

        if ($id > 0 && $czas !== '' && strlen($czas) <= 80) {
            $z = $pdo->prepare('UPDATE uslugi SET czas = :czas WHERE id = :id');
            $z->execute(['czas' => $czas, 'id' => $id]);
            header('Location: admin.php?ok=zmieniono_czas');
            exit;
        }
        $blad = 'Podaj czas realizacji (maksymalnie 80 znaków).';
    }

    if ($akcja === 'usun') {
        $id = (int) ($_POST['id'] ?? 0);
        if ($id > 0) {
            $z = $pdo->prepare('DELETE FROM uslugi WHERE id = :id');
            $z->execute(['id' => $id]);
            header('Location: admin.php?ok=usunieto');
            exit;
        }
        $blad = 'Nieprawidłowy identyfikator usługi.';
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
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css"
          integrity="sha384-QWTKZyjpPEjISv5WaRU9OFeRpok6YctnYmDr5pNlyT2bRjXh0JMhjY6hW+ALEwIH"
          crossorigin="anonymous">
    <link rel="stylesheet" href="panel.css">
</head>
<body class="panel-body">
<nav class="navbar navbar-dark navbar-szprycha">
    <div class="container d-flex justify-content-between">
        <a class="navbar-brand d-flex align-items-center gap-2" href="index.php">
            <svg viewBox="0 0 120 64" width="54" height="30" aria-hidden="true">
                <circle cx="28" cy="42" r="16" fill="none" stroke="#ffcc00" stroke-width="4"/>
                <circle cx="92" cy="42" r="16" fill="none" stroke="#ffcc00" stroke-width="4"/>
                <polyline points="28,42 50,18 60,42 28,42 50,18 76,18 60,42" fill="none" stroke="#fff" stroke-width="4" stroke-linejoin="round"/>
                <polyline points="76,18 92,42" fill="none" stroke="#fff" stroke-width="4"/>
            </svg>
            Szprycha
        </a>
        <form action="wyloguj.php" method="post">
            <button type="submit" class="btn btn-outline-light btn-sm">Wyloguj</button>
        </form>
    </div>
</nav>
<main class="container py-5">
    <div class="d-flex justify-content-between align-items-center flex-wrap gap-3 mb-4">
        <div>
            <h1 class="h3 panel-title mb-2">Panel administratora</h1>
            <p class="text-secondary mb-0">Witaj, <?= htmlspecialchars($_SESSION['uzytkownik'], ENT_QUOTES, 'UTF-8') ?>.</p>
        </div>
        <a class="btn btn-outline-dark" href="index.php#cennik">Zobacz cennik</a>
    </div>

    <?php if (($_GET['ok'] ?? '') === 'dodano'): ?>
        <div class="alert alert-success" role="status">Usługa dodana.</div>
    <?php elseif (($_GET['ok'] ?? '') === 'usunieto'): ?>
        <div class="alert alert-success" role="status">Usługa usunięta.</div>
    <?php elseif (($_GET['ok'] ?? '') === 'zmieniono_czas'): ?>
        <div class="alert alert-success" role="status">Czas realizacji zapisany.</div>
    <?php endif; ?>
    <?php if ($blad): ?>
        <div class="alert alert-danger" role="alert"><?= htmlspecialchars($blad, ENT_QUOTES, 'UTF-8') ?></div>
    <?php endif; ?>

    <div class="row g-4">
        <div class="col-lg-8">
            <div class="card panel-card shadow-sm">
                <div class="card-header">Lista usług</div>
                <div class="card-body p-0">
                    <div class="table-responsive">
                        <table class="table table-striped mb-0">
                            <thead><tr><th>Usługa</th><th>Czas realizacji</th><th>Cena</th><th>Akcja</th></tr></thead>
                            <tbody>
                            <?php foreach ($uslugi as $usluga): ?>
                                <tr>
                                    <td><?= htmlspecialchars($usluga['nazwa'], ENT_QUOTES, 'UTF-8') ?></td>
                                    <td>
                                        <form method="post" class="d-flex align-items-center gap-2">
                                            <input type="hidden" name="akcja" value="edytuj_czas">
                                            <input type="hidden" name="id" value="<?= (int) $usluga['id'] ?>">
                                            <input name="czas" class="form-control form-control-sm" style="min-width: 150px"
                                                   value="<?= htmlspecialchars($usluga['czas'], ENT_QUOTES, 'UTF-8') ?>"
                                                   aria-label="Czas realizacji: <?= htmlspecialchars($usluga['nazwa'], ENT_QUOTES, 'UTF-8') ?>"
                                                   maxlength="80" required>
                                            <button type="submit" class="btn btn-sm btn-outline-dark text-nowrap">Zapisz</button>
                                        </form>
                                    </td>
                                    <td class="text-nowrap"><?= (int) $usluga['cena'] ?> zł</td>
                                    <td>
                                        <form method="post" class="d-inline">
                                            <input type="hidden" name="akcja" value="usun">
                                            <input type="hidden" name="id" value="<?= (int) $usluga['id'] ?>">
                                            <button type="submit" class="btn btn-sm btn-outline-danger">Usuń</button>
                                        </form>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>
                    <?php if (!$uslugi): ?>
                        <p class="p-3 mb-0">Nie ma jeszcze żadnych usług.</p>
                    <?php endif; ?>
                </div>
            </div>
        </div>
        <div class="col-lg-4">
            <div class="card panel-card shadow-sm">
                <div class="card-header">Dodaj usługę</div>
                <div class="card-body">
                    <form method="post">
                        <input type="hidden" name="akcja" value="dodaj">
                        <div class="mb-3">
                            <label for="nazwa" class="form-label">Nazwa</label>
                            <input id="nazwa" name="nazwa" class="form-control" required maxlength="100">
                        </div>
                        <div class="mb-3">
                            <label for="czas" class="form-label">Czas realizacji</label>
                            <input id="czas" name="czas" class="form-control" maxlength="80"
                                   placeholder="np. 1 dzień (opcjonalnie)">
                        </div>
                        <div class="mb-3">
                            <label for="cena" class="form-label">Cena (zł)</label>
                            <input id="cena" name="cena" type="number" class="form-control" min="1" required>
                        </div>
                        <button type="submit" class="btn btn-primary w-100">Dodaj</button>
                    </form>
                    <small class="text-secondary d-block mt-3">Pusty czas realizacji = „Do ustalenia”.</small>
                </div>
            </div>
        </div>
    </div>
</main>
</body>
</html>
