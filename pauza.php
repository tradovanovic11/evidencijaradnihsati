<?php
session_start();
include "baza.php";

if (!isset($_SESSION['korisnik_id'])) {
    header("Location: index.php");
    exit();
}

$korisnik_id = $_SESSION['korisnik_id'];

// Pronađi nezavršeni radni dan (bez obzira na datum)
$sql = "SELECT * FROM radni_sati WHERE korisnik_id = ? AND kraj IS NULL ORDER BY id DESC LIMIT 1";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $korisnik_id);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows === 1) {
    $radni = $result->fetch_assoc();

    // Provjeri postoji li već otvorena pauza
    $sql_check_pauza = "SELECT id FROM pauze WHERE korisnik_id = ? AND kraj IS NULL ORDER BY id DESC LIMIT 1";
    $stmt_check = $conn->prepare($sql_check_pauza);
    $stmt_check->bind_param("i", $korisnik_id);
    $stmt_check->execute();
    $result_check = $stmt_check->get_result();

    if ($result_check->num_rows === 0) {
        // Ažuriraj status u radni_sati na 'pauza' SAMO ako nije već 'pauza'
        if ($radni['status'] !== 'pauza') {
            $sql_update = "UPDATE radni_sati SET status = 'pauza' WHERE id = ?";
            $stmt_update = $conn->prepare($sql_update);
            $stmt_update->bind_param("i", $radni['id']);
            $stmt_update->execute();
        }

        // Upis nove pauze u tablicu pauze
        $sql_pauza = "INSERT INTO pauze (korisnik_id, datum, pocetak) VALUES (?, ?, NOW())";
        $stmt_pauza = $conn->prepare($sql_pauza);
        $stmt_pauza->bind_param("is", $korisnik_id, $radni['datum']);
        $stmt_pauza->execute();

        $_SESSION['poruka'] = "✅ Pauza je uspješno evidentirana!";
    } else {
        $_SESSION['poruka'] = "❗ Pauza je već aktivna!";
    }
} else {
    $_SESSION['poruka'] = "❗ Nemate aktivan radni dan za pauzu!";
}

header("Location: radni_sati.php");
exit();
?>
