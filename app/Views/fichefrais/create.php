<?php
/** @var array $visiteurs */
/** @var array $etats */
/** @var array $frais */
/** @var string $title */
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title><?= htmlspecialchars($title ?? 'Créer une fiche de frais') ?></title>
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

<h1>Créer une fiche</h1>


<form method="post" action="<?= BASE_URL ?>fichefrais/store">


<label>Mois</label>
<select name="mois">
<?php for($i=1;$i<=12;$i++):
$mois = date('Y').sprintf('%02d',$i); ?>
<option value="<?= $mois ?>">
<?= sprintf('%02d/%s',$i,date('Y')) ?>
</option>
<?php endfor; ?>
</select>

<label>Justificatifs</label>
<input type="number" name="nbrJustificatifs">




<label>Frais hors forfait</label>
<select name="idLigneFraisHorsForfait" required>
<option value="">-- Choisir --</option>

<?php foreach($frais as $f): ?>
<option value="<?= $f['ID'] ?>">
<?= htmlspecialchars($f['libelle']) ?>, 
<?= htmlspecialchars($f['montant']) ?>€ 
</option>
<?php endforeach ?>

</select>

<label>un frais forfaitaire</label>
<br>
<label>frais forfait</label>
<select name="idfraisforfait" required>
<option value="">-- Choisir --</option>

<?php foreach($fraisforfait as $f): ?>
<option value="<?= $f['ID'] ?>">
<?= htmlspecialchars($f['libelle']) ?>, 
<?= htmlspecialchars($f['montant']) ?>€ 
</option>
<?php endforeach ?>

</select>
<label>quantité</label>
<input type="number" step="1" name="quantite">


<br>
<br>
<br>
<button type="submit">Créer</button>
</form>