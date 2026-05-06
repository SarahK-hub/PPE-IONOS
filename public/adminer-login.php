<?php
$env = parse_ini_file(__DIR__ . '/../.env');

$server = $env['DB_HOST'] ?? '';
$user   = $env['DB_USER'] ?? '';
$pass   = $env['DB_PASSWORD'] ?? '';
$db     = $env['DB_NAME'] ?? '';
?>

<form method="post" action="adminer.php">
    <input type="hidden" name="auth[driver]" value="server">
    <input type="hidden" name="auth[server]" value="<?= htmlspecialchars($server) ?>">
    <input type="hidden" name="auth[username]" value="<?= htmlspecialchars($user) ?>">
    <input type="hidden" name="auth[password]" value="<?= htmlspecialchars($pass) ?>">
    <input type="hidden" name="auth[db]" value="<?= htmlspecialchars($db) ?>">

    <button type="submit">
        Accéder à la base de données
    </button>
</form>