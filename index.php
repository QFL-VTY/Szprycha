<?php
$firma = 'Szprycha';
$rok = date('Y');
require 'baza.php';
$uslugi = $pdo->query('SELECT nazwa, czas, cena FROM uslugi ORDER BY cena')->fetchAll();
?>
<!DOCTYPE html>
<html lang="pl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Szprycha – Pracownia Rowerowa</title>
    <link rel="stylesheet" href="style.css">
    <script src="app.js" defer></script>
</head>
<body>
<header>
    <a class="logo" href="#">
        <svg viewBox="0 0 120 64" width="72" height="38" role="img" aria-labelledby="logo-tytul">
            <title id="logo-tytul">Logo rowerowe Szprycha</title>
            <circle cx="28" cy="42" r="16" fill="none" stroke="#ffcc00" stroke-width="4"/>
            <circle cx="92" cy="42" r="16" fill="none" stroke="#ffcc00" stroke-width="4"/>
            <polyline points="28,42 50,18 60,42 28,42" fill="none" stroke="#ffffff" stroke-width="4" stroke-linejoin="round"/>
            <polyline points="50,18 76,18 60,42 50,18" fill="none" stroke="#ffffff" stroke-width="4" stroke-linejoin="round"/>
            <polyline points="76,18 92,42" fill="none" stroke="#ffffff" stroke-width="4" stroke-linejoin="round"/>
        </svg>
        <span>Szprycha</span>
    </a>
    <button class="menu-toggle" aria-expanded="false" aria-label="Menu">☰ Menu</button>
    <nav>
        <ul class="nav-list">
            <li><a href="#oferta">Oferta</a></li>
            <li><a href="#cennik">Cennik</a></li>
            <li><a href="#kontakt">Kontakt</a></li>
        </ul>
    </nav>
</header>
<main>
    <section id="oferta">
        <h2>Nasza oferta</h2>
        <div class="cards-container">
            <article class="card">
                <h3>Przegląd podstawowy</h3>
                <p>Regulacja przerzutek i hamulców, smarowanie łańcucha oraz kontrola ciśnienia w oponach.</p>
            </article>
            <article class="card card--highlight">
                <h3>Przegląd generalny (Polecany)</h3>
                <p>Kompleksowy serwis: rozebranie, czyszczenie i smarowanie wszystkich podzespołów, centrowanie kół.</p>
            </article>
            <article class="card">
                <h3>Szybkie naprawy</h3>
                <p>Wymiana dętki, opony, linek, klocków hamulcowych oraz montaż dodatkowych akcesoriów.</p>
            </article>
        </div>
    </section>
    <section id="cennik">
        <h2>Cennik usług</h2>
        <table>
            <thead>
            <tr><th>Usługa</th><th>Czas realizacji</th><th>Cena</th></tr>
            </thead>
            <tbody>
            <?php foreach ($uslugi as $usluga): ?>
                <tr>
                    <td><?= htmlspecialchars($usluga['nazwa'], ENT_QUOTES, 'UTF-8'); ?></td>
                    <td><?= htmlspecialchars($usluga['czas'], ENT_QUOTES, 'UTF-8'); ?></td>
                    <td><?= (int) $usluga['cena']; ?> zł</td>
                </tr>
            <?php endforeach; ?>
            </tbody>
        </table>
    </section>
</main>
<footer id="kontakt">
    <h2>Kontakt</h2>
    <address>
        <p><strong>Pracownia Rowerowa „Szprycha”</strong></p>
        <p>ul. Rowerowa 12, 00-001 Warszawa</p>
        <p>Tel.: <a href="tel:+48123456789">+48 123 456 789</a></p>
        <p>E-mail: <a href="mailto:kontakt@szprycha.pl">kontakt@szprycha.pl</a></p>
        <p>Godziny otwarcia: Pon–Pt 10:00–18:00, Sob 10:00–14:00</p>
    </address>
    <p class="copyright">&copy; <?= $rok; ?> <?= $firma; ?>. Wszelkie prawa zastrzeżone.</p>
</footer>
</body>
</html>
