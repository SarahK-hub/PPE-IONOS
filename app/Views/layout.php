<?php /** @var string $title */ ?>
<!doctype html>
<html lang="fr">
<head>
<meta charset="utf-8">
<meta name="viewport" content="width=device-width, initial-scale=1">
<title><?= htmlspecialchars($title ?? 'GSB') ?></title>

<style>

/* RESET */
body {
    font-family: Arial, sans-serif;
    margin: 0;
    background: #eef1f5;
}
.logo {
    width: 100px;
    display:block;
    margin: 0  auto;
    text-align: left;
   
}
/* WRAPPER GLOBAL LOGIN */
.login-wrapper {
    text-align: center;
}

/* GROS LOGO */
.logo-big {
    width: 180px;
    border-radius: 20px; /* coins arrondis */
    margin-bottom: 20px;
    box-shadow: 0 8px 20px rgba(0,0,0,0.2);
}

/* OPTION : effet au survol */
.logo-big:hover {
    transform: scale(1.05);
    transition: 0.3s;
}
/* DASHBOARD */
.dashboard-title {
    font-size: 26px;
    margin-bottom: 10px;
    color: #2c3e50;
}

.welcome {
    margin-bottom: 25px;
    color: #555;
}

/* GRID */
.dashboard-grid {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
    gap: 20px;
}

/* CARD */
.card {
    background: white;
    padding: 20px;
    border-radius: 10px;
    text-decoration: none;
    color: #2c3e50;
    box-shadow: 0 5px 15px rgba(0,0,0,0.1);
    transition: 0.3s;
}

.card h2 {
    margin-bottom: 10px;
}

.card p {
    color: #777;
}

/* HOVER */
.card:hover {
    transform: translateY(-5px);
    background: #3498db;
    color: white;
}

.card:hover p {
    color: white;
}
/* ========================= */
/* HEADER */
/* ========================= */
.header {
    background: #2c3e50;
    color: white;
    padding: 15px;
    font-size: 20px;
}
/* HEADER */
.header {
    background: #2c3e50;
    color: white;
    padding: 10px 20px;
}

/* ALIGNEMENT LOGO + TEXTE */
.header-left {
    display: flex;
    align-items: center;
    gap: 12px;
}

/* LOGO PETIT */
.header-left img {
    height: 40px;
    width: auto;
    border-radius: 6px;
}

/* TEXTE */
.header-left span {
    font-size: 20px;
    font-weight: bold;
}
.header-left img {
    height: 40px;
    border-radius: 8px;
    background: white;
    padding: 3px;
}

/* ========================= */
/* MENU */
/* ========================= */
.menu {
    background: #34495e;
    padding: 10px 20px;
    display: flex;
    align-items: center;
    flex-wrap: wrap;
    gap: 4px;
}

.menu a {
    color: white;
    margin-right: 15px;
    text-decoration: none;
    font-weight: bold;
}

.menu a:hover {
    text-decoration: underline;
}

/* ========================= */
/* CONTENU */
/* ========================= */
.container {
    padding: 20px;
}

/* ========================= */
/* TABLE */
/* ========================= */
table {
    width: 100%;
    border-collapse: collapse;
    background: white;
}

th {
    background: #3498db;
    color: white;
}

td, th {
    padding: 10px;
    border: 1px solid #ddd;
}

/* ========================= */
/* BOUTONS */
/* ========================= */
.btn {
    padding: 6px 10px;
    border: none;
    background: #3498db;
    color: white;
    cursor: pointer;
    border-radius: 4px;
}

.btn.green { background: #2ecc71; }
.btn.red { background: #e74c3c; }

/* ========================= */
/* BADGES */
/* ========================= */
.badge {
    padding: 4px 8px;
    border-radius: 4px;
    color: white;
}

.cl { background: orange; }
.va { background: blue; }
.pa { background: green; }

/* ========================= */
/* LOGIN PAGE UNIQUEMENT */
/* ========================= */
.login-page {
    background: linear-gradient(135deg, #2c3e50, #3498db);
    height: 100vh;
    display: flex;
    justify-content: center;
    align-items: center;
}

/* désactive le padding sur login */
.login-page .container {
    padding: 0;
}

/* ========================= */
/* LOGIN BOX */
/* ========================= */
.login-box {
    background: white;
    padding: 30px;
    width: 320px;
    border-radius: 10px;
    box-shadow: 0 10px 25px rgba(0,0,0,0.2);
    text-align: center;
}

.login-box h1 {
    margin-bottom: 20px;
    color: #2c3e50;
}

.login-box input {
    width: 100%;
    padding: 10px;
    margin: 10px 0;
    border-radius: 6px;
    border: 1px solid #ccc;
}

.login-box input:focus {
    border-color: #3498db;
    outline: none;
}

.login-box button {
    width: 100%;
    padding: 10px;
    background: #3498db;
    border: none;
    color: white;
    font-weight: bold;
    border-radius: 6px;
    cursor: pointer;
}

.login-box button:hover {
    background: #2980b9;
}

/* MESSAGE */
.flash {
    background: #e74c3c;
    color: white;
    padding: 8px;
    border-radius: 5px;
    margin-bottom: 10px;
}

</style>
</head>


<!-- ✅ CLASSE DYNAMIQUE -->
<body class="<?= empty($_SESSION['user']) ? 'login-page' : '' ?>">

<!-- HEADER -->
<?php if (!empty($_SESSION['user'])): ?>
<div class="header">
    <div class="header-left">
        <img src="/logo-gsb.png" alt="GSB">
        <span>GSB - Gestion des frais</span>
    </div>
</div>
<?php endif; ?>

<!-- MENU -->
<?php if (!empty($_SESSION['user'])): ?>
<div class="menu">

<?php if ($_SESSION['user']['roles'] === 'visiteur'): ?>

    <a href="/index.php/fichefrais/create">Nouvelle fiche</a>
    <a href="/index.php/fichefrais">Mes fiches</a>
    <a href="/index.php/logout" style="margin-left:auto;color:#e74c3c;font-weight:bold;">⎋ Déconnexion</a>

<?php else: ?>
    <a href="/index.php/etat">etat</a>
    <a href="/index.php/fichefrais">Suivi fiches</a>
    <a href="/index.php/visiteur">Visiteurs</a>
    <a href="/index.php/logout" style="margin-left:auto;color:#e74c3c;font-weight:bold;">⎋ Déconnexion</a>

<?php endif; ?>

</div>
<?php endif; ?>

<!-- CONTENU -->
<div class="container">
    <?php require $viewFile; ?>
</div>

</body>
</html>