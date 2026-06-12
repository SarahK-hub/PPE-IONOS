<?php
/**
 * Vue liste des fiches — rôle VISITEUR
 * Affiche uniquement les fiches du visiteur connecté.
 */
?>
<!doctype html>
<html lang="fr">
<head>
<meta charset="utf-8">
<title><?= htmlspecialchars($title ?? 'Mes fiches de frais') ?></title>
<style>
body{font-family:'Segoe UI',sans-serif;background:#f0f2f5;color:#2c3e50;margin:0;padding:0 20px}
.topbar{display:flex;flex-wrap:wrap;gap:12px;align-items:center;margin:20px 0}
.topbar h1{margin:0;font-size:1.8rem;flex:1}
a.button,button{display:inline-block;padding:10px 16px;border-radius:6px;border:none;background:#3498db;color:#fff;font-weight:bold;cursor:pointer;text-decoration:none}
a.button:hover,button:hover{background:#2980b9}
.flash{padding:10px 15px;margin:10px 0;border-radius:6px;background:#d4edda;color:#155724;font-weight:bold}
table{width:100%;border-collapse:collapse;background:#fff;border-radius:6px;overflow:hidden;box-shadow:0 4px 8px rgba(0,0,0,.05)}
th,td{padding:12px 15px;text-align:left}
th{background:#3498db;color:#fff;text-transform:uppercase;font-size:.85rem}
tr:nth-child(even){background:#f2f6fc}
tr:hover{background:#d6eaf8}
.badge{display:inline-block;padding:3px 10px;border-radius:12px;font-size:.82rem;font-weight:bold}
.badge-cloturee{background:#e74c3c;color:#fff}
.badge-validee{background:#27ae60;color:#fff}
.badge-creee{background:#f39c12;color:#fff}
.badge-remboursee{background:#8e44ad;color:#fff}
.badge-default{background:#95a5a6;color:#fff}
</style>
</head>
<body>

<div class="topbar">
  <h1><?= htmlspecialchars($title ?? 'Mes fiches de frais') ?></h1>
  <a class="button" href="/index.php/dashboard/visiteur">Dashboard</a>
  <a class="button" href="/index.php/fichefrais/create">Nouvelle fiche</a>
  <a class="button" href="/index.php/logout">Déconnexion</a>
</div>

<?php if (!empty($_SESSION['flash'])): ?>
  <div class="flash"><?= htmlspecialchars($_SESSION['flash']) ?></div>
  <?php unset($_SESSION['flash']); ?>
<?php endif; ?>

<?php if (empty($fiches)): ?>
  <p>Vous n'avez aucune fiche de frais pour le moment.</p>
<?php else: ?>

<table>
  <thead>
    <tr>
      <th>Mois</th>
      <th>Justificatifs</th>
      <th>Montant validé</th>
      <th>État</th>
      <th>Actions</th>
    </tr>
  </thead>
  <tbody>
  <?php foreach ($fiches as $f):
      $moisStr = (string)($f['mois'] ?? '');
      $moisAff = strlen($moisStr) === 6 ? substr($moisStr,4,2).'/'.substr($moisStr,0,4) : $moisStr;
      $etat    = $f['etat'] ?? '';
      $cloture = strtolower(trim($etat)) === 'clôturée';
      $valide = strtolower(trim($etat)) === 'validée';
     
      $badgeClass = match(strtolower(trim($etat))) {
          'clôturée'   => 'badge-cloturee',
          'validée'    => 'badge-validee',
          'créé','créée' => 'badge-creee',
          'remboursée' => 'badge-remboursee',
          default      => 'badge-default',
      };
  ?>
    <tr>
      <td><?= htmlspecialchars($moisAff) ?></td>
      <td><?= htmlspecialchars($f['nbrJustificatifs'] ?? '') ?></td>
      <td><?= number_format((float)($f['montantValide'] ?? 0), 2, ',', ' ') ?> €</td>
      <td><span class="badge <?= $badgeClass ?>"><?= htmlspecialchars($etat) ?></span></td>
      <td>
        <a class="button"
          href="/index.php/fichefrais/<?= $f['IDvisiteur'] ?>/<?= $f['mois'] ?>">
            Voir
        </a>
        <?php if (!$cloture && !$valide ): ?>
          <a class="button" href="/fichefrais/<?= $f['IDvisiteur'] ?>/<?= $f['mois'] ?>/update">Modifier</a>
          <form method="post"
                action="/index.php/fichefrais/<?= $f['IDvisiteur'] ?>/<?= $f['mois'] ?>/delete"
                style="display:inline"
                onsubmit="return confirm('Supprimer cette fiche ?');">
            <button type="submit" style="background:#e74c3c">Supprimer</button>
          </form>
        <?php else: ?>
          <span style="color:#e74c3c;font-weight:bold;margin-left:8px">🔒 consultation uniquement</span>
        <?php endif; ?>
      </td>
    </tr>
  <?php endforeach; ?>
  </tbody>
</table>

<?php endif; ?>
</body>
</html>
