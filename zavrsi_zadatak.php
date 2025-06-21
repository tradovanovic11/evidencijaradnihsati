<?php
session_start();
include "baza.php";

if (!isset($_SESSION['korisnik_id'])) {
    header("Location: index.php");
    exit();
}

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $zadatak_id = intval($_POST['zadatak_id']);
    $akcija = $_POST['akcija'];

    if ($akcija === "započni") {
        $sql = "UPDATE dodijeljeni_zadaci SET stvarni_pocetak = NOW() WHERE id = ?";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("i", $zadatak_id);
        $stmt->execute();
    }

    elseif ($akcija === "završi") {
        $sql = "UPDATE dodijeljeni_zadaci SET zavrsetak = NOW() WHERE id = ?";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("i", $zadatak_id);
        $stmt->execute();
    }

    elseif ($akcija === "napomena" && isset($_POST['napomena'])) {
        $nova_napomena = trim($_POST['napomena']);

        if (!empty($nova_napomena)) {
            // Prikazuje staru napomenu
            $sql = "SELECT napomena FROM dodijeljeni_zadaci WHERE id = ?";
            $stmt = $conn->prepare($sql);
            $stmt->bind_param("i", $zadatak_id);
            $stmt->execute();
            $result = $stmt->get_result();
            $stara_napomena = "";
            if ($row = $result->fetch_assoc()) {
                $stara_napomena = $row['napomena'];
            }

            // Dodaje novu napomenu sa timestampom
            $timestamp = date("Y-m-d H:i:s");
            $dodano = "[$timestamp] " . $nova_napomena;
            $nova_ukupna_napomena = trim($stara_napomena . "\n" . $dodano);

            $sqlUpdate = "UPDATE dodijeljeni_zadaci SET napomena = ? WHERE id = ?";
            $stmtUpdate = $conn->prepare($sqlUpdate);
            $stmtUpdate->bind_param("si", $nova_ukupna_napomena, $zadatak_id);
            $stmtUpdate->execute();
        }
    }
}

header("Location: radni_sati.php");
exit();
?>
