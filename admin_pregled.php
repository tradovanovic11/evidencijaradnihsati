<?php
session_start();
include "baza.php";

// Samo admin može pristupiti
if (!isset($_SESSION['korisnik_id']) || $_SESSION['uloga'] !== 'admin') {
    header("Location: index.php");
    exit();
}

// Dohvati sve korisnike 
$korisnici = [];
$res = $conn->query("SELECT id, ime, prezime FROM korisnici ORDER BY prezime, ime");
while ($k = $res->fetch_assoc()) {
    $korisnici[] = $k;
}

//  Priprema filtera i paginacije 
$filter_korisnik = $_GET['filter_korisnik'] ?? '';
$filter_status = $_GET['filter_status'] ?? '';
$filter_od = $_GET['filter_od'] ?? '';
$filter_do = $_GET['filter_do'] ?? '';
$zad_filter_korisnik = $_GET['zad_filter_korisnik'] ?? '';
$zad_filter_status = $_GET['zad_filter_status'] ?? '';
$zad_filter_od = $_GET['zad_filter_od'] ?? '';
$zad_filter_do = $_GET['zad_filter_do'] ?? '';
$zad_filter_kljucna_rijec = $_GET['zad_filter_kljucna_rijec'] ?? '';
$page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
$page_zadaci = isset($_GET['page_zadaci']) ? (int)$_GET['page_zadaci'] : 1;
?>
<!DOCTYPE html>
<html>
<head>
    <title>Administrator - kontrolna ploča</title>
    <meta charset="UTF-8">
    <link rel="stylesheet" href="dizajn.css">
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <style>
        .dashboard { max-width: 1300px; margin: 0 auto; }
        .section-card { margin-top: 30px; }
        .center-btns { margin: 20px 0; text-align: center; }
    </style>
</head>
<body>
<div class="dashboard">
    <h2 class="dashboard-title">👨‍💼 Administrator - kontrolna ploča</h2>
    <div class="center-btns">
        <a href="dodaj_zadatak.php" class="main-btn">➕ Dodijeli novi zadatak</a>
        <a href="dodaj_zaposlenika.php" class="main-btn">👤 Dodaj zaposlenika</a>
        <a href="promjena_lozinke.php" class="main-btn">🔑 Promijeni lozinku</a>
        <a href="odjava.php" class="main-btn">🚪 Odjava</a>
    </div>

<!-- FILTERI RADNI SATI -->
    <div class="section-card">
        <div class="section-title">📆 Evidencija radnih sati</div>
        <div class="toolbar-row">
            <form id="filterForm" method="get" class="inline-form" style="flex:1 1 auto;">
                <label for="filter_korisnik">👤</label>
                <select name="filter_korisnik" id="filter_korisnik">
                    <option value="">Svi</option>
                    <?php foreach ($korisnici as $k): ?>
                        <option value="<?= $k['id'] ?>" <?= ($filter_korisnik == $k['id']) ? 'selected' : '' ?>>
                         <?= htmlspecialchars($k['ime'] . ' ' . $k['prezime']) ?>
                        </option>
                    <?php endforeach; ?>
                </select>
                <label for="filter_status">Status:</label>
                <select name="filter_status" id="filter_status" class="status-filter">
                    <option value="">Svi</option>
                    <option value="aktivno" <?= $filter_status == 'aktivno' ? 'selected' : '' ?>>Aktivno</option>
                    <option value="pauza" <?= $filter_status == 'pauza' ? 'selected' : '' ?>>Pauza</option>
                    <option value="zavrseno" <?= $filter_status == 'zavrseno' ? 'selected' : '' ?>>Završeno</option>
                </select>
                <label for="filter_od">Od:</label>
                <input type="date" name="filter_od" value="<?= htmlspecialchars($filter_od) ?>">
                <label for="filter_do">Do:</label>
                <input type="date" name="filter_do" value="<?= htmlspecialchars($filter_do) ?>">
                <input type="hidden" name="page" id="page" value="<?= $page ?>">
                <input type="submit" value="🔍">
                <input type="button" id="clearFilters" value="Očisti">
            </form>
            <div class="export-btns">
            <a href="#" class="main-btn" id="csvExportBtn">CSV</a>

            </div>
        </div>
        <div id="radni_sati_tablica"></div>
    </div>

    <!-- FILTERI ZADACI -->
    <div class="section-card">
        <div class="section-title">📝 Dodijeljeni zadaci</div>
        <div class="toolbar-row">
            <form id="zadaciFilterForm" method="get" class="inline-form" style="flex:1 1 auto;">
                <label for="zad_filter_korisnik">👤</label>
                <select name="zad_filter_korisnik" id="zad_filter_korisnik">
                    <option value="">Svi</option>
                    <?php foreach ($korisnici as $k): ?>
                        <option value="<?= $k['id'] ?>" <?= ($filter_korisnik == $k['id']) ? 'selected' : '' ?>>
                        <?= htmlspecialchars($k['ime'] . ' ' . $k['prezime']) ?>
                        </option>
                    <?php endforeach; ?>
                </select>
                <label for="zad_filter_status">Status:</label>
                <select name="zad_filter_status" id="zad_filter_status" class="status-filter">
                    <option value="">Svi</option>
                    <option value="nije_zapoceto" <?= $zad_filter_status == 'nije_zapoceto' ? 'selected' : '' ?>>Nije započeto</option>
                    <option value="u_tijeku" <?= $zad_filter_status == 'u_tijeku' ? 'selected' : '' ?>>U tijeku</option>
                    <option value="zavrseno" <?= $zad_filter_status == 'zavrseno' ? 'selected' : '' ?>>Završeno</option>
                </select>
                <label for="zad_filter_od">Od:</label>
                <input type="date" name="zad_filter_od" value="<?= htmlspecialchars($zad_filter_od) ?>">
                <label for="zad_filter_do">Do:</label>
                <input type="date" name="zad_filter_do" value="<?= htmlspecialchars($zad_filter_do) ?>">
                <input type="text" name="zad_filter_kljucna_rijec" class="keyword-filter" placeholder="Ključna riječ..." value="<?= htmlspecialchars($zad_filter_kljucna_rijec) ?>">
                <input type="hidden" name="page_zadaci" id="page_zadaci" value="<?= $page_zadaci ?>">
                <input type="submit" value="🔍">
                <input type="button" id="clearZadaciFilters" value="Očisti">
            </form>
            <div class="export-btns">
            <a href="#" class="main-btn" id="csvExportZadaciBtn">CSV</a>
            </div>
        </div>
        <div id="zadaci_tablica"></div>
    </div>

    <div class="center-btns">
        <a href="odjava.php" class="main-btn">🚪 Odjava</a>
    </div>
