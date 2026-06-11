<?php
$isComptable = ($_SESSION['user']['roles'] ?? '') === 'comptable';
$isVisiteur = ($_SESSION['user']['roles'] ?? '') === 'visiteur';
$etat = strtolower(trim($fiche['etat'] ?? ''));
$isEditableVisiteur = ($etat === 'créé' || $etat === 'cree');
?>

<!DOCTYPE html>
<html lang="fr">
<head>
<meta charset="UTF-8">
<title>Modifier fiche</title>

<style>
body{font-family:Segoe UI,sans-serif;background:#f4f6f9;padding:20px}
.card{background:#fff;padding:20px;border-radius:10px}
.field{margin-bottom:15px}
label{font-weight:bold;display:block;margin-bottom:5px}
input,select{width:100%;padding:8px;border:1px solid #ccc;border-radius:6px}
button{background:#3498db;color:#fff;padding:10px 15px;border:none;border-radius:6px}
</style>
</head>

<body>

<div class="card">

<h1>Modifier fiche de frais</h1>

<form method="post"
      action="<?= BASE_URL ?>fichefrais/<?= $fiche['IDvisiteur'] ?>/<?= $fiche['mois'] ?>/update">

<!-- ===================== -->
<!-- 👤 VISITEUR -->
<!-- ===================== -->

<?php if ($isVisiteur && $isEditableVisiteur): ?>

<div class="field">
    <label>Libellé (hors forfait lié)</label>
    <input type="text" name="libelle"
           value="<?= htmlspecialchars($fiche['libelle'] ?? '') ?>">
</div>

<div class="field">
    <label>Montant</label>
    <input type="number" step="0.01" name="montant"
           value="<?= htmlspecialchars($fiche['montant'] ?? 0) ?>">
</div>

<?php else: ?>

<p>Modification non autorisée (fiche non modifiable ou état verrouillé)</p>

<?php endif; ?>

<!-- ===================== -->
<!-- 👨‍💼 COMPTABLE -->
<!-- ===================== -->

<?php if (
    $isComptable
    && $etat !== 'clôturée'
    && $etat !== 'remboursée'
): ?>

<div class="field">
    <label>État</label>
    <select name="idEtat">
        <?php foreach($etats as $e): ?>
            <option value="<?= $e['id'] ?>"
                <?= ($fiche['idEtat'] == $e['id']) ? 'selected' : '' ?>>
                <?= htmlspecialchars($e['libelle']) ?>
            </option>
        <?php endforeach ?>
    </select>
</div>

<?php endif; ?>

<button type="submit">Enregistrer</button>

</form>

</div>

</body>
</html>