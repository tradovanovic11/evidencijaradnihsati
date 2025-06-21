<?php
session_start();
include "baza.php";

if (!isset($_SESSION['korisnik_id'])) {
    header("Location: prijava.php");
    exit();
}

$poruka = "";

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $stara = $_POST['stara_lozinka'];
    $nova = $_POST['nova_lozinka'];
    $potvrda = $_POST['potvrda_lozinke'];
    $korisnik_id = $_SESSION['korisnik_id'];
  
    $stmt = $conn->prepare("SELECT lozinka FROM korisnici WHERE id = ?");
    $stmt->bind_param("i", $korisnik_id);
    $stmt->execute();
    $stmt->bind_result($hash_lozinka);
    $stmt->fetch();
    $stmt->close();

    if (!password_verify($stara, $hash_lozinka)) {
        $poruka = "❌ Stara lozinka nije ispravna.";
    } elseif ($nova !== $potvrda) {
        $poruka = "❌ Nova lozinka i potvrda se ne podudaraju.";
    } elseif (strlen($nova) < 6) {
        $poruka = "❌ Nova lozinka mora imati barem 6 znakova.";
    } else {
        $nova_hash = password_hash($nova, PASSWORD_DEFAULT);
        $stmt = $conn->prepare("UPDATE korisnici SET lozinka = ? WHERE id = ?");
        $stmt->bind_param("si", $nova_hash, $korisnik_id);
        if ($stmt->execute()) {
            $poruka = "✅ Lozinka uspješno promijenjena.";
        } else {
            $poruka = "❌ Došlo je do pogreške. Pokušajte ponovno.";
        }
        $stmt->close();
    }
}
$back_link = ($_SESSION['uloga'] === 'admin') ? 'admin_pregled.php' : 'radni_sati.php';

?>
<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <title>Promjena lozinke</title>
    <link rel="stylesheet" href="dizajn.css">
    <style>
    .login-error {
        color: #d32f2f;
        background: #fbeaea;
        border-radius: 5px;
        font-size: 14px;
        padding: 8px 12px;
        margin-bottom: 18px;
        text-align: center;
    }
    .login-success {
        color: #2e7d32;
        background: #e9f5e1;
        border-radius: 5px;
        font-size: 14px;
        padding: 8px 12px;
        margin-bottom: 18px;
        text-align: center;
    }
    </style>
</head>
<body>
<div class="dashboard" style="max-width: 420px; margin-top: 60px;">
    <div class="section-card">
        <h2 class="dashboard-title" style="font-size:2em; margin-bottom:26px;">Promjena lozinke</h2>
        <?php if ($poruka): ?>
            <div class="<?= strpos($poruka, '✅') === 0 ? 'login-success' : 'login-error' ?>"><?= $poruka ?></div>
        <?php endif; ?>
        <form method="POST" class="vertical-form">
            <label for="stara_lozinka">Stara lozinka:</label>
            <input type="password" name="stara_lozinka" id="stara_lozinka" required>

            <label for="nova_lozinka">Nova lozinka:</label>
            <input type="password" name="nova_lozinka" id="nova_lozinka" required>

            <label for="potvrda_lozinke">Potvrdi novu lozinku:</label>
            <input type="password" name="potvrda_lozinke" id="potvrda_lozinke" required>

            <div class="center-btns" style="margin-top:18px;">
                <input type="submit" class="main-btn" value="Promijeni lozinku">
                <a href="<?= $back_link ?>" class="main-btn">⬅ Natrag</a>
            </div>
        </form>
    </div>
</div>
</body>
</html>