</div>

<script>
function ucitajRadneSate() {
    var params = $('#filterForm').serialize();
    $.get('admin_pregled_tablica.php?' + params, function(data) {
        $('#radni_sati_tablica').html(data);
    });
}
function ucitajZadace() {
    var params = $('#zadaciFilterForm').serialize();
    $.get('admin_pregled_zadaci.php?' + params, function(data) {
        $('#zadaci_tablica').html(data);
    });
}
$(document).ready(function() {
    ucitajRadneSate();
    ucitajZadace();

    setInterval(ucitajRadneSate, 5000);
    setInterval(ucitajZadace, 5000);

    // Filter radni sati
    $('#filterForm').on('change submit', function(e) {
        e.preventDefault();
        $('#page').val(1); // vraća stranicu na 1 kad filtriraš
        ucitajRadneSate();
        history.replaceState(null, '', '?' + $(this).serialize());
    });

    // Filter zadaci
    $('#zadaciFilterForm').on('change submit', function(e) {
        e.preventDefault();
        $('#page_zadaci').val(1);
        ucitajZadace();
        history.replaceState(null, '', '?' + $('#filterForm').serialize() + '&' + $(this).serialize());
    });

    // Paginacija radni sati
    $(document).on('click', '#radni_sati_tablica .pagination-link', function(e) {
        e.preventDefault();
        var page = $(this).data('page');
        $('#page').val(page);
        ucitajRadneSate();
        history.replaceState(null, '', '?' + $('#filterForm').serialize());
    });

    // Paginacija zadaci
    $(document).on('click', '#zadaci_tablica .pagination-link-zadaci', function(e) {
        e.preventDefault();
        var page = $(this).data('page');
        $('#page_zadaci').val(page);
        ucitajZadace();
        history.replaceState(null, '', '?' + $('#zadaciFilterForm').serialize());
    });

    // Očisti filtere - RADNI SATI
    $('#clearFilters').click(function() {
        $('#filterForm')[0].reset();
        $('#filterForm select').val('');
        $('#filterForm input[type="date"]').val('');
        $('#filterForm input[type="text"]').val('');
        $('#page').val(1);
        ucitajRadneSate();
        history.replaceState(null, '', window.location.pathname);
    });

    // Očisti filtere - ZADACI
    $('#clearZadaciFilters').click(function() {
        $('#zadaciFilterForm')[0].reset();
        $('#zadaciFilterForm select').val('');
        $('#zadaciFilterForm input[type="date"]').val('');
        $('#zadaciFilterForm input[type="text"]').val('');
        $('#page_zadaci').val(1);
        ucitajZadace();
        history.replaceState(null, '', window.location.pathname);
    });
});

// CSV export za radne sate
$('#csvExportBtn').click(function(e) {
    e.preventDefault();
    var params = $('#filterForm').serialize() + '&format=csv';
    window.open('export_radni_sati.php?' + params, '_blank');
});

// CSV export za zadatke
$('#csvExportZadaciBtn').click(function(e) {
    e.preventDefault();
    var params = $('#zadaciFilterForm').serialize() + '&format=csv';
    window.open('export_zadaci.php?' + params, '_blank');
});

</script>
</body>
</html>
