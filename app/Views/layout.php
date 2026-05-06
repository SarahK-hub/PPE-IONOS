<?php /** @var string $title */ ?>
<!doctype html>
<html lang="fr">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width,initial-scale=1">
  <title><?= htmlspecialchars($title ?? 'GSB', ENT_QUOTES) ?></title>
  <style>
    body { font-family: Arial, sans-serif; margin: 0; background: #f4f6f9; }
    .navbar { background: #2c3e50; padding: 15px; }
    .navbar a { color: white; margin-right: 15px; text-decoration: none; font-weight: bold; }
    .navbar a:hover { text-decoration: underline; }
    .container { padding: 20px; }
    .flash { background: #d4edda; border: 1px solid #c3e6cb; color: #155724; padding: 10px 15px; border-radius: 4px; margin-bottom: 15px; }
    .flash.error { background: #f8d7da; border-color: #f5c6cb; color: #721c24; }
  </style>
</head>
<body>

<?php if (!empty($_SESSION['uid'])): ?>
  <div class="navbar">
    <strong style="color:white;">GSB</strong>

    <?php $role = $_SESSION['user']['roles'] ?? 'visiteur'; ?>

    <?php if ($role === 'visiteur'): ?>
      <a href="/dashboard/visiteur">Dashboard</a>
      <a href="/fichefrais/create">Saisir fiche</a>
      <a href="/mes-fiches">Mes fiches</a>
    <?php else: ?>
      <a href="/dashboard/comptable">Dashboard</a>
      <a href="/visiteur">Gérer visiteurs</a>
      <a href="/fichefrais">Suivi fiches</a>
      <a href="/etat">États</a>
      <a href="/fraisforfait">Frais forfait</a>
      <a href="/frais_hors_forfait">Frais hors forfait</a>
    <?php endif; ?>

    <a href="/logout">Déconnexion</a>
  </div>
<?php endif; ?>

<div class="container">
  <?php if (!empty($message)): ?>
    <div class="flash"><?= htmlspecialchars($message, ENT_QUOTES) ?></div>
  <?php endif; ?>

  <?php require $viewFile; ?>
</div>

</body>
</html>
