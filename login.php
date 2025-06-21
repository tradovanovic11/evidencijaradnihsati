<?php
session_start();
include "baza.php";

$poruka = "";
$kor_ime = "";

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $kor_ime = $_POST['korisnicko_ime'];
    $lozinka = $_POST['lozinka'];

    $sql = "SELECT * FROM korisnici WHERE korisnicko_ime = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("s", $kor_ime);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows === 1) {
        $korisnik = $result->fetch_assoc();

        if (password_verify($lozinka, $korisnik['lozinka'])) {
            $_SESSION['korisnik_id'] = $korisnik['id'];
            $_SESSION['korisnicko_ime'] = $korisnik['korisnicko_ime'];
            $_SESSION['uloga'] = $korisnik['uloga'];

            if ($korisnik['uloga'] === 'admin') {
                header("Location: admin_pregled.php");
            } else {
                header("Location: radni_sati.php");
            }
            exit();
        } else {
            $poruka = "❌ Pogrešna lozinka ili korisničko ime.";
        }
    } else {
        $poruka = "❌ Pogrešna lozinka ili korisničko ime.";
    }
}
?>
<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <title>Prijava korisnika</title>
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
    </style>
</head>
<body>
<div class="dashboard" style="max-width: 420px; margin-top: 60px;">
    <div class="section-card">
        <h2 class="dashboard-title" style="font-size:2em; margin-bottom:26px;">Prijava</h2>
        <?php if ($poruka): ?>
            <div class="login-error"><?= $poruka ?></div>
        <?php endif; ?>
        <form action="" method="POST" class="vertical-form">
            <label for="korisnicko_ime">Korisničko ime:</label>
            <input type="text" name="korisnicko_ime" id="korisnicko_ime" required value="<?= htmlspecialchars($kor_ime) ?>">

            <label for="lozinka">Lozinka:</label>
            <input type="password" name="lozinka" id="lozinka" required>

            <div class="center-btns" style="margin-top:18px;">
                <input type="submit" class="main-btn" value="Prijavi se">
            </div>
        </form>
    </div>
</div>
</body>
</html>
