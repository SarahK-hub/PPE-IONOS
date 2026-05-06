<!DOCTYPE html>
<html>
<head>
<meta charset="UTF-8">
<title>Fiche frais</title>

<style>
body{font-family:Segoe UI;background:#f4f6f9;margin:0;padding:20px}

.card{
background:#fff;
padding:20px;
border-radius:10px;
box-shadow:0 5px 15px rgba(0,0,0,0.08);
margin-bottom:20px;
}

h1{margin-bottom:10px}

table{
width:100%;
border-collapse:collapse;
margin-top:10px;
}

th,td{
padding:12px;
text-align:left;
}

th{
background:#3498db;
color:white;
}

tr:nth-child(even){background:#f2f2f2}

.total{
text-align:right;
font-size:18px;
font-weight:bold;
margin-top:10px;
}

.btn{
display:inline-block;
padding:10px 15px;
margin-right:10px;
border-radius:6px;
text-decoration:none;
color:white;
font-weight:bold;
}

.btn-edit{background:#f39c12}
.btn-delete{background:#e74c3c}
.btn-back{background:#7f8c8d}

.btn:hover{opacity:0.8}
</style>

</head>
<body>

<div class="card">
<h1>Fiche de frais</h1>

<p><strong>Visiteur :</strong> <?= $fiche['IDvisiteur'] ?></p>

<?php
$date = DateTime::createFromFormat('Ym', $fiche['mois']);
?>

<p><strong>Mois :</strong> <?= $date->format('m/Y') ?></p>
<p><strong>Etat :</strong> <?= htmlspecialchars($fiche['etat']) ?></p>

<a class="btn btn-edit"
href="/fichefrais/<?= $fiche['IDvisiteur'] ?>/<?= $fiche['mois'] ?>/update">
Modifier
</a>

<form method="post"
action="/fichefrais/<?= $fiche['IDvisiteur'] ?>/<?= $fiche['mois'] ?>/delete"
style="display:inline">
<button class="btn btn-delete">Supprimer</button>
</form>

<a class="btn btn-back" href="/fichefrais">Retour</a>

</div>

<!-- FRAIS FORFAIT -->
<div class="card">
<h2>Frais forfait</h2>

<table>
<tr>
<th>Libellé</th>
<th>Quantité</th>
<th>Montant</th>
<th>Total</th>
</tr>

<?php $totalFF = 0; ?>
<?php foreach($fraisForfait as $f): 
$ligneTotal = $f['quantite'] * $f['montant'];
$totalFF += $ligneTotal;
?>

<tr>
<td><?= htmlspecialchars($f['libelle']) ?></td>
<td><?= $f['quantite'] ?></td>
<td><?= $f['montant'] ?> €</td>
<td><?= $ligneTotal ?> €</td>
</tr>

<?php endforeach; ?>
</table>

<div class="total">Total forfait : <?= $totalFF ?> €</div>

</div>

<!-- HORS FORFAIT -->
<div class="card">
<h2>Frais hors forfait</h2>

<table>
<tr>
<th>Date</th>
<th>Libellé</th>
<th>Montant</th>
</tr>

<?php $totalHF = 0; ?>

<?php foreach($fraisHorsForfait as $f): 
$totalHF += $f['montant'];
?>

<tr>
<td><?= $f['date_frais'] ?></td>
<td><?= htmlspecialchars($f['libelle']) ?></td>
<td><?= $f['montant'] ?> €</td>
</tr>

<?php endforeach; ?>

</table>

<div class="total">Total hors forfait : <?= $totalHF ?> €</div>

</div>

<!-- TOTAL GLOBAL -->
<div class="card">
<h2>Total global : <?= $totalFF + $totalHF ?> €</h2>
</div>

</body>
</html>