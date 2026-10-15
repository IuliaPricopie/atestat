<?php
require_once 'db.php';
$filme = $pdo->query("SELECT * FROM filme")->fetchAll(PDO::FETCH_ASSOC);
$proiectii = $pdo->query("SELECT p.*, f.titlu, s.nume as sala FROM proiectii p JOIN filme f ON p.film_id = f.id JOIN sali s ON p.sala_id = s.id")->fetchAll(PDO::FETCH_ASSOC);
?>
<!DOCTYPE html>
<html lang="ro">
<head>
    <meta charset="UTF-8">
    <title>CineMax - Platformă Cinematograf</title>
    <style>
        body { font-family: Arial, sans-serif; margin: 0; background-color: #121212; color: #e0e0e0; }
        header { background-color: #1f1f1f; display: flex; justify-content: space-between; align-items: center; padding: 15px 50px; border-bottom: 2px solid #e50914; }
        .logo { font-size: 24px; font-weight: bold; color: #e50914; }
        nav a { color: #fff; text-decoration: none; margin-left: 20px; font-weight: bold; }
        nav a:hover { color: #e50914; }
        .container { max-width: 1100px; margin: 30px auto; padding: 0 20px; }
        .grid { display: flex; gap: 20px; flex-wrap: map; margin-top: 20px; }
        .card { background-color: #1e1e1e; border-radius: 8px; width: 250px; overflow: hidden; border: 1px solid #333; }
        .card img { width: 100%; height: 320px; object-fit: cover; }
        .card-info { padding: 15px; }
        .btn { background-color: #e50914; color: white; border: none; padding: 8px 12px; border-radius: 4px; cursor: pointer; text-decoration: none; display: inline-block; margin-top: 10px;}
        .btn:hover { background-color: #b20710; }
        table { width: 100%; border-collapse: collapse; margin-top: 15px; background: #1e1e1e; }
        th, td { padding: 12px; border: 1px solid #333; text-align: left; }
        th { background: #252525; }
    </style>
</head>
<body>
    <header>
        <div class="logo">🎬 CineMax</div>
        <nav>
            <a href="index.php">Acasă</a>
            <?php if (isset($_SESSION['user_id'])): ?>
                <span>Salut, <b><?= htmlspecialchars($_SESSION['nume']) ?></b></span>
                <?php if ($_SESSION['rol'] === 'admin'): ?>
                    <a href="admin.php" style="color: #ffc107;">Panou Admin</a>
                <?php endif; ?>
                <a href="logout.php">Deconectare</a>
            <?php else: ?>
                <a href="autentificare.php">Autentificare / Cont</a>
            <?php endif; ?>
        </nav>
    </header>

    <main class="container">
        <h2>Filme în Program</h2>
        <div class="grid">
            <?php foreach ($filme as $f): ?>
                <div class="card">
                    <img src="<?= htmlspecialchars($f['poster_url']) ?>" alt="Poster">
                    <div class="card-info">
                        <h3><?= htmlspecialchars($f['titlu']) ?></h3>
                        <p><b>Gen:</b> <?= htmlspecialchars($f['gen']) ?></p>
                        <p><b>Durată:</b> <?= $f['durata_minute'] ?> min</p>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>

        <h2 style="margin-top: 50px;">Proiecții Disponibile</h2>
        <table>
            <tr>
                <th>Film</th>
                <th>Sală</th>
                <th>Dată și Oră</th>
                <th>Preț</th>
                <th>Acțiune</th>
            </tr>
            <?php foreach ($proiectii as $p): ?>
                <tr>
                    <td><?= htmlspecialchars($p['titlu']) ?></td>
                    <td><?= htmlspecialchars($p['sala']) ?></td>
                    <td><?= $p['inceput_la'] ?></td>
                    <td><?= $p['pret'] ?> RON</td>
                    <td><a href="rezervare.php?proiectie_id=<?= $p['id'] ?>" class="btn">Rezervă Locuri</a></td>
                </tr>
            <?php endforeach; ?>
        </table>
    </main>
</body>
</html>