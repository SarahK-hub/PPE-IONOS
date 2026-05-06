<!doctype html>
<html lang="fr">
<head>
<meta charset="utf-8">
<title><?= htmlspecialchars($title ?? 'Fiches de frais') ?></title>

<style>
body{font-family:'Segoe UI',Tahoma,Geneva,Verdana,sans-serif;background:#f0f2f5;color:#2c3e50;margin:0;padding:0 20px}

.topbar{display:flex;flex-wrap:wrap;gap:12px;align-items:center;margin:20px 0}
.topbar h1{margin:0;font-size:1.8rem;flex:1}

a.button,button,input[type=submit]{display:inline-block;padding:10px 16px;border-radius:6px;border:none;background:#3498db;color:#fff;font-weight:bold;cursor:pointer;text-decoration:none;transition:.3s,.2s}
a.button:hover,button:hover,input[type=submit]:hover{background:#2980b9;transform:translateY(-2px)}

.flash{padding:10px 15px;margin:15px 0;border-radius:6px;font-weight:bold}
.flash-success{background:#2ecc71;color:#fff}
.flash-error{background:#e74c3c;color:#fff}

table{width:100%;max-width:1000px;border-collapse:collapse;border-radius:6px;overflow:hidden;box-shadow:0 4px 8px rgba(0,0,0,.05);background:#fff}
th,td{padding:12px 15px;text-align:left}
th{background:#3498db;color:#fff;text-transform:uppercase;font-size:.9rem}
tr:nth-child(even){background:#f2f6fc}
tr:hover{background:#d6eaf8}
td a{color:#3498db;font-weight:bold;text-decoration:none}
td a:hover{text-decoration:underline}

form{max-width:500px;background:#fff;padding:20px;border-radius:6px;box-shadow:0 4px 10px rgba(0,0,0,.05);margin-bottom:30px}
.field{margin-bottom:15px}
label{display:block;margin-bottom:5px;font-weight:bold}
input[type=text],input[type=number],textarea{width:100%;padding:8px 12px;border-radius:6px;border:1px solid #ccc;font-size:1rem;box-sizing:border-box}
input:focus,textarea:focus{outline:none;border-color:#3498db}

.error{color:#e74c3c;font-size:.9rem;margin-top:4px}

@media(max-width:600px){
.topbar{flex-direction:column;align-items:flex-start}
table,th,td{font-size:.9rem}
input,button,a.button{width:100%;margin-bottom:10px}
td a{display:inline-block;margin-bottom:5px}
}
</style>

</head>

<body>

<div class="topbar">

<h1 style="flex:1">Fiches de frais</h1>

<a class="button" href="/dashboard">Dashboard</a>

<a class="button" href="/fichefrais/create">Créer une fiche</a>

<a class="button" href="/logout">Déconnexion</a>

</div>

<?php if(empty($fiches)): ?>

<p>Aucune fiche trouvée.</p>

<?php else: ?>

<table>

<thead>
<tr>
<th>Visiteur</th>
<th>Mois</th>
<th>Justificatifs</th>
<th>Montant</th>
<th>Etat</th>
<th>Actions</th>
</tr>
</thead>

<tbody>

<?php foreach($fiches as $f): ?>

<tr>

<td><?= htmlspecialchars($f['IDvisiteur'] ?? '') ?></td>

<td>

<?php
$mois = $f['mois'] ?? '';
$moisAffichage = substr($mois,4,2).'/'.substr($mois,0,4);
?>

<?= htmlspecialchars($moisAffichage) ?>

</td>

<td><?= htmlspecialchars($f['nbrJustificatifs'] ?? '') ?></td>

<td><?= htmlspecialchars($f['montantValide'] ?? '') ?> €</td>

<td><?= htmlspecialchars($f['etat'] ?? '') ?></td>

<td>

<a class="button"
href="/fichefrais/<?= $f['IDvisiteur'] ?>/<?= $f['mois'] ?>">
Voir
</a>

<a class="button"
href="/fichefrais/<?= $f['IDvisiteur'] ?>/<?= $f['mois'] ?>/update">
Modifier
</a>

<form method="post"
action="/fichefrais/<?= $f['IDvisiteur'] ?>/<?= $f['mois'] ?>/delete"
style="display:inline"
onsubmit="return confirm('Supprimer cette fiche ?');">

<button type="submit">Supprimer</button>

</form>

</td>

</tr>

<?php endforeach ?>

</tbody>

</table>

<?php endif ?>

</body>
</html>