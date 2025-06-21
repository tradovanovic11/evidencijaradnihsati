<?php
session_start();
include "baza.php";

// Samo admin može pristupiti
if (!isset($_SESSION['korisnik_id']) || $_SESSION['uloga'] !== 'admin') {
    http_response_code(403);
    exit("Nedozvoljen pristup.");
}

// --- FILTERI I PAGINACIJA ---
$zad_filter_korisnik = $_GET['zad_filter_korisnik'] ?? '';
$zad_filter_status = $_GET['zad_filter_status'] ?? '';
$zad_filter_od = $_GET['zad_filter_od'] ?? '';
$zad_filter_do = $_GET['zad_filter_do'] ?? '';
$zad_filter_kljucna_rijec = $_GET['zad_filter_kljucna_rijec'] ?? '';
$page_zadaci = isset($_GET['page_zadaci']) && is_numeric($_GET['page_zadaci']) ? (int)$_GET['page_zadaci'] : 1;
if ($page_zadaci < 1) $page_zadaci = 1;
$zadaci_po_stranici = 5;
$offset_zadaci = ($page_zadaci - 1) * $zadaci_po_stranici;

// --- BROJ UKUPNO ZA PAGINACIJU ---
$sql_count = "SELECT COUNT(*) AS total FROM dodijeljeni_zadaci dz WHERE 1=1";
$params_count = []; $types_count = "";
if (!empty($zad_filter_korisnik)) { $sql_count .= " AND dz.korisnik_id = ?"; $types_count .= "i"; $params_count[] = $zad_filter_korisnik; }
if (!empty($zad_filter_status)) {
    $sql_count .= " AND ( 
        (? = 'zavrseno' AND dz.zavrsetak IS NOT NULL) OR
        (? = 'u_tijeku' AND dz.zavrsetak IS NULL AND dz.stvarni_pocetak IS NOT NULL) OR
        (? = 'nije_zapoceto' AND dz.stvarni_pocetak IS NULL)
    )";
    $types_count .= "sss";
    $params_count[] = $zad_filter_status;
    $params_count[] = $zad_filter_status;
    $params_count[] = $zad_filter_status;
}
if (!empty($zad_filter_od)) { $sql_count .= " AND dz.vrijeme_pocetka >= ?"; $types_count .= "s"; $params_count[] = $zad_filter_od; }
if (!empty($zad_filter_do)) { $sql_count .= " AND dz.vrijeme_pocetka <= ?"; $types_count .= "s"; $params_count[] = $zad_filter_do; }
if (!empty($zad_filter_kljucna_rijec)) { $sql_count .= " AND dz.opis LIKE ?"; $types_count .= "s"; $params_count[] = '%' . $zad_filter_kljucna_rijec . '%'; }
$stmt_count = $conn->prepare($sql_count);
if (!empty($params_count)) $stmt_count->bind_param($types_count, ...$params_count);
$stmt_count->execute();
$total_zadaci = $stmt_count->get_result()->fetch_assoc()['total'];
$total_pages_zadaci = ceil($total_zadaci / $zadaci_po_stranici);

// --- DOHVATI ZADATKE ---
$sql_zad = "SELECT dz.*, k.ime, k.prezime FROM dodijeljeni_zadaci dz JOIN korisnici k ON dz.korisnik_id = k.id WHERE 1=1";
$params_z = []; $types_z = "";
if (!empty($zad_filter_korisnik)) { $sql_zad .= " AND dz.korisnik_id = ?"; $types_z .= "i"; $params_z[] = $zad_filter_korisnik; }
if (!empty($zad_filter_status)) {
    $sql_zad .= " AND ( 
        (? = 'zavrseno' AND dz.zavrsetak IS NOT NULL) OR
        (? = 'u_tijeku' AND dz.zavrsetak IS NULL AND dz.stvarni_pocetak IS NOT NULL) OR
        (? = 'nije_zapoceto' AND dz.stvarni_pocetak IS NULL)
    )";
    $types_z .= "sss";
    $params_z[] = $zad_filter_status;
    $params_z[] = $zad_filter_status;
    $params_z[] = $zad_filter_status;
}
if (!empty($zad_filter_od)) { $sql_zad .= " AND dz.vrijeme_pocetka >= ?"; $types_z .= "s"; $params_z[] = $zad_filter_od; }
if (!empty($zad_filter_do)) { $sql_zad .= " AND dz.vrijeme_pocetka <= ?"; $types_z .= "s"; $params_z[] = $zad_filter_do; }
if (!empty($zad_filter_kljucna_rijec)) { $sql_zad .= " AND dz.opis LIKE ?"; $types_z .= "s"; $params_z[] = '%' . $zad_filter_kljucna_rijec . '%'; }
$sql_zad .= " ORDER BY dz.vrijeme_pocetka DESC LIMIT ?, ?";
$types_z .= "ii";
$params_z[] = $offset_zadaci;
$params_z[] = $zadaci_po_stranici;
$stmt_z = $conn->prepare($sql_zad);
if (!empty($params_z)) $stmt_z->bind_param($types_z, ...$params_z);
$stmt_z->execute();
$zadaci = $stmt_z->get_result();
?>

