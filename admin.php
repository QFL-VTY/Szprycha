<?php
require 'auth.php';
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
    </section>
</main>
</body>
</html>
