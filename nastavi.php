<?php
session_start();
include "baza.php";

if (!isset($_SESSION['korisnik_id'])) {
    header("Location: index.php");
    exit();
}

$korisnik_id = $_SESSION['korisnik_id'];


$sql = "SELECT * FROM radni_sati WHERE korisnik_id = ? AND status = 'pauza' AND kraj IS NULL ORDER BY id DESC LIMIT 1";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $korisnik_id);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows === 1) {
    $radni = $result->fetch_assoc();

    
    $sql_update = "UPDATE radni_sati SET status = 'aktivno' WHERE id = ?";
    $stmt_update = $conn->prepare($sql_update);
    $stmt_update->bind_param("i", $radni['id']);
    $stmt_update->execute();
    if ($stmt_update->affected_rows > 0) {
            } else {      
    }
    

    $sql_pauza_select = "SELECT id FROM pauze WHERE korisnik_id = ? AND kraj IS NULL ORDER BY id DESC LIMIT 1";
    $stmt_pauza_select = $conn->prepare($sql_pauza_select);
    $stmt_pauza_select->bind_param("i", $korisnik_id);
    $stmt_pauza_select->execute();
    $rezultat = $stmt_pauza_select->get_result();

    if ($rezultat->num_rows === 1) {
        $pauza = $rezultat->fetch_assoc();
        $pauza_id = $pauza['id'];
    
        $sql_pauza_update = "UPDATE pauze SET kraj = NOW() WHERE id = ?";
        $stmt_pauza_update = $conn->prepare($sql_pauza_update);
        $stmt_pauza_update->bind_param("i", $pauza_id);
        $stmt_pauza_update->execute();
    }
}

header("Location: radni_sati.php");
exit();
?>
