<?php
session_start();
include "baza.php";

if (!isset($_SESSION['korisnik_id'])) {
    header("Location: index.php");
    exit();
}

if ($_SERVER["REQUEST_METHOD"] === "POST" && isset($_POST['zadatak_id'])) {
    $zadatak_id = $_POST['zadatak_id'];
    $korisnik_id = $_SESSION['korisnik_id'];

    // Ažuriraj status zadatka samo ako je pripadajući korisniku
    $sql = "UPDATE dodijeljeni_zadaci SET status = 'završeno' WHERE id = ? AND korisnik_id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("ii", $zadatak_id, $korisnik_id);
    $stmt->execute();
}

header("Location: radni_sati.php");
exit();
?>
