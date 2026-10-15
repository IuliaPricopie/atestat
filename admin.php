<?php
require_once 'db.php';

if (!isset($_SESSION['user_id']) || $_SESSION['rol'] !== 'admin') {
    die("Acces interzis! Doar administratorii pot accesa această pagină.");
}

$mesaj = '';
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['adauga_film'])) {
    $titlu = trim($_POST['titlu']);
    $descriere = trim($_POST['descriere']);
    $gen = trim($_POST['gen']);
    $durata = (int)$_POST['durata'];
    $poster = trim($_POST['poster']);

    $stmt = $pdo->prepare("INSERT INTO filme (titlu, descriere, gen, durata_minute, poster_url) VALUES (?, ?, ?, ?, ?)");
    $stmt->execute([$titlu, $descriere, $gen, $durata, $poster]);
    $mesaj = "Filmul a fost adăugat cu succes!";
}

$filme = $pdo->query("SELECT * FROM filme")->fetchAll(PDO::FETCH_ASSOC);
$sali = $pdo->query("SELECT * FROM sali")->fetchAll(PDO::FETCH_ASSOC);
?>
<!DOCTYPE html>
<html lang="ro">
<head>
    <meta charset="UTF-8">
    <title>Panou Admin - CineMax</title>
    <style>
        body { font-family: Arial; background: #121212; color: #fff; padding: 20px; }
        .container { max-width: 900px; margin: auto; background: #1e1e1e; padding: 20px; border-radius: 8px; border: 1px solid #333; }
        input, textarea, select { width: 100%; padding: 10px; margin: 8px 0; background: #2a2a2a; border: 1px solid #444; color: #fff; border-radius: 4px; box-sizing: border-box;}
        button { background: #28a745; color: white; border: none; padding: 10px 20px; cursor: pointer; border-radius: 4px; font-weight: bold; }
        button:hover { background: #218838; }
        table { width: 100%; border-collapse: collapse; margin-top: 20px; }
        th, td { padding: 10px; border: 1px solid #333; text-align: left; }
        th { background: #252525; }
    </style>
</head>
<body>
<div class="container">
    <h2>Panou Administrator - Gestionare Filme</h2>
    <p><a href="index.php" style="color: #ffc107; text-decoration: none;">&larr; Înapoi la Site</a></p>
    
    <?php if($mesaj) echo "<p style='color:#28a745; font-weight:bold;'>$mesaj</p>"; ?>

    <h3>Adaugă Film Nou</h3>
    <form method="POST">
        <input type="text" name="titlu" placeholder="Titlu film" required>
        <textarea name="descriere" placeholder="Descriere scurtă"></textarea>
        <input type="text" name="gen" placeholder="Gen (ex: Action / Drama)" required>
        <input type="number" name="durata" placeholder="Durată în minute" required>
        <input type="text" name="poster" placeholder="URL Poster imagine" required>
        <button type="submit" name="adauga_film">Salvează Filmul</button>
    </form>

    <h3 style="margin-top: 40px;">Filme Existente în Sistem</h3>
    <table>
        <tr>
            <th>ID</th>
            <th>Titlu</th>
            <th>Gen</th>
            <th>Durată</th>
        </tr>
        <?php foreach ($filme as $f): ?>
            <tr>
                <td><?= $f['id'] ?></td>
                <td><?= htmlspecialchars($f['titlu']) ?></td>
                <td><?= htmlspecialchars($f['gen']) ?></td>
                <td><?= $f['durata_minute'] ?> min</td>
            </tr>
        <?php endforeach; ?>
    </table>
</div>
</body>
</html>