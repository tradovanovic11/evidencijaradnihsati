<?php
session_start();
include "baza.php";

// Samo admin može pristupiti
if (!isset($_SESSION['korisnik_id']) || $_SESSION['uloga'] !== 'admin') {
    http_response_code(403);
    exit("Nedozvoljen pristup.");
}

// --- FILTERI I PAGINACIJA ---
$filter_korisnik = $_GET['filter_korisnik'] ?? '';
$filter_status = $_GET['filter_status'] ?? '';
$filter_od = $_GET['filter_od'] ?? '';
$filter_do = $_GET['filter_do'] ?? '';
$page = isset($_GET['page']) && is_numeric($_GET['page']) ? (int)$_GET['page'] : 1;
if ($page < 1) $page = 1;
$po_stranici = 5;
$offset = ($page - 1) * $po_stranici;

// --- BROJ UKUPNO ZA PAGINACIJU ---
$sql_count = "SELECT COUNT(*) AS ukupno FROM radni_sati rs WHERE 1=1";
$params_count = []; $types_count = "";
if (!empty($filter_korisnik)) { $sql_count .= " AND rs.korisnik_id = ?"; $types_count .= "i"; $params_count[] = $filter_korisnik; }
if (!empty($filter_status)) { $sql_count .= " AND rs.status = ?"; $types_count .= "s"; $params_count[] = $filter_status; }
if (!empty($filter_od)) { $sql_count .= " AND rs.datum >= ?"; $types_count .= "s"; $params_count[] = $filter_od; }
if (!empty($filter_do)) { $sql_count .= " AND rs.datum <= ?"; $types_count .= "s"; $params_count[] = $filter_do; }
$stmt_count = $conn->prepare($sql_count);
if (!empty($params_count)) $stmt_count->bind_param($types_count, ...$params_count);
$stmt_count->execute();
$ukupno = $stmt_count->get_result()->fetch_assoc()['ukupno'];
$ukupno_stranica = ceil($ukupno / $po_stranici);

// --- DOHVATI RADNE SATE ---
$sql_rs = "SELECT rs.*, k.ime, k.prezime FROM radni_sati rs JOIN korisnici k ON rs.korisnik_id = k.id WHERE 1=1";
$params = []; $types = "";
if (!empty($filter_korisnik)) { $sql_rs .= " AND rs.korisnik_id = ?"; $types .= "i"; $params[] = $filter_korisnik; }
if (!empty($filter_status)) { $sql_rs .= " AND rs.status = ?"; $types .= "s"; $params[] = $filter_status; }
if (!empty($filter_od)) { $sql_rs .= " AND rs.datum >= ?"; $types .= "s"; $params[] = $filter_od; }
if (!empty($filter_do)) { $sql_rs .= " AND rs.datum <= ?"; $types .= "s"; $params[] = $filter_do; }
$sql_rs .= " ORDER BY rs.datum DESC, rs.pocetak DESC LIMIT ?, ?";
$types .= "ii";
$params[] = $offset;
$params[] = $po_stranici;
$stmt_rs = $conn->prepare($sql_rs);
if (!empty($params)) $stmt_rs->bind_param($types, ...$params);
$stmt_rs->execute();
$radni_sati = $stmt_rs->get_result();
?>

<div class="toolbar-row">
    <div class="pagination">
        <?php if ($ukupno_stranica > 1): ?>
            <?php if ($page > 1): ?>
                <a href="#" class="pagination-link" data-page="1">&laquo;</a>
                <a href="#" class="pagination-link" data-page="<?= $page-1 ?>">&lt;</a>
            <?php endif; ?>
            <?php
            $start = max(1, $page - 2);
            $end = min($ukupno_stranica, $page + 2);
            if ($start > 1) echo '<span>...</span>';
            for ($i = $start; $i <= $end; $i++):
            ?>
                <?php if ($i == $page): ?>
                    <span class="active"><?= $i ?></span>
                <?php else: ?>
                    <a href="#" class="pagination-link" data-page="<?= $i ?>"><?= $i ?></a>
                <?php endif; ?>
            <?php endfor;
            if ($end < $ukupno_stranica) echo '<span>...</span>';
            ?>
            <?php if ($page < $ukupno_stranica): ?>
                <a href="#" class="pagination-link" data-page="<?= $page+1 ?>">&gt;</a>
                <a href="#" class="pagination-link" data-page="<?= $ukupno_stranica ?>">&raquo;</a>
            <?php endif; ?>
        <?php endif; ?>
    </div>
</div>