<div class="toolbar-row">
    <div class="pagination">
        <?php if ($total_pages_zadaci > 1): ?>
            <?php if ($page_zadaci > 1): ?>
                <a href="#" class="pagination-link-zadaci" data-page="1">&laquo;</a>
                <a href="#" class="pagination-link-zadaci" data-page="<?= $page_zadaci-1 ?>">&lt;</a>
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
                    <a href="#" class="pagination-link-zadaci" data-page="<?= $i ?>"><?= $i ?></a>
                <?php endif; ?>
            <?php endfor;
            if ($end < $total_pages_zadaci) echo '<span>...</span>';
            ?>
            <?php if ($page_zadaci < $total_pages_zadaci): ?>
                <a href="#" class="pagination-link-zadaci" data-page="<?= $page_zadaci+1 ?>">&gt;</a>
                <a href="#" class="pagination-link-zadaci" data-page="<?= $total_pages_zadaci ?>">&raquo;</a>
            <?php endif; ?>
        <?php endif; ?>
    </div>
</div>

<table>
    <tr>
        <th>Zaposlenik</th>
        <th>Opis zadatka</th>
        <th>Početak (dodijeljen)</th>
        <th>Rok</th>
        <th>Stvarni početak</th>
        <th>Stvarni završetak</th>
        <th>Status</th>
        <th>Napomene zaposlenika</th>
    </tr>
    <?php while ($z = $zadaci->fetch_assoc()): ?>
        <tr>
            <td><?= htmlspecialchars($z['ime'] . ' ' . $z['prezime']) ?></td>
            <td><?= htmlspecialchars($z['opis']) ?></td>
            <td>
                <?php
                if (!empty($z['vrijeme_pocetka']) && $z['vrijeme_pocetka'] !== '00:00:00') {
                    $dt = new DateTime($z['vrijeme_pocetka']);
                    echo $dt->format('d.m.Y') . "<br>" . $dt->format('H:i');
                } else {
                    echo "-";
                }
                ?>
            </td>
            <td>
                <?php
                if (!empty($z['rok']) && $z['rok'] !== '00:00:00') {
                    $dt_rok = new DateTime($z['rok']);
                    echo $dt_rok->format('d.m.Y') . "<br>" . $dt_rok->format('H:i');
                } else {
                    echo "-";
                }
                ?>
            </td>
            <td>
    <?php
    if (!empty($z['stvarni_pocetak']) && $z['stvarni_pocetak'] !== '00:00:00') {
        $dt = new DateTime($z['stvarni_pocetak']);
        echo $dt->format('d.m.Y') . "<br>" . $dt->format('H:i');
    } else {
        echo "-";
    }
    ?>
</td>
<td>
    <?php
    if (!empty($z['zavrsetak']) && $z['zavrsetak'] !== '00:00:00') {
        $dt = new DateTime($z['zavrsetak']);
        echo $dt->format('d.m.Y') . "<br>" . $dt->format('H:i');
    } else {
        echo "-";
    }
    ?>
</td>

            <td>
                <?php
                    if (!empty($z['zavrsetak'])) {
                        echo "<span class='status-badge status-zavrseno'>✅ Završeno</span>";
                    } elseif (!empty($z['stvarni_pocetak'])) {
                        echo "<span class='status-badge status-pauza'>⏳ U tijeku</span>";
                    } else {
                        echo "<span class='status-badge status-aktivno'>🕒 Nije započeto</span>";
                    }
                ?>
            </td>
            <td><?= !empty($z['napomena']) ? nl2br(htmlspecialchars($z['napomena'])) : '<i>Nema napomena</i>' ?></td>
        </tr>
    <?php endwhile; ?>
</table>
