<?php
require_once 'db.php';
$mesaj = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['action']) && $_POST['action'] === 'register') {
        $nume = trim($_POST['nume']);
        $email = trim($_POST['email']);
        $parola = password_hash($_POST['parola'], PASSWORD_DEFAULT);

        try {
            $stmt = $pdo->prepare("INSERT INTO utilizatori (nume, email, parola) VALUES (?, ?, ?)");
            $stmt->execute([$nume, $email, $parola]);
            $mesaj = "Cont creat cu succes! Te poți autentifica.";
        } catch (PDOException $e) {
            $mesaj = "Eroare: Acest email este deja folosit.";
        }
    } elseif (isset($_POST['action']) && $_POST['action'] === 'login') {
        $email = trim($_POST['email']);
        $parola = $_POST['parola'];

        $stmt = $pdo->prepare("SELECT * FROM utilizatori WHERE email = ?");
        $stmt->execute([$email]);
        $user = $stmt->fetch(PDO::FETCH_ASSOC);

        if ($user && password_verify($parola, $user['parola'])) {
            $_SESSION['user_id'] = $user['id'];
            $_SESSION['nume'] = $user['nume'];
            $_SESSION['rol'] = $user['rol'];
            header("Location: index.php");
            exit();
        } else {
            $mesaj = "Email sau parolă incorecte!";
        }
    }
}
?>
<!DOCTYPE html>
<html lang="ro">
<head>
    <meta charset="UTF-8">
    <title>Autentificare - CineMax</title>
    <style>
        body { background: #121212; color: #fff; font-family: Arial; display: flex; justify-content: center; align-items: center; height: 100vh; margin: 0; }
        .box { background: #1e1e1e; padding: 30px; border-radius: 8px; width: 320px; border: 1px solid #333; }
        input { width: 100%; padding: 10px; margin: 10px 0; background: #2a2a2a; border: 1px solid #444; color: #fff; border-radius: 4px; box-sizing: border-box;}
        button { width: 100%; padding: 10px; background: #e50914; border: none; color: white; font-weight: bold; border-radius: 4px; cursor: pointer; }
        button:hover { background: #b20710; }
        .mesaj { color: #28a745; margin-bottom: 10px; font-size: 14px; }
    </style>
</head>
<body>
<div class="box">
    <h2>Autentificare</h2>
    <?php if($mesaj) echo "<p class='mesaj'>$mesaj</p>"; ?>
    <form method="POST">
        <input type="hidden" name="action" value="login">
        <input type="email" name="email" placeholder="Email" required>
        <input type="password" name="parola" placeholder="Parolă" required>
        <button type="submit">Conectare</button>
    </form>

    <hr style="border-color: #333; margin: 20px 0;">

    <h2>Înregistrare</h2>
    <form method="POST">
        <input type="hidden" name="action" value="register">
        <input type="text" name="nume" placeholder="Nume complet" required>
        <input type="email" name="email" placeholder="Email" required>
        <input type="password" name="parola" placeholder="Parolă" required>
        <button type="submit" style="background: #28a745;">Creează Cont</button>
    </form>
    <p style="text-align:center; margin-top:15px;"><a href="index.php" style="color:#aaa; text-decoration:none;">&larr; Înapoi la Acasă</a></p>
</div>
</body>
</html>