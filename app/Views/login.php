<?php
$e = fn($s)=>htmlspecialchars((string)$s, ENT_QUOTES|ENT_SUBSTITUTE, 'UTF-8');
?>
<h2>Connexion</h2>
<?php if (!empty($message)): ?><p style="color:red"><?= $e($message) ?></p><?php endif; ?>
<form method="post" action="login">
  <label>Utilisateur <input name="username" required></label><br>
  <label>Mot de passe <input type="password" name="password" required></label><br>
  <input type="hidden" name="csrf" value="<?= $e($csrf) ?>">
  <button>Se connecter</button>
</form>
