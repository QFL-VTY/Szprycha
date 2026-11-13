<?php
$firma = 'Szprycha';
$rok = date('Y');

$cennik = [
    ['nazwa' => 'Przegląd podstawowy', 'czas' => '1 dzień', 'cena' => 120],
    ['nazwa' => 'Przegląd generalny', 'czas' => '2–3 dni', 'cena' => 250],
    ['nazwa' => 'Centrowanie koła', 'czas' => 'Od ręki / 1 dzień', 'cena' => 50],
    ['nazwa' => 'Regulacja przerzutek lub hamulców', 'czas' => 'Od ręki', 'cena' => 40],
    ['nazwa' => 'Wymiana dętki / opony', 'czas' => 'Od ręki', 'cena' => 30]
];
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
        <div class="brand">
            <h1>Szprycha</h1>
            <p>Twoja lokalna pracownia rowerowa</p>
        </div>
        
        <button class="menu-toggle" aria-expanded="false" aria-label="Menu">
            ☰ Menu
        </button>

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
                    <tr>
                        <th>Usługa</th>
                        <th>Czas realizacji</th>
                        <th>Cena</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($cennik as $usluga): ?>
                        <tr>
                            <td><?= htmlspecialchars($usluga['nazwa']); ?></td>
                            <td><?= htmlspecialchars($usluga['czas']); ?></td>
                            <td><?= $usluga['cena']; ?> zł</td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </section>
    </main>

    <footer id="kontakt">
        <h2>Kontakt</h2>
        <address>
            <p><strong> Pracownia Rowerowa „Szprycha”</strong></p>
            <p>ul. Rowerowa 12, 00-001 Warszawa</p>
            <p>Tel.: <a href="tel:+48123456789">+48 123 456 789</a></p>
            <p>E-mail: <a href="mailto:kontakt@szprycha.pl">kontakt@szprycha.pl</a></p>
            <p>Godziny otwarcia: Pon–Pt 10:00–18:00, Sob 10:00–14:00</p>
        </address>
        <p class="copyright">&copy; <?= $rok; ?> <?= $firma; ?>. Wszelkie prawa zastrzeżone.</p>
    </footer>

</body>
</html>