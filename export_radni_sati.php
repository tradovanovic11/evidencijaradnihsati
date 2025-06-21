<?php
session_start();
include "baza.php";

if (!isset($_SESSION['korisnik_id']) || $_SESSION['uloga'] !== 'admin') {
    die("Nedozvoljen pristup.");
}

// Filtri
$filter_korisnik = $_GET['filter_korisnik'] ?? '';
$filter_status = $_GET['filter_status'] ?? '';
$filter_od = $_GET['filter_od'] ?? '';
$filter_do = $_GET['filter_do'] ?? '';

// Pripremi SQL upit
$sql = "SELECT rs.*, k.korisnicko_ime 
        FROM radni_sati rs
        JOIN korisnici k ON rs.korisnik_id = k.id
        WHERE 1=1";
$params = [];
$types = "";

if (!empty($filter_korisnik)) { $sql .= " AND rs.korisnik_id = ?"; $types .= "i"; $params[] = $filter_korisnik; }
if (!empty($filter_status)) { $sql .= " AND rs.status = ?"; $types .= "s"; $params[] = $filter_status; }
if (!empty($filter_od)) { $sql .= " AND rs.datum >= ?"; $types .= "s"; $params[] = $filter_od; }
if (!empty($filter_do)) { $sql .= " AND rs.datum <= ?"; $types .= "s"; $params[] = $filter_do; }
$sql .= " ORDER BY rs.datum DESC, rs.pocetak DESC";

$stmt = $conn->prepare($sql);
if (!empty($params)) $stmt->bind_param($types, ...$params);
$stmt->execute();
$res = $stmt->get_result();

// Priprema za CSV
header('Content-Type: text/csv; charset=utf-8');
header('Content-Disposition: attachment; filename="radni_sati.csv"');
$output = fopen('php://output', 'w');

// Zaglavlje
fputcsv($output, ['Korisnik', 'Datum', 'Početak', 'Kraj', 'Status', 'Napomena']);

// Podaci
while ($row = $res->fetch_assoc()) {
    fputcsv($output, [
        $row['korisnicko_ime'],
        $row['datum'],
        $row['pocetak'],
        $row['kraj'],
        ucfirst($row['status']),
        $row['opis']
    ]);
}

fclose($output);
exit;
?>
