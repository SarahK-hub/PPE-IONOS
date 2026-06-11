<?php
// Variables disponibles :
// $title   : titre de la page ("Créer un visiteur")
// $message : message flash éventuel
// $old     : valeurs précédentes du formulaire (['nom' => '...'])
// $errors  : erreurs de validation (['nom' => '...'])
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title><?= htmlspecialchars($title ?? 'Créer un visiteur', ENT_QUOTES, 'UTF-8'); ?></title>
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

<h1><?= htmlspecialchars($title ?? 'Créer un visiteur', ENT_QUOTES, 'UTF-8'); ?></h1>

<?php if (!empty($message)): ?>
    <div class="flash">
        <?= htmlspecialchars($message, ENT_QUOTES, 'UTF-8'); ?>
    </div>
<?php endif; ?>
<form method="post" action="<?= BASE_URL ?>visiteur/store">


    <!-- Nom -->
    <div class="field">
        <label for="nom">Nom</label>
        <input
            type="text"
            name="nom"
            id="nom"
            value="<?= htmlspecialchars($old['nom'] ?? '', ENT_QUOTES, 'UTF-8'); ?>"
            required
        >
        <?php if (!empty($errors['nom'])): ?>
            <div class="error">
                <?= htmlspecialchars($errors['nom'], ENT_QUOTES, 'UTF-8'); ?>
            </div>
        <?php endif; ?>
    </div>

    <!-- Prénom -->
    <div class="field">
        <label for="prenom">Prénom</label>
        <input
            type="text"
            name="prenom"
            id="prenom"
            value="<?= htmlspecialchars($old['prenom'] ?? '', ENT_QUOTES, 'UTF-8'); ?>"
            required
        >
        <?php if (!empty($errors['prenom'])): ?>
            <div class="error">
                <?= htmlspecialchars($errors['prenom'], ENT_QUOTES, 'UTF-8'); ?>
            </div>
        <?php endif; ?>
    </div>

    <!-- Adresse -->
    <div class="field">
        <label for="adresse">Adresse</label>
        <input
            type="text"
            name="adresse"
            id="adresse"
            value="<?= htmlspecialchars($old['adresse'] ?? '', ENT_QUOTES, 'UTF-8'); ?>"
            required
        >
        <?php if (!empty($errors['adresse'])): ?>
            <div class="error">
                <?= htmlspecialchars($errors['adresse'], ENT_QUOTES, 'UTF-8'); ?>
            </div>
        <?php endif; ?>
    </div>

    <!-- Ville -->
    <div class="field">
        <label for="ville">Ville</label>
        <input
            type="text"
            name="ville"
            id="ville"
            value="<?= htmlspecialchars($old['ville'] ?? '', ENT_QUOTES, 'UTF-8'); ?>"
            required
        >
        <?php if (!empty($errors['ville'])): ?>
            <div class="error">
                <?= htmlspecialchars($errors['ville'], ENT_QUOTES, 'UTF-8'); ?>
            </div>
        <?php endif; ?>
    </div>

    <!-- Code postal -->
    <div class="field">
        <label for="CP">Code postal</label>
        <input
            type="text"
            name="CP"
            id="CP"
            value="<?= htmlspecialchars($old['CP'] ?? '', ENT_QUOTES, 'UTF-8'); ?>"
            required
        >
        <?php if (!empty($errors['CP'])): ?>
            <div class="error">
                <?= htmlspecialchars($errors['CP'], ENT_QUOTES, 'UTF-8'); ?>
            </div>
        <?php endif; ?>
    </div>

    <!-- Date d'embauche -->
    <div class="field">
        <label for="date_embauche">Date d’embauche</label>
        <input
            type="date"
            name="date_embauche"
            id="date_embauche"
            value="<?= htmlspecialchars($old['date_embauche'] ?? '', ENT_QUOTES, 'UTF-8'); ?>"
            required
        >
        <?php if (!empty($errors['date_embauche'])): ?>
            <div class="error">
                <?= htmlspecialchars($errors['date_embauche'], ENT_QUOTES, 'UTF-8'); ?>
            </div>
        <?php endif; ?>
    </div>
     <!-- mdp-->
    <div class="field">
        <label for="mdp">mot de passe</label>
        <input
            type="text"
            name="mdp"
            id="mdp"
            value="<?= htmlspecialchars($old['mdp'] ?? '', ENT_QUOTES, 'UTF-8'); ?>"
            required
        >
        <?php if (!empty($errors['mdp'])): ?>
            <div class="error">
                <?= htmlspecialchars($errors['mdp'], ENT_QUOTES, 'UTF-8'); ?>
            </div>
        <?php endif; ?>
    </div>
     <!-- login -->
    <div class="field">
        <label for="login">Login</label>
        <input
            type="text"
            name="login"
            id="login"
            value="<?= htmlspecialchars($old['login'] ?? '', ENT_QUOTES, 'UTF-8'); ?>"
            required
        >
        <?php if (!empty($errors['login'])): ?>
            <div class="error">
                <?= htmlspecialchars($errors['login'], ENT_QUOTES, 'UTF-8'); ?>
            </div>
        <?php endif; ?>
    </div>
 <!-- roles -->
       <div class="field">
        <label for="login">Rôle</label>
                <select name="roles" required>
                    <option value="visiteur" <?= ($old['roles'] ?? '') === 'visiteur' ? 'selected' : '' ?>>visiteur</option>
                    <option value="comptable" <?= ($old['roles'] ?? '') === 'comptable' ? 'selected' : '' ?>>comptable</option>
                </select>

                <?php if (!empty($errors['roles'])): ?>
                    <div class="error">
                        <?= htmlspecialchars($errors['roles']) ?>
                    </div>
                <?php endif; ?>

    </div>
                    <button type="submit">Enregistrer</button>
                    <a href="/visiteur">Annuler</a>

</form>

</body>
</html>

</html>