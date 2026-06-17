<h1 class="dashboard-title">Tableau de bord - Comptable</h1>

<p class="welcome">
    Bienvenue <?= htmlspecialchars($_SESSION['user']['login']) ?>
</p>

<div class="dashboard-grid">

    <a href="index.php/fichefrais" class="card">
        <h2> Suivi des fiches</h2>
        <p>Valider et suivre les fiches de frais</p>
    </a>

    <a href="/index.php/visiteur" class="card">
        <h2> Gérer les visiteurs</h2>
        <p>Ajouter, modifier ou supprimer</p>
    </a>

 <a href="/index.php/etat" class="card">
        <h2> Gérer les etats</h2>
        <p>Ajouter, modifier ou supprimer</p>
    </a>


</div>