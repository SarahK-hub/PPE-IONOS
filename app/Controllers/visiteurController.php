<?php
namespace Controllers;

use Core\Controller;
use Models\visiteur;

final class visiteurController extends Controller
{
 public function index(): void
{
    // 🚫 Vérifie si l'utilisateur est connecté
    if (empty($_SESSION['user'])) {
    $this->redirect('/');
}

    // 🆕 Récupération du critère de recherche
    $search = trim($_GET['q'] ?? '');

    try {
        // Si recherche → filtrer
        if ($search !== '') {
            $visiteurs = visiteur::findBySearch($search);
        } else {
            $visiteurs = visiteur::findAll();
        }
    } catch (\Throwable $e) {
        // On stocke l'erreur dans le flash et on renvoie un tableau vide
        $_SESSION['flash'] = 'Impossible de charger les visiteurs : ' . $e->getMessage();
        $visiteurs = [];
    }

    // 🖥️ Affichage de la vue
    $this->render('visiteur/index', [
        'title'     => 'Liste des visiteurs',
        'visiteurs' => $visiteurs,
        'search'    => $search,           // pour réafficher la recherche dans le champ
        'message'   => $_SESSION['flash'] ?? '',
    ]);

    // 🔥 On vide le flash
    unset($_SESSION['flash']);
}

    public function show($id): void
    {
        if (empty($_SESSION['user'])) {
    $this->redirect('/');
}

        $id = (int)$id;

        try {
            $visiteur = visiteur::findById($id);

            if (!$visiteur) {
                http_response_code(404);
                $_SESSION['flash'] = 'visiteur introuvable.';
                $this->redirect('/visiteur');
            }
        } catch (\Throwable $e) {
            $_SESSION['flash'] = 'Erreur lors du chargement du visiteur.';
            $visiteur = null;
        }

        $this->render('visiteur/show', [
            'title'    => 'Détail du visiteur',
            'visiteur' => $visiteur,
            'message'  => $_SESSION['flash'] ?? '',
        ]);

        unset($_SESSION['flash']);
    }
    public function create(): void
{
    if (empty($_SESSION['user'])) {
    $this->redirect('/');
}

    $this->render('visiteur/create', [
        'title'   => 'Créer un visiteur',
        'message' => $_SESSION['flash'] ?? '',
        'old'     => $_SESSION['old'] ?? [
            'nom' => '',
            'prenom' => '',
            'adresse' => '',
            'ville' => '',
            'CP' => '',
            'date_embauche' => '',
            'login' => '',
            'mdp' => '',
            'roles' => '',
        ],
        'errors'  => $_SESSION['errors'] ?? [],
    ]);

    unset($_SESSION['flash'], $_SESSION['old'], $_SESSION['errors']);
}


