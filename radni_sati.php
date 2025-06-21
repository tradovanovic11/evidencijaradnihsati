<?php
session_start();
include "baza.php";

if (!isset($_SESSION['korisnik_id'])) {
    header("Location: index.php");
    exit();
}

$korisnik_id = $_SESSION['korisnik_id'];

// PAGINACIJA RADNI SATI
$records_per_page = 5;
$page = isset($_GET['page']) && is_numeric($_GET['page']) ? (int)$_GET['page'] : 1;
$offset = ($page - 1) * $records_per_page;

// Ukupan broj radnih dana
$sql_count = "SELECT COUNT(*) AS total FROM radni_sati WHERE korisnik_id = ?";
$stmt_count = $conn->prepare($sql_count);
$stmt_count->bind_param("i", $korisnik_id);
$stmt_count->execute();
$total_rows = $stmt_count->get_result()->fetch_assoc()['total'];
$total_pages = ceil($total_rows / $records_per_page);

// Dohvaća radne sate za trenutnu stranicu
$sql = "SELECT * FROM radni_sati WHERE korisnik_id = ? ORDER BY datum DESC LIMIT ?, ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("iii", $korisnik_id, $offset, $records_per_page);
$stmt->execute();
$radni_sati = $stmt->get_result();

// Provjeri postoji li AKTIVNA pauza 
$sql_pauza_check = "SELECT id FROM pauze WHERE korisnik_id = ? AND kraj IS NULL ORDER BY id DESC LIMIT 1";
$stmt_pauza_check = $conn->prepare($sql_pauza_check);
$stmt_pauza_check->bind_param("i", $korisnik_id);
$stmt_pauza_check->execute();
$result_pauza_check = $stmt_pauza_check->get_result();
$pauza_aktivna = ($result_pauza_check->num_rows > 0);

// Dohvati trenutni status
$status_poruka = "⏳ Još niste započeli radni dan.";
$sqlStatus = "SELECT status FROM radni_sati WHERE korisnik_id = ? AND status IN ('aktivno', 'pauza') AND kraj IS NULL ORDER BY id DESC LIMIT 1";
$stmtStatus = $conn->prepare($sqlStatus);
$stmtStatus->bind_param("i", $korisnik_id);
$stmtStatus->execute();
$resultStatus = $stmtStatus->get_result();

if ($resultStatus->num_rows > 0) {
    $rowStatus = $resultStatus->fetch_assoc();
    switch ($rowStatus['status']) {
        case 'aktivno':
            $status_poruka = "🟢 Trenutno ste prijavljeni i radite.";
            break;
        case 'pauza':
            $status_poruka = "🟡 Trenutno ste na pauzi.";
            break;
        case 'zavrseno':
            $status_poruka = "🔴 Radni dan je završen.";
            break;
    }
}

// PAGINACIJA ZA ZADATKE
$zadaci_po_stranici = 5;
$page_zadaci = isset($_GET['page_zadaci']) && is_numeric($_GET['page_zadaci']) ? (int)$_GET['page_zadaci'] : 1;
if ($page_zadaci < 1) $page_zadaci = 1;
$offset_zadaci = ($page_zadaci - 1) * $zadaci_po_stranici;


$sql_zadaci_count = "SELECT COUNT(*) AS total FROM dodijeljeni_zadaci WHERE korisnik_id = ?";
$stmt_zadaci_count = $conn->prepare($sql_zadaci_count);
$stmt_zadaci_count->bind_param("i", $korisnik_id);
$stmt_zadaci_count->execute();
$total_zadaci = $stmt_zadaci_count->get_result()->fetch_assoc()['total'];
$total_pages_zadaci = ceil($total_zadaci / $zadaci_po_stranici);

// Dohvati zadatke za trenutnu stranicu
$sqlZ = "SELECT * FROM dodijeljeni_zadaci WHERE korisnik_id = ? ORDER BY vrijeme_pocetka DESC LIMIT ?, ?";
$stmtZ = $conn->prepare($sqlZ);
$stmtZ->bind_param("iii", $korisnik_id, $offset_zadaci, $zadaci_po_stranici);
$stmtZ->execute();
$zadaci = $stmtZ->get_result();
?>
<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <title>Radni sati</title>
    <link rel="stylesheet" href="dizajn.css">
