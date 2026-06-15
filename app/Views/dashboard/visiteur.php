
<div class="container">
<h1 class="dashboard-title">Tableau de bord - Visiteur</h1>

<p class="welcome">
    Bienvenue <?= htmlspecialchars($_SESSION['user']['login']) ?>
</p>

<div class="dashboard-grid">

    <a href="/index.php/fichefrais/create" class="card">
        <h2> Nouvelle fiche</h2>
        <p>Créer une nouvelle fiche de frais</p>
    </a>

    <a href="/index.php/fichefrais" class="card">
        <h2>Mes fiches</h2>
        <p>Consulter mes fiches de frais</p>
    </a>
    <a href="/index.php/frais_hors_forfait/create" class="card">
        <h2>Frais hors forfait</h2>
        <p>Créer un frais hors forfait</p>
    </a>


</div>