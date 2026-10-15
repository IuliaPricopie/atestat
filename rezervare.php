<?php
require_once 'db.php';

if (!isset($_SESSION['user_id'])) {
    header("Location: autentificare.php");
    exit();
}

$proiectie_id = $_GET['proiectie_id'] ?? null;
if (!$proiectie_id) {
    header("Location: index.php");
    exit();
}

// Preluăm detalii proiecție și sală
$stmt = $pdo->prepare("SELECT p.*, f.titlu, s.nume as sala_nume, s.randuri, s.coloane 
                       FROM proiectii p 
                       JOIN filme f ON p.film_id = f.id 
                       JOIN sali s ON p.sala_id = s.id 
                       WHERE p.id = ?");
$stmt->execute([$proiectie_id]);
$proiectie = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$proiectie) {
    die("Proiecția negăsită.");
}

// Preluăm locurile deja ocupate pentru această proiecție
$stmt_ocupate = $pdo->prepare("SELECT lr.rand_loc, lr.numar_loc 
                               FROM locuri_rezervate lr 
                               JOIN rezervari r ON lr.rezervare_id = r.id 
                               WHERE r.proiectie_id = ?");
$stmt_ocupate->execute([$proiectie_id]);
$ocupate = $stmt_ocupate->fetchAll(PDO::FETCH_ASSOC);
$map_ocupate = [];
foreach ($ocupate as $o) {
    $map_ocupate[$o['rand_loc'] . '-' . $o['numar_loc']] = true;
}

// Procesare rezervare trimisă din formular
$mesaj = '';
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $locuri_alese = $_POST['locuri'] ?? [];
    if (empty($locuri_alese)) {
        $mesaj = "Nu ai selectat niciun loc!";
    } else {
        $total = count($locuri_alese) * $proiectie['pret'];
        
        // Salvăm rezervarea în tranzacție
        $pdo->beginTransaction();
        try {
            $stmt_rez = $pdo->prepare("INSERT INTO rezervari (utilizator_id, proiectie_id, total) VALUES (?, ?, ?)");
            $stmt_rez->execute([$_SESSION['user_id'], $proiectie_id, $total]);
            $rezervare_id = $pdo->lastInsertId();

            $stmt_loc = $pdo->prepare("INSERT INTO locuri_rezervate (rezervare_id, rand_loc, numar_loc) VALUES (?, ?, ?)");
            foreach ($locuri_alese as $loc_str) {
                list($rand, $numar) = explode('-', $loc_str);
                $stmt_loc->execute([$rezervare_id, $rand, $numar]);
            }

            $pdo->commit();
            $mesaj = "Rezervare confirmată cu succes! Cost total: $total RON.";
        } catch (Exception $e) {
            $pdo->rollBack();
            $mesaj = "Eroare la rezervare (posibil ca unul din locuri să fi fost ocupat între timp).";
        }
    }
}
?>
<!DOCTYPE html>
<html lang="ro">
<head>
    <meta charset="UTF-8">
    <title>Rezervare Locuri - CineMax</title>
    <style>
        body { font-family: Arial; background: #121212; color: #fff; text-align: center; margin: 0; padding: 20px; }
        .screen { background: #555; width: 60%; margin: 20px auto; padding: 5px; border-radius: 4px; font-weight: bold; letter-spacing: 2px;}
        .sala-grid { display: inline-block; margin: 20px auto; }
        .rand { margin-bottom: 10px; }
        .loc { display: inline-block; width: 35px; height: 35px; line-height: 35px; margin: 5px; background: #28a745; border-radius: 4px; cursor: pointer; font-size: 14px; font-weight: bold;}
        .loc.ocupat { background: #dc3545; cursor: not-allowed; }
        .loc.selectat { background: #ffc107; color: #000; }
        input[type="checkbox"] { display: none; }
        .btn { background: #e50914; color: #fff; padding: 10px 20px; border: none; font-size: 16px; border-radius: 4px; cursor: pointer; margin-top: 20px;}
        .btn:hover { background: #b20710; }
    </style>
</head>
<body>
    <h1><?= htmlspecialchars($proiectie['titlu']) ?></h1>
    <p>Sală: <b><?= htmlspecialchars($proiectie['sala_nume']) ?></b> | Dată: <?= $proiectie['inceput_la'] ?> | Preț bilet: <?= $proiectie['pret'] ?> RON</p>
    
    <div class="screen">ECRAN</div>

    <?php if($mesaj): ?>
        <p style="font-size: 18px; font-weight: bold; color: #ffc107;"><?= $mesaj ?></p>
        <p><a href="index.php" style="color: #fff;">Înapoi la program</a></p>
    <?php else: ?>
        <form method="POST">
            <div class="sala-grid">
                <?php for ($r = 1; $r <= $proiectie['randuri']; $r++): ?>
                    <div class="rand">
                        <span>Rândul <?= $r ?>: </span>
                        <?php for ($c = 1; $c <= $proiectie['coloane']; $c++): ?>
                            <?php 
                                $is_ocupat = isset($map_ocupate["$r-$c"]);
                            ?>
                            <label>
                                <input type="checkbox" name="locuri[]" value="<?= "$r-$c" ?>" <?= $is_ocupat ? 'disabled' : '' ?> onchange="toggleLoc(this)">
                                <span class="loc <?= $is_ocupat ? 'ocupat' : '' ?>"><?= $c ?></span>
                            </label>
                        <?php endfor; ?>
                    </div>
                <?php endfor; ?>
            </div>
            <br>
            <button type="submit" class="btn">Confirmă Rezervarea</button>
        </form>
        <p><a href="index.php" style="color: #aaa; text-decoration: none;">&larr; Anulează</a></p>
    <?php endif; ?>

    <script>
        function toggleLoc(checkbox) {
            let span = checkbox.nextElementSibling;
            if (checkbox.checked) {
                span.classList.add('selectat');
            } else {
                span.classList.remove('selectat');
            }
        }
    </script>
</body>
</html>