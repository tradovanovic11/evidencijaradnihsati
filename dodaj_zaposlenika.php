<?php
session_start();
include "baza.php";

// provjera uloge, samo admin pristup
if (!isset($_SESSION['korisnik_id']) || $_SESSION['uloga'] !== 'admin') {
    header("Location: index.php");
    exit();
}

$poruka = "";
$novo_korime = "";
$nova_lozinka = "";

// Generiranje korisničkog imena i lozinke
function generiraj_korime($ime, $prezime, $conn) {
    $osnovno = strtolower(substr($ime,0,1) . $prezime);
    $korime = $osnovno;
    $i = 1;
    while (true) {
        $stmt = $conn->prepare("SELECT id FROM korisnici WHERE korisnicko_ime = ?");
        $stmt->bind_param("s", $korime);
        $stmt->execute();
        $stmt->store_result();
        if ($stmt->num_rows == 0) break;
        $korime = $osnovno . $i;
        $i++;
    }
    return $korime;
}

function generiraj_lozinku($duljina = 8) {
    $chars = 'abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789';
    return substr(str_shuffle($chars), 0, $duljina);
}

// Dodavanje zaposlenika
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $ime = trim($_POST['ime']);
    $prezime = trim($_POST['prezime']);
    $uloga = $_POST['uloga'];

    $novo_korime = generiraj_korime($ime, $prezime, $conn);
    $nova_lozinka = generiraj_lozinku(10);
    $hash_lozinka = password_hash($nova_lozinka, PASSWORD_DEFAULT);

    $stmt = $conn->prepare("INSERT INTO korisnici (korisnicko_ime, lozinka, ime, prezime, uloga) VALUES (?, ?, ?, ?, ?)");
    $stmt->bind_param("sssss", $novo_korime, $hash_lozinka, $ime, $prezime, $uloga);


    if ($stmt->execute()) {
        $poruka = "✅ Novi zaposlenik dodan!<br>Korisničko ime: <b>$novo_korime</b><br>Lozinka: <b>$nova_lozinka</b>";
    } else {
        $poruka = "❌ Greška pri dodavanju zaposlenika.";
    }
}
?>
<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <title>Dodaj zaposlenika</title>
    <link rel="stylesheet" href="dizajn.css">
</head>
<body>
<div class="dashboard">
    <h2 class="dashboard-title">👤 Dodaj novog zaposlenika</h2>
    <div class="section-card" style="max-width:500px; margin:0 auto;">
        <?php if ($poruka): ?>
            <div class="status-box"><?= $poruka ?></div>
        <?php endif; ?>
        <form method="POST" class="vertical-form">
            <label for="ime">Ime:</label>
            <input type="text" name="ime" id="ime" required>
            <label for="prezime">Prezime:</label>
            <input type="text" name="prezime" id="prezime" required>
            <label for="uloga">Uloga:</label>
                <select name="uloga" id="uloga" required>
                    <option value="zaposlenik">Zaposlenik</option>
                    <option value="admin">Administrator</option>
                </select>

            <div class="center-btns">
                <input type="submit" class="main-btn" value="➕ Dodaj zaposlenika">
                <a href="admin_pregled.php" class="main-btn">⬅ Natrag</a>
            </div>
        </form>
    </div>
</div>
</body>
</html>
