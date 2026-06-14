<!doctype html>
<html lang="fr">
<head>
    <meta charset="utf-8">
    <title><?= htmlspecialchars($title ?? 'Visiteurs') ?></title>
    <meta name="viewport" content="width=device-width, initial-scale=1">
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

@media(max-width:600px){
.topbar{flex-direction:column;align-items:flex-start}
table,th,td{font-size:.9rem}
a.button,button{width:100%;margin-bottom:6px}
}
</style>
</head>

<body>

<div class="topbar">
    <h1>Liste des visiteurs</h1>
    <a class="button" href="/index.php/dashboard">Dashboard</a>
    <a class="button" href="/index.php/logout">Se déconnecter</a>
    <a class="button" href="/index.php/visiteur/create">Créer un visiteur</a>
</div>

<!-- Recherche -->
<form method="get" action="/index.php/visiteur" style="margin-bottom:16px;">
    <input
        type="text"
        name="q"
        placeholder="Rechercher par nom ou prénom"
        value="<?= htmlspecialchars($search ?? '', ENT_QUOTES) ?>"
        style="padding:6px;width:220px;"
    >
    <button type="submit" class="button">Rechercher</button>
</form>

<?php if (!empty($message)): ?>
    <div class="flash"><?= htmlspecialchars($message) ?></div>
<?php endif; ?>

<?php if (empty($visiteurs)): ?>
    <p>Aucun visiteur trouvé.</p>
<?php else: ?>
    <table>
        <thead>
            <tr>
                <th>ID</th>
                <th>Nom</th>
                <th>Prénom</th>
                <th>Actions</th>
                <th>Rôle</th>
            </tr>
        </thead>
        <tbody>
        <?php foreach ($visiteurs as $v): ?>
            <tr>
                <td><?= htmlspecialchars($v['ID']) ?></td>
                <td><?= htmlspecialchars($v['NOM']) ?></td>
                <td><?= htmlspecialchars($v['PRENOM']) ?></td>
                <td><?= htmlspecialchars($v['roles']) ?></td>
                <td>
                    <a class="button" href="/index.php/visiteur/<?= (int)$v['ID'] ?>/update">Modifier</a>

                    <form method="post"
                          action="/index.php/visiteur/<?= (int)$v['ID'] ?>/delete"
                          style="display:inline"
                          onsubmit="return confirm('Supprimer ce visiteur ?');">
                        <button type="submit">Supprimer</button>
                    </form>
                </td>
            </tr>
        <?php endforeach; ?>
        </tbody>
    </table>
<?php endif; ?>

</body>
</html>
