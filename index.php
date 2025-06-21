<?php
session_start();
$poruka = "";


if (isset($_GET['error'])) {
    if ($_GET['error'] === "1") {
        $poruka = "❌ Pogrešno korisničko ime ili lozinka.";
    } elseif ($_GET['error'] === "2") {
        $poruka = "❌ Morate se prijaviti za pristup sustavu.";
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
        <form action="login.php" method="POST" class="vertical-form">
            <label for="korisnicko_ime">Korisničko ime:</label>
            <input type="text" name="korisnicko_ime" id="korisnicko_ime" required>

            <label for="lozinka">Lozinka:</label>
            <input type="password" name="lozinka" id="lozinka" required>

            <div class="center-btns" style="margin-top:18px;">
                <input type="submit" class="main-btn" value="Prijavi se">
            </div>
        </form>
        <div style="margin-top:18px; text-align:center; font-size:14px;">
    Zaboravili ste korisničko ime ili lozinku?<br>
    <b>Obratite se administratoru sustava.</b>
</div>

    </div>
</div>
</body>
</html>
