<?php
session_start();
include "baza.php";

// Provjera uloge
if (!isset($_SESSION['uloga']) || $_SESSION['uloga'] !== 'admin') {
    header("Location: index.php");
    exit();
}

$poruka = "";

// Dodavanje zadatka
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $korisnik_id = $_POST['korisnik_id'];
    $naziv_zadatka = $_POST['opis'];
    $pocetak = date('Y-m-d H:i:00', strtotime($_POST['vrijeme_pocetka']));
    $rok = date('Y-m-d H:i:00', strtotime($_POST['rok_izvrsenja']));

    $sql = "INSERT INTO dodijeljeni_zadaci (korisnik_id, opis, vrijeme_pocetka, rok) VALUES (?, ?, ?, ?)";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("isss", $korisnik_id, $naziv_zadatka, $pocetak, $rok);
    $stmt->execute();

    $poruka = "✅ Zadatak uspješno dodan.";
}
?>
<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <title>Dodaj zadatak</title>
    <link rel="stylesheet" href="dizajn.css">
</head>
<body>
<div class="dashboard">
    <h2 class="dashboard-title">📝 Dodaj zadatak zaposleniku</h2>
    <div class="section-card" style="max-width:500px; margin:0 auto;">
        <?php if ($poruka): ?>
            <div class="status-box"><?= $poruka ?></div>
        <?php endif; ?>
        <form method="POST" class="vertical-form">
            <label for="korisnik_id">Zaposlenik:</label>
            <select name="korisnik_id" id="korisnik_id" required>
                <option value="">Odaberi zaposlenika</option>
                <?php
                $rez = $conn->query("SELECT id, ime, prezime FROM korisnici WHERE uloga = 'zaposlenik' ORDER BY prezime, ime");
                while ($red = $rez->fetch_assoc()) {
                    $imePrezime = htmlspecialchars($red['ime'] . ' ' . $red['prezime']);
                    echo "<option value='{$red['id']}'>{$imePrezime}</option>";
                }
                
                ?>
            </select>

            <label for="opis">Opis zadatka:</label>
            <textarea name="opis" id="opis" rows="4" required></textarea>

            <label for="vrijeme_pocetka">Vrijeme početka:</label>
            <input type="datetime-local" name="vrijeme_pocetka" id="vrijeme_pocetka" required>

            <label for="rok_izvrsenja">Rok za izvršenje:</label>
            <input type="datetime-local" name="rok_izvrsenja" id="rok_izvrsenja" required>

            <div class="center-btns">
                <input type="submit" class="main-btn" value="➕ Dodaj zadatak">
                <a href="admin_pregled.php" class="main-btn">⬅ Natrag</a>
            </div>
        </form>
    </div>
</div>
</body>
</html>