   public function store(): void
{
   if (empty($_SESSION['user'])) {
    $this->redirect('/');
}
    $nom            = trim($_POST['nom'] ?? '');
    $prenom         = trim($_POST['prenom'] ?? '');
    $adresse        = trim($_POST['adresse'] ?? '');
    $ville          = trim($_POST['ville'] ?? '');
    $CP             = trim($_POST['CP'] ?? '');
    $date_embauche  = trim($_POST['date_embauche'] ?? '');
    $mdp            = trim($_POST['mdp'] ?? '');
    $login          = trim($_POST['login'] ?? '');
    $roles           =trim($post['roles']?? '');

    $errors = [];

    // Nom
    if ($nom === '') {
        $errors['nom'] = 'Le nom est obligatoire.';
    } elseif (mb_strlen($nom) > 100) {
        $errors['nom'] = 'Le nom ne doit pas dépasser 100 caractères.';
    }

    // Prénom
    if ($prenom === '') {
        $errors['prenom'] = 'Le prénom est obligatoire.';
    }

    // Adresse
    if ($adresse === '') {
        $errors['adresse'] = 'L’adresse est obligatoire.';
    }

    // Ville
    if ($ville === '') {
        $errors['ville'] = 'La ville est obligatoire.';
    }

    // Code postal
    if ($CP === '') {
        $errors['CP'] = 'Le code postal est obligatoire.';
    } elseif (!preg_match('/^\d{5}$/', $CP)) {
        $errors['CP'] = 'Le code postal doit contenir 5 chiffres.';
    }

    // Date d'embauche
    if ($date_embauche === '') {
        $errors['date_embauche'] = 'La date d’embauche est obligatoire.';
    }

    //login
    if ($login === '') {
        $errors['login'] = 'Le login est obligatoire.';
    }
    // mot de passe
    if ($mdp === '') {
        $errors['mdp'] = 'Le mot de passe est obligatoire.';
    }
    //roles
    if (!in_array($roles, ['visiteur', 'comptable'])) {
    $errors['roles'] = 'Rôle invalide.';
}


    if (!empty($errors)) {
        $_SESSION['errors'] = $errors;
        $_SESSION['old'] = compact(
            'nom',
            'prenom',
            'adresse',
            'ville',
            'CP',
            'date_embauche',
            'mdp',
            'login',
            'roles'
        );
        $_SESSION['flash'] = 'Merci de corriger les erreurs du formulaire.';
        $this->redirect('/visiteur/create');
    }

    try {
        \Models\Visiteur::create(
            $nom,
            $prenom,
            $adresse,
            $ville,
            $CP,
            $date_embauche,
            $login,
            $mdp,
            $login,
            $roles
            
        );

        $_SESSION['flash'] = 'Visiteur créé avec succès.';
        $this->redirect('./visiteur');
    } catch (\Throwable $e) {
        $_SESSION['flash'] = 'Impossible de créer le visiteur.';
        $this->redirect('/visiteur/create');
    }
}
public function update(int $id): void
{
   if (empty($_SESSION['user'])) {
    $this->redirect('/');
}

    $visiteur = visiteur::findById($id);

    if (!$visiteur) {
        $_SESSION['flash'] = 'Visiteur introuvable.';
        $this->redirect('/visiteur');
    }

    $this->render('visiteur/update', [
        'title'    => 'Modifier un visiteur',
        'visiteur' => $visiteur,
        'old'      => $_SESSION['old'] ?? $visiteur,
        'errors'   => $_SESSION['errors'] ?? [],
        'message'  => $_SESSION['flash'] ?? '',
    ]);

    unset($_SESSION['flash'], $_SESSION['old'], $_SESSION['errors']);
}
public function save(int $id): void
{
    if (empty($_SESSION['user'])) {
    $this->redirect('/');
}

    $nom           = trim($_POST['nom'] ?? '');
    $prenom        = trim($_POST['prenom'] ?? '');
    $adresse       = trim($_POST['adresse'] ?? '');
    $ville         = trim($_POST['ville'] ?? '');
    $CP            = trim($_POST['CP'] ?? '');
    $date_embauche = trim($_POST['date_embauche'] ?? '');
    $login         = trim($_POST['login'] ?? '');
    $roles        = trim($_POST['roles'] ?? '');

    $errors = [];

    if ($nom === '')    $errors['nom'] = 'Nom obligatoire';
    if ($prenom === '') $errors['prenom'] = 'Prénom obligatoire';
    if ($CP === '' || !preg_match('/^\d{5}$/', $CP)) {
        $errors['CP'] = 'Code postal invalide';
    }
    if (!in_array($roles, ['visiteur', 'comptable'])) {
    $errors['roles'] = 'Rôle invalide';
}

    if (!empty($errors)) {
        $_SESSION['errors'] = $errors;
        $_SESSION['old'] = compact(
            'nom','prenom','adresse','ville','CP','date_embauche','login','roles'
        );
        $this->redirect("/visiteur/$id/update");
    }

    visiteur::update(
        $id,
        $nom,
        $prenom,
        $adresse,
        $ville,
        $CP,
        $date_embauche,
        $login,
        $roles
    );

    $_SESSION['flash'] = 'Visiteur modifié avec succès.';
    $this->redirect("/visiteur/$id");
}
public function delete(int $id): void
{
   if (empty($_SESSION['user'])) {
    $this->redirect('/');
}

    visiteur::delete($id);
    $_SESSION['flash'] = 'Visiteur supprimé.';
    $this->redirect('/visiteur');
}



}
