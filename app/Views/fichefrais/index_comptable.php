<!doctype html>

<html lang="fr">

<head>

<meta charset="utf-8">

<title><?= htmlspecialchars($title ?? 'Suivi des fiches') ?></title>

<style>

*{box-sizing:border-box}

body{font-family:'Segoe UI',sans-serif;background:#f0f2f5;color:#2c3e50;margin:0;padding:0 24px}

.topbar{display:flex;flex-wrap:wrap;gap:12px;align-items:center;margin:20px 0}

.topbar h1{margin:0;font-size:1.8rem;flex:1}

a.button{display:inline-block;padding:9px 16px;border-radius:6px;border:none;background:#3498db;color:#fff;font-weight:bold;text-decoration:none}

a.button:hover{background:#2980b9}

.flash{padding:10px 15px;margin:10px 0;border-radius:6px;background:#d4edda;color:#155724;font-weight:bold}

/* Barre de filtre */

.filter-bar{margin:0 0 16px;display:flex;gap:10px;align-items:center}

.filter-bar input{padding:8px 12px;border-radius:6px;border:1px solid #ccc;font-size:1rem;width:280px}

/* Tableau principal */

table{width:100%;border-collapse:collapse;background:#fff;border-radius:8px;overflow:hidden;box-shadow:0 4px 12px rgba(0,0,0,.07)}

th,td{padding:13px 16px;text-align:left}

th{background:#2c3e50;color:#fff;text-transform:uppercase;font-size:.82rem;letter-spacing:.04em}

tbody tr{cursor:pointer;transition:background .15s}

tbody tr:nth-child(even){background:#f6f9fc}

tbody tr:hover{background:#d0e8f8}

/* Badges état */

.badge{display:inline-block;padding:3px 10px;border-radius:12px;font-size:.8rem;font-weight:bold;white-space:nowrap}

.badge-cloturee{background:#e74c3c;color:#fff}

.badge-validee{background:#27ae60;color:#fff}

.badge-creee{background:#f39c12;color:#fff}

.badge-remboursee{background:#8e44ad;color:#fff}

.badge-default{background:#95a5a6;color:#fff}

/* Ligne sélectionnée */

tbody tr.selected{background:#2980b9 !important;color:#fff}

tbody tr.selected td{color:#fff}

/* Panneau détail */

#detail-panel{display:none;margin-top:24px}

#detail-panel h2{font-size:1.3rem;margin:20px 0 10px;color:#2c3e50;border-bottom:2px solid #3498db;padding-bottom:6px}

.detail-meta{background:#fff;border-radius:8px;padding:16px 20px;box-shadow:0 2px 8px rgba(0,0,0,.06);margin-bottom:10px;display:flex;flex-wrap:wrap;gap:20px}

.detail-meta p{margin:0;flex:1 1 200px}

.detail-meta strong{color:#2c3e50}

.detail-table{width:100%;border-collapse:collapse;background:#fff;border-radius:8px;overflow:hidden;box-shadow:0 2px 8px rgba(0,0,0,.06);margin-bottom:10px}

.detail-table th{background:#3498db;color:#fff;padding:11px 14px;text-align:left;font-size:.82rem;text-transform:uppercase}

.detail-table td{padding:10px 14px;border-bottom:1px solid #eee}

.detail-table tr:last-child td{border-bottom:none}

.detail-table tfoot td{background:#ecf0f1;font-weight:bold}

.section-title{margin:18px 0 8px;font-size:1.05rem;font-weight:bold;color:#2c3e50}

.total-global{background:#2c3e50;color:#fff;padding:12px 20px;border-radius:8px;font-size:1.1rem;font-weight:bold;margin-top:12px;display:inline-block}

/* Loader */

#detail-loader{text-align:center;padding:30px;display:none;color:#7f8c8d}

</style>

</head>


<?php if (empty($fiches)): ?>

<p>Aucune fiche de frais trouvée.</p>

<?php return; endif; ?>

<div class="filter-bar">
    <input
        type="text"
        id="search-visiteur"
        placeholder="🔍 Filtrer par visiteur..."
        onkeyup="filterTable()">
</div>

<table id="fiches-table">
   <thead>
    <tr>
        <th>Visiteur</th>
        <th>Mois</th>
        <th>Forfait</th>
        <th>Hors forfait</th>
        <th>Total</th>
        <th>Actions</th>
    </tr>
</thead>


<tbody>

<?php foreach ($fiches as $f):

    $forfait = (float)$f['totalForfait'];
    $horsForfait = (float)$f['totalHorsForfait'];
    $total = $forfait + $horsForfait;

    $mois = $f['mois'];
    $moisAffiche =
        substr($mois,4,2)
        . '/'
        . substr($mois,0,4);

?>

<tr
    data-nom="<?= strtolower($f['NOM'].' '.$f['PRENOM']) ?>"
>

    <td>
        <?= htmlspecialchars($f['NOM']) ?>
        <?= htmlspecialchars($f['PRENOM']) ?>
    </td>

    <td><?= $moisAffiche ?></td>

    <td><?= number_format($forfait,2,',',' ') ?> €</td>

    <td><?= number_format($horsForfait,2,',',' ') ?> €</td>

    <td>
        <strong>
            <?= number_format($total,2,',',' ') ?> €
        </strong>
    </td>

   

    <td>

        <a
            class="button"
            href="<?= BASE_URL ?>fichefrais/<?= $f['IDvisiteur'] ?>/<?= $f['mois'] ?>"
        >
            Voir
        </a>

    </td>

</tr>

<?php endforeach; ?>

</tbody>

</table>

<script>
function filterTable() {

    let q =
        document
        .getElementById('search-visiteur')
        .value
        .toLowerCase();

    document
        .querySelectorAll('#fiches-table tbody tr')
        .forEach(tr => {

            tr.style.display =
                tr.dataset.nom.includes(q)
                ? ''
                : 'none';

        });
}
</script>
