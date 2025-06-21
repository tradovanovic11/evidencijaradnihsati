<?php
session_start();
include "baza.php";

if (!isset($_SESSION['korisnik_id'])) {
    header("Location: index.php");
    exit();
}

$korisnik_id = $_SESSION['korisnik_id'];


$sql = "SELECT * FROM radni_sati WHERE korisnik_id = ? AND status IN ('aktivno', 'pauza') AND kraj IS NULL ORDER BY id DESC LIMIT 1";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $korisnik_id);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows === 1) {
    $radni = $result->fetch_assoc();

    // Ako je korisnik bio na pauzi, zatvora posljednju aktivnu pauzu
    if ($radni['status'] === 'pauza') {
        // Pronađi posljednju aktivnu pauzu
        $sql_pauza = "SELECT id FROM pauze WHERE korisnik_id = ? AND kraj IS NULL ORDER BY id DESC LIMIT 1";
        $stmt_pauza = $conn->prepare($sql_pauza);
        $stmt_pauza->bind_param("i", $korisnik_id);
        $stmt_pauza->execute();
        $rez_pauza = $stmt_pauza->get_result();
        if ($row_pauza = $rez_pauza->fetch_assoc()) {
            $sql_zatvori = "UPDATE pauze SET kraj = NOW() WHERE id = ?";
            $stmt_zatvori = $conn->prepare($sql_zatvori);
            $stmt_zatvori->bind_param("i", $row_pauza['id']);
            $stmt_zatvori->execute();
        }
    }

    // Ažurira radni zapis: upisuje kraj i status
    $sql_update = "UPDATE radni_sati SET status = 'zavrseno', kraj = NOW() WHERE id = ?";
    $stmt_update = $conn->prepare($sql_update);
    $stmt_update->bind_param("i", $radni['id']);
    $stmt_update->execute();
}

header("Location: radni_sati.php");
exit();
?>
