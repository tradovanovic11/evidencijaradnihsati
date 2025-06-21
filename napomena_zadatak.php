<?php
session_start();
include "baza.php";

if (!isset($_SESSION['korisnik_id'])) {
    header("Location: index.php");
    exit();
}

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['zadatak_id'])) {
    $zadatak_id = (int)$_POST['zadatak_id'];
    $napomena = trim($_POST['napomena'] ?? '');
    $korisnik_id = $_SESSION['korisnik_id'];

    // Provjera je li zadatak stvarno dodijeljen korisniku
    $provjera = $conn->prepare("SELECT id FROM dodijeljeni_zadaci WHERE id = ? AND korisnik_id = ?");
    $provjera->bind_param("ii", $zadatak_id, $korisnik_id);
    $provjera->execute();
    $rezultat = $provjera->get_result();

    if ($rezultat->num_rows > 0) {
        $update = $conn->prepare("UPDATE dodijeljeni_zadaci SET napomena = ? WHERE id = ?");
        $update->bind_param("si", $napomena, $zadatak_id);
        $update->execute();
    }
}

header("Location: radni_sati.php");
exit();
?>
