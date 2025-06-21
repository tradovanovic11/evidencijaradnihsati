<?php
session_start();
include "baza.php";

// Provjera prijave korisnika
if (!isset($_SESSION['korisnik_id'])) {
    header("Location: index.php");
    exit();
}

$korisnik_id = $_SESSION['korisnik_id'];

// Provjera za nezavršeni radni
$sql = "SELECT id FROM radni_sati WHERE korisnik_id = ? AND kraj IS NULL";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $korisnik_id);
$stmt->execute();
$res = $stmt->get_result();
if ($res->num_rows > 0) {
    $_SESSION['poruka'] = "❗ Ne možete započeti novi rad dok prethodni nije završen!";
    header("Location: radni_sati.php");
    exit;
}

// Dodaje novi zapis u bazu
$sql = "INSERT INTO radni_sati (korisnik_id, datum, pocetak, status, opis) VALUES (?, CURDATE(), NOW(), 'aktivno', '')";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $korisnik_id);
$stmt->execute();

// Preusmjeravanje natrag 
header("Location: radni_sati.php");
exit();
?>
