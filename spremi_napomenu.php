<?php
session_start();
include "baza.php";

if (!isset($_SESSION['korisnik_id'])) {
    header("Location: index.php");
    exit();
}

$korisnik_id = $_SESSION['korisnik_id'];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $radni_sati_id = $_POST['radni_sati_id'];
    $opis = $_POST['opis'];

    $stmt = $conn->prepare("UPDATE radni_sati SET opis = ? WHERE id = ? AND korisnik_id = ?");
    $stmt->bind_param("sii", $opis, $radni_sati_id, $korisnik_id);
    $stmt->execute();
}

header("Location: radni_sati.php");
exit();
?>