</head>
<body>
<div class="dashboard">
    <h2 class="dashboard-title">Evidencija radnih sati - <?= htmlspecialchars($_SESSION['korisnicko_ime']) ?></h2>
    <div class="center-btns">
            <a href="dodaj_unos.php" class="main-btn">🟢 Započni rad</a>
            <?php if (!$pauza_aktivna): ?>
                <a href="pauza.php" class="main-btn">⏸ Pauza</a>
            <?php else: ?>
                <a class="main-btn" style="background-color: grey; pointer-events: none; opacity: 0.6;">⏸ Pauza (aktivna)</a>
            <?php endif; ?>
            <a href="nastavi.php" class="main-btn">▶ Nastavi rad</a>
            <a href="zavrsi_rad.php" class="main-btn">🔴 Završi rad</a>
        </div>
    <div class="section-card">
        <div class="section-title">🕒 Radni sati</div>
        <div class="status-box"><?= $status_poruka ?></div>
        <div class="toolbar-row">
            
            <div class="pagination">
                <?php if ($total_pages > 1): ?>
                    <?php if ($page > 1): ?>
                        <a href="?page=1">&laquo;</a>
                        <a href="?page=<?= $page-1 ?>">&lt;</a>
                    <?php endif; ?>
                    <?php
                    $start = max(1, $page - 2);
                    $end = min($total_pages, $page + 2);
                    if ($start > 1) echo '<span>...</span>';
                    for ($i = $start; $i <= $end; $i++):
                    ?>
                        <?php if ($i == $page): ?>
                            <span class="active"><?= $i ?></span>
                        <?php else: ?>
                            <a href="?page=<?= $i ?>"><?= $i ?></a>
                        <?php endif; ?>
                    <?php endfor;
                    if ($end < $total_pages) echo '<span>...</span>';
                    ?>
                    <?php if ($page < $total_pages): ?>
                        <a href="?page=<?= $page+1 ?>">&gt;</a>
                        <a href="?page=<?= $total_pages ?>">&raquo;</a>
                    <?php endif; ?>
                <?php endif; ?>
            </div>
        </div>
        <table>
            <tr>
                <th>Datum</th>
                <th>Početak</th>
                <th>Kraj</th>                
                <th>Status</th>
                <th>Trajanje pauza</th>
            </tr>
            <?php while ($row = $radni_sati->fetch_assoc()): ?>
            <tr>
            <td>
    <?php

    if (!empty($row['pocetak'])) {
        echo date('d.m.y', strtotime($row['pocetak']));
    } else {
        echo '-';
    }
    ?>
</td>
<td>
    <?php
    
    if (!empty($row['pocetak'])) {
        echo date('d.m.y H:i:s', strtotime($row['pocetak']));
    } else {
        echo '-';
    }
    ?>
</td>
<td>
    <?php
    
    if (!empty($row['kraj']) && $row['kraj'] !== '00:00:00') {
        echo date('d.m.y H:i:s', strtotime($row['kraj']));
    } else {
        echo '';
    }
    ?>
</td>
                               <td>
                    <span class="status-badge status-<?= htmlspecialchars($row['status']) ?>">
                        <?= ucfirst($row['status']) ?>
                    </span>
                </td>
                <td>
                <?php
$sql_pauza = "SELECT SUM(TIMESTAMPDIFF(SECOND, pocetak, IFNULL(kraj, NOW()))) AS trajanje,
                     MAX(CASE WHEN kraj IS NULL THEN UNIX_TIMESTAMP(pocetak) ELSE NULL END) AS aktivna_pauza_pocetak
              FROM pauze
              WHERE korisnik_id = ? AND datum = ?";
$stmt_pauza = $conn->prepare($sql_pauza);
$stmt_pauza->bind_param("is", $korisnik_id, $row['datum']);
$stmt_pauza->execute();
$rez_pauza = $stmt_pauza->get_result();
$pauza = $rez_pauza->fetch_assoc();

$sekunde = (int)$pauza['trajanje'];
$aktivna_pauza_pocetak = $pauza['aktivna_pauza_pocetak'];