<table>
    <tr>
        <th>Zaposlenik</th>
        <th>Datum</th>
        <th>Početak</th>
        <th>Kraj</th>
        <th>Status</th>
        <th>Radno vrijeme</th>
        <th>Pauza</th>
    </tr>
    <?php while ($row = $radni_sati->fetch_assoc()): ?>
    <tr>
    <td><?= htmlspecialchars($row['ime'] . ' ' . $row['prezime']) ?></td>
        <td><?= date('d.m.y', strtotime($row['pocetak'])) ?></td>
        <td><?= date('H:i', strtotime($row['pocetak'])) ?></td>
        <td><?= !empty($row['kraj']) && $row['kraj'] !== '00:00:00' ? date('H:i', strtotime($row['kraj'])) : '' ?></td>
        <td>
            <span class="status-badge status-<?= htmlspecialchars($row['status']) ?>">
                <?= ucfirst($row['status']) ?>
            </span>
        </td>        
        <td>
        <?php
        if (!empty($row['kraj']) && $row['kraj'] !== '00:00:00') {
            $start_time = strtotime($row['pocetak']);
            $end_time = strtotime($row['kraj']);
            $sekunde = $end_time - $start_time;
            if ($sekunde < 0) $sekunde = 0;
            $h = floor($sekunde / 3600);
            $m = floor(($sekunde % 3600) / 60);
            $s = $sekunde % 60;
            printf("%02d:%02d", $h, $m);
        } else {
            $start_time = strtotime($row['pocetak']);
            $now = time();
            $sekunde = $now - $start_time;
            if ($sekunde < 0) $sekunde = 0;
            $h = floor($sekunde / 3600);
            $m = floor(($sekunde % 3600) / 60);
            $s = $sekunde % 60;
            echo "<span id='rad_timer_{$row['korisnik_id']}_{$row['id']}'>" . sprintf("%02d:%02d", $h, $m) . "</span>";

            echo "<script>
                (function() {
                    var sekunde = <?= (int)$sekunde ?>;
                    var el = document.getElementById('rad_timer_{$row['korisnik_id']}_{$row['id']}');
                    setInterval(function() {
                        sekunde++;
                        var h = Math.floor(sekunde / 3600);
                        var m = Math.floor((sekunde % 3600) / 60);
                        var s = sekunde % 60;
                        el.textContent = ('0'+h).slice(-2) + ':' + ('0'+m).slice(-2);
                    }, 1000);
                })();
            </script>";
        }
        ?>
        </td>
        <td>
        <?php
        // Prikaz pauza za ovaj radni dan
        $sql_aktivna_pauza = "SELECT pocetak FROM pauze WHERE korisnik_id = ? AND datum = ? AND kraj IS NULL LIMIT 1";
        $stmt_aktivna_pauza = $conn->prepare($sql_aktivna_pauza);
        $stmt_aktivna_pauza->bind_param("is", $row['korisnik_id'], $row['datum']);
        $stmt_aktivna_pauza->execute();
        $rez_aktivna = $stmt_aktivna_pauza->get_result();
        $aktivna_pauza = $rez_aktivna->fetch_assoc();

        if ($aktivna_pauza) {
            $start_time = strtotime($aktivna_pauza['pocetak']);
            $now = time();
            $sekunde = $now - $start_time;
            if ($sekunde < 0) $sekunde = 0;
            $h = floor($sekunde / 3600);
            $m = floor(($sekunde % 3600) / 60);
            $s = $sekunde % 60;
            echo "<span id='pauza_timer_{$row['korisnik_id']}_{$row['datum']}'>" . sprintf("%02d:%02d", $h, $m) . "</span>";

            echo "<script>
                (function() {
                    var sekunde = <?= (int)$sekunde ?>;
                    var el = document.getElementById('pauza_timer_{$row['korisnik_id']}_{$row['datum']}');
                    setInterval(function() {
                        sekunde++;
                        var h = Math.floor(sekunde / 3600);
                        var m = Math.floor((sekunde % 3600) / 60);
                        var s = sekunde % 60;
                        el.textContent = ('0'+h).slice(-2) + ':' + ('0'+m).slice(-2);
                    }, 1000);
                })();
            </script>";
        } else {
            $sql_pauza = "SELECT SUM(TIMESTAMPDIFF(SECOND, pocetak, kraj)) AS trajanje FROM pauze WHERE korisnik_id = ? AND datum = ? AND kraj IS NOT NULL";
            $stmt_pauza = $conn->prepare($sql_pauza);
            $stmt_pauza->bind_param("is", $row['korisnik_id'], $row['datum']);
            $stmt_pauza->execute();
            $rez_pauza = $stmt_pauza->get_result();
            $pauza = $rez_pauza->fetch_assoc();
            $sekunde = (int)$pauza['trajanje'];
            if ($sekunde > 0) {
                $h = floor($sekunde / 3600);
                $m = floor(($sekunde % 3600) / 60);
                $s = $sekunde % 60;
                printf("%02d:%02d", $h, $m);
            } else {
                echo "-";
            }
        }
        ?>
        </td>
    </tr>
    <?php endwhile; ?>
</table>
