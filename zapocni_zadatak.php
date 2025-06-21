<?php
session_start();
include "baza.php";

// Provjera prijave
if (!isset($_SESSION['korisnik_id'])) {
    header("Location: index.php");
    exit();
}

$korisnik_id = $_SESSION['korisnik_id'];

// Provjera aktivnog radnog vremena
$sql = "SELECT * FROM radni_sati WHERE korisnik_id = ? AND kraj IS NULL";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $korisnik_id);
$stmt->execute();
$rezultat = $stmt->get_result();

if ($rezultat->num_rows === 0) {
    echo "⛔ Nema aktivnog rada. Pokreni rad prije zadatka.";
    exit();
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $naziv = $_POST['naziv_zadatka'];
    $pocetak = date("Y-m-d H:i:s");

    // Ubacuje novi red u dodijeljeni_zadaci za trenutnog korisnika
    $stmt = $conn->prepare("INSERT INTO dodijeljeni_zadaci (korisnik_id, naziv_zadatka, pocetak) VALUES (?, ?, ?)");
    $stmt->bind_param("iss", $korisnik_id, $naziv, $pocetak);
    $stmt->execute();

    echo "✅ Zadatak započet.";
    exit();
}
?>

<form method="POST">
    <label>Unesi naziv zadatka:</label><br>
    <input type="text" name="naziv_zadatka" required>
    <br><br>
    <input type="submit" value="🟢 Započni zadatak">
</form>