if ($aktivna_pauza_pocetak) {
    // Pauza je aktivna - prikazuj live timer
    // $sekunde sadrži ukupno do sada, ali uključuje i aktivnu pauzu
    $timer_id = "pauza_timer_{$row['id']}";
    echo "<span id='$timer_id'>" . gmdate("H:i:s", $sekunde) . "</span>";
    echo "<script>
        (function() {
            var sekunde = $sekunde;
            var el = document.getElementById('$timer_id');
            setInterval(function() {
                sekunde++;
                var h = Math.floor(sekunde / 3600);
                var m = Math.floor((sekunde % 3600) / 60);
                var s = sekunde % 60;
                el.textContent = ('0'+h).slice(-2) + ':' + ('0'+m).slice(-2) + ':' + ('0'+s).slice(-2);
            }, 1000);
        })();
    </script>";
} else {
    // Pauza nije aktivna prikazuje se ukupno vrijeme
    $h = floor($sekunde / 3600);
    $m = floor(($sekunde % 3600) / 60);
    $s = $sekunde % 60;
    printf("%02d:%02d:%02d", $h, $m, $s);
}
?>

                </td>
            </tr>
            <?php endwhile; ?>
        </table>        
    </div>

    <div class="section-card">
        <div class="section-title">📝 Zadaci koje je dodijelio administrator</div>
        <div class="toolbar-row">
            <div class="pagination">
                <?php if ($total_pages_zadaci > 1): ?>
                    <?php if ($page_zadaci > 1): ?>
                        <a href="?page_zadaci=1">&laquo;</a>
                        <a href="?page_zadaci=<?= $page_zadaci-1 ?>">&lt;</a>
                    <?php endif; ?>
                    <?php
                    $start = max(1, $page_zadaci - 2);
                    $end = min($total_pages_zadaci, $page_zadaci + 2);
                    if ($start > 1) echo '<span>...</span>';
                    for ($i = $start; $i <= $end; $i++):
                    ?>
                        <?php if ($i == $page_zadaci): ?>
                            <span class="active"><?= $i ?></span>
                        <?php else: ?>
                            <a href="?page_zadaci=<?= $i ?>"><?= $i ?></a>
                        <?php endif; ?>
                    <?php endfor;
                    if ($end < $total_pages_zadaci) echo '<span>...</span>';
                    ?>
                    <?php if ($page_zadaci < $total_pages_zadaci): ?>
                        <a href="?page_zadaci=<?= $page_zadaci+1 ?>">&gt;</a>
                        <a href="?page_zadaci=<?= $total_pages_zadaci ?>">&raquo;</a>
                    <?php endif; ?>
                <?php endif; ?>
            </div>
        </div>
        <table>
            <tr>
                <th>Naziv zadatka</th>
                <th>Početak</th>
                <th>Rok</th>
                <th>Status</th>
                <th>Napomene</th>
            </tr>
            <?php if ($zadaci->num_rows === 0): ?>
                <tr><td colspan='5'>Nema dodijeljenih zadataka.</td></tr>
            <?php else: ?>
                <?php while ($zad = $zadaci->fetch_assoc()): ?>
                <tr>
                    <td><?= htmlspecialchars($zad['opis']) ?></td>
                    <td>
                        <?php
                        if (!empty($zad['vrijeme_pocetka']) && $zad['vrijeme_pocetka'] !== '00:00:00') {
                            $dt = new DateTime($zad['vrijeme_pocetka']);
                            echo $dt->format('d.m.Y') . "<br>" . $dt->format('H:i');
                        } else {
                            echo "-";
                        }
                        ?>
                    </td>
                    <td>
                        <?php
                        if (!empty($zad['rok']) && $zad['rok'] !== '00:00:00') {
                            $dt_rok = new DateTime($zad['rok']);
                            echo $dt_rok->format('d.m.Y') . "<br>" . $dt_rok->format('H:i');
                        } else {
                            echo "-";
                        }
                        ?>
                    </td>
                    <td>
                        <?php
                        if (empty($zad['stvarni_pocetak'])) {
                            echo "<form action='zavrsi_zadatak.php' method='POST'>
                                <input type='hidden' name='zadatak_id' value='{$zad['id']}'>
                                <input type='hidden' name='akcija' value='započni'>
                                <input type='submit' value='▶️ Započni zadatak'>
                            </form>";
                        } elseif (empty($zad['zavrsetak'])) {
                            echo "<form action='zavrsi_zadatak.php' method='POST'>
                                <input type='hidden' name='zadatak_id' value='{$zad['id']}'>
                                <input type='hidden' name='akcija' value='završi'>
                                <input type='submit' value='✅ Završi zadatak'>
                            </form>";
                        } else {
                            $start = new DateTime($zad['stvarni_pocetak']);
                            $end = new DateTime($zad['zavrsetak']);
                            $trajanje = $start->diff($end);
                            echo "✅ Završeno (Trajanje: " . $trajanje->format('%H:%I:%S') . ")";
                        }
                        ?>
                    </td>
                    <td>
                        <div style="text-align:left; font-size: 13px; white-space: pre-wrap;"><?= htmlspecialchars($zad['napomena']) ?></div>
                        <form action="zavrsi_zadatak.php" method="POST" class="napomena-form">
                            <input type="hidden" name="zadatak_id" value="<?= $zad['id'] ?>">
                            <input type="hidden" name="akcija" value="napomena">
                            <textarea name="napomena" rows="2" placeholder="Unesi novu napomenu..."></textarea>
                            <input type="submit" value="💾 Spremi napomenu">
                        </form>
                    </td>
                </tr>
                <?php endwhile; ?>
            <?php endif; ?>
        </table>
    </div>

    <div class="center-btns">
        <a href="promjena_lozinke.php" class="main-btn">🔑 Promijeni lozinku</a>
        <a href="odjava.php" class="main-btn">🚪 Odjava</a>
    </div>
</div>
</body>
</html>
