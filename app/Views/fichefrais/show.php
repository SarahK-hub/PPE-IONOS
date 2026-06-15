<?php
$role = $role ?? ($_SESSION['user']['roles'] ?? 'visiteur');

$etatLib = strtolower(trim($fiche['etat'] ?? ''));
$readonly = in_array($etatLib, ['clôturée', 'validée', 'remboursée'], true);

$isVisiteur = ($role === 'visiteur');
$isComptable = ($role === 'comptable');
?>

<!DOCTYPE html>
<html lang="fr">
<head>
<meta charset="UTF-8">
<title>Fiche de frais</title>

<style>
body{font-family:Segoe UI,sans-serif;background:#f4f6f9;margin:0;padding:20px;color:#2c3e50}
.card{background:#fff;padding:20px;border-radius:10px;margin-bottom:20px;box-shadow:0 4px 12px rgba(0,0,0,.08)}
h1{margin:0 0 10px}
table{width:100%;border-collapse:collapse;margin-top:10px}
th,td{padding:10px;text-align:left}
th{background:#3498db;color:#fff}
tr:nth-child(even){background:#f2f6fc}
tfoot td{font-weight:bold;background:#ecf0f1}
.btn{padding:8px 14px;border-radius:6px;color:#fff;text-decoration:none;display:inline-block}
.btn-edit{background:#f39c12}
.btn-delete{background:#e74c3c}
.btn-back{background:#7f8c8d}
.locked{padding:8px 12px;background:#eee;border-radius:6px;display:inline-block}
</style>
</head>

<body>

<div class="card">

<h1>Fiche de frais</h1>

<p><strong>Visiteur :</strong>
<?= htmlspecialchars(($fiche['NOM'] ?? '') . ' ' . ($fiche['PRENOM'] ?? '')) ?>
</p>

<p><strong>Mois :</strong> <?= htmlspecialchars($fiche['mois'] ?? '') ?></p>
<p><strong>État :</strong> <?= htmlspecialchars($fiche['etat'] ?? '') ?></p>

<br>

<?php if (!$readonly): ?>
    <a class="btn btn-edit"
       href="/index.php/fichefrais/<?= $fiche['IDvisiteur'] ?>/<?= $fiche['mois'] ?>/update">
        Modifier
    </a>

    <form method="post"
          action="/index.php/fichefrais/<?= $fiche['IDvisiteur'] ?>/<?= $fiche['mois'] ?>/delete"
          style="display:inline"
          onsubmit="return confirm('Supprimer cette fiche ?');">

        <button class="btn btn-delete">Supprimer</button>
    </form>
<?php else: ?>
    <span class="locked">🔒 Consultation uniquement</span>
<?php endif; ?>

<a class="btn btn-back" href="/index.php/fichefrais">← Retour</a>

</div>

<!-- ===================== -->
<!-- FRAIS FORFAIT -->
<!-- ===================== -->

<div class="card">

<h2>Frais forfait</h2>

<?php if (empty($fraisForfait)): ?>
    <p>Aucun frais forfait enregistré.</p>
<?php else: ?>

<?php $totalFF = 0; ?>

<table>
<thead>
<tr>
    <th>Libellé</th>
    <th>Quantité</th>
    <th>Montant</th>
    <th>Total</th>
</tr>
</thead>

<tbody>
<?php foreach ($fraisForfait as $f): ?>
<?php
    $tl = (float)$f['quantite'] * (float)$f['montant'];
    $totalFF += $tl;
?>
<tr>
    <td><?= htmlspecialchars($f['libelle']) ?></td>
    <td><?= htmlspecialchars($f['quantite']) ?></td>
    <td><?= number_format((float)$f['montant'],2,',',' ') ?> €</td>
    <td><?= number_format($tl,2,',',' ') ?> €</td>
</tr>
<?php endforeach; ?>
</tbody>

<tfoot>
<tr>
    <td colspan="3"><strong>Total forfait</strong></td>
    <td><strong><?= number_format($totalFF,2,',',' ') ?> €</strong></td>
</tr>
</tfoot>

</table>

<?php endif; ?>

</div>

<!-- ===================== -->
<!-- FRAIS HORS FORFAIT -->
<!-- ===================== -->

<div class="card">

<h2>Frais hors forfait</h2>

<?php if (empty($fraisHorsForfait)): ?>
    <p>Aucun frais hors forfait enregistré.</p>
<?php else: ?>

    <?php $totalHF = 0; ?>

    <table>
        <tbody>

        <?php foreach ($fraisHorsForfait as $f): ?>

            <?php
                $id = $f['id'] ?? $f['ID'] ?? null;
                $totalHF += (float)($f['montant'] ?? 0);
            ?>

            <tr>

                <td><?= htmlspecialchars($f['libelle'] ?? '') ?></td>

                <!-- FORM CORRIGÉ SANS TOUCHER AU DESIGN -->
                <td>
                    <form method="post"
                          action="/index.php/fichefrais/<?= $fiche['IDvisiteur'] ?>/<?= $fiche['mois'] ?>/horsforfait<?= $id ?>/update">
                </td>

                <td>
                   <?php

                    $etatHF = strtolower(trim($f['etat'] ?? ''));

                    $editableMontant =
                    (
                        ($isVisiteur && $etatHF === 'créé')
                        ||
                        ($isComptable && !in_array($etatHF, ['clôturé', 'remboursé']))
                    );

                    ?>

                    <?php if ($editableMontant): ?>

                        <input type="number"
                            step="0.01"
                            name="montant"
                            value="<?= htmlspecialchars($f['montant'] ?? 0) ?>">

                    <?php else: ?>

    <?= number_format((float)$f['montant'], 2, ',', ' ') ?> €

<?php endif; ?>
                </td>

                <td>
                    <?php if ($isComptable && !$readonly): ?>

                    <select name="idEtat">
                        <option value="7" <?= ($f['idEtat'] ?? 0) == 7 ? 'selected' : '' ?>>
                            Créé
                        </option>

                        <option value="3" <?= ($f['idEtat'] ?? 0) == 3 ? 'selected' : '' ?>>
                            Validé
                        </option>

                        <option value="2" <?= ($f['idEtat'] ?? 0) == 2 ? 'selected' : '' ?>>
                            Clôturé
                        </option>

                        <option value="4" <?= ($f['idEtat'] ?? 0) == 4 ? 'selected' : '' ?>>
                            Remboursé
                        </option>
                    </select>

                <?php else: ?>

    <?= htmlspecialchars($f['etat']) ?>

<?php endif; ?>
                </td>

                <td><?= htmlspecialchars(substr($f['date_frais'] ?? '', 0, 10)) ?></td>

                <td><?= htmlspecialchars($f['dateModif'] ?? '-') ?></td>

                <td>
                <?php if ($editableMontant || ($isComptable && !$readonly)): ?>
                  <button type="submit">Enregistrer</button>
                <?php endif; ?>
                    </form>
                </td>

            </tr>

        <?php endforeach; ?>

        </tbody>

        <tfoot>
            <tr>
                <td colspan="6">
                    Total : <?= number_format($totalHF, 2, ',', ' ') ?> €
                </td>
            </tr>
        </tfoot>

    </table>

<?php endif; ?>