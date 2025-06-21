<?php
session_start();
include "baza.php";

if (!isset($_SESSION['korisnik_id']) || $_SESSION['uloga'] !== 'admin') {
    die("Nedozvoljen pristup.");
}

// Filtri 
$zad_filter_korisnik = $_GET['zad_filter_korisnik'] ?? '';
$zad_filter_status = $_GET['zad_filter_status'] ?? '';
$zad_filter_od = $_GET['zad_filter_od'] ?? '';
$zad_filter_do = $_GET['zad_filter_do'] ?? '';
$zad_filter_kljucna_rijec = $_GET['zad_filter_kljucna_rijec'] ?? '';

// Pripremi SQL upit
$sql = "SELECT dz.*, k.korisnicko_ime 
        FROM dodijeljeni_zadaci dz
        JOIN korisnici k ON dz.korisnik_id = k.id
        WHERE 1=1";
$params = [];
$types = "";

if (!empty($zad_filter_korisnik)) { $sql .= " AND dz.korisnik_id = ?"; $types .= "i"; $params[] = $zad_filter_korisnik; }
if (!empty($zad_filter_status)) {
    $sql .= " AND ( 
        (? = 'zavrseno' AND dz.zavrsetak IS NOT NULL) OR
        (? = 'u_tijeku' AND dz.zavrsetak IS NULL AND dz.stvarni_pocetak IS NOT NULL) OR
        (? = 'nije_zapoceto' AND dz.stvarni_pocetak IS NULL)
    )";
    $types .= "sss";
    $params[] = $zad_filter_status;
    $params[] = $zad_filter_status;
    $params[] = $zad_filter_status;
}
if (!empty($zad_filter_od)) { $sql .= " AND dz.vrijeme_pocetka >= ?"; $types .= "s"; $params[] = $zad_filter_od; }
if (!empty($zad_filter_do)) { $sql .= " AND dz.vrijeme_pocetka <= ?"; $types .= "s"; $params[] = $zad_filter_do; }
if (!empty($zad_filter_kljucna_rijec)) { $sql .= " AND dz.opis LIKE ?"; $types .= "s"; $params[] = '%' . $zad_filter_kljucna_rijec . '%'; }
$sql .= " ORDER BY dz.vrijeme_pocetka DESC";

$stmt = $conn->prepare($sql);
if (!empty($params)) $stmt->bind_param($types, ...$params);
$stmt->execute();
$res = $stmt->get_result();

// Priprema za CSV
header('Content-Type: text/csv; charset=utf-8');
header('Content-Disposition: attachment; filename="dodijeljeni_zadaci.csv"');
$output = fopen('php://output', 'w');

// Zaglavlje
fputcsv($output, ['Korisnik', 'Opis', 'Početak', 'Rok', 'Stvarni početak', 'Stvarni završetak', 'Status', 'Napomena']);

// Podaci
while ($row = $res->fetch_assoc()) {
    // Status zadatka
    if (!empty($row['zavrsetak'])) $status = 'Završeno';
    elseif (!empty($row['stvarni_pocetak'])) $status = 'U tijeku';
    else $status = 'Nije započeto';

    fputcsv($output, [
        $row['korisnicko_ime'],
        $row['opis'],
        $row['vrijeme_pocetka'],
        $row['rok'],
        $row['stvarni_pocetak'],
        $row['zavrsetak'],
        $status,
        $row['napomena']
    ]);
}

fclose($output);
exit;
?>
