<?php
namespace Controllers;

use Core\Controller;
use Models\visiteur;

final class visiteurController extends Controller
{
    public function index(): void
    {
        if (empty($_SESSION['uid'])) $this->redirect('/');

        $search = trim($_GET['q'] ?? '');

        try {
            $visiteurs = $search !== '' ? visiteur::findBySearch($search) : visiteur::findAll();
        } catch (\Throwable $e) {
            $_SESSION['flash'] = 'Impossible de charger les visiteurs.';
            $visiteurs = [];
        }

        $this->render('visiteur/index', [
            'title'     => 'Liste des visiteurs',
            'visiteurs' => $visiteurs,
            'search'    => $search,
            'message'   => $_SESSION['flash'] ?? '',
        ]);
        unset($_SESSION['flash']);
    }

    public function show(int $id): void
    {
        if (empty($_SESSION['uid'])) $this->redirect('/');

        try {
            $visiteur = visiteur::findById($id);
            if (!$visiteur) {
                $_SESSION['flash'] = 'Visiteur introuvable.';
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
        if (empty($_SESSION['uid'])) $this->redirect('/');

        $this->render('visiteur/create', [
            'title'   => 'Créer un visiteur',
            'message' => $_SESSION['flash'] ?? '',
            'old'     => $_SESSION['old'] ?? [
                'nom'           => '',
                'prenom'        => '',
                'adresse'       => '',
                'ville'         => '',
                'CP'            => '',
                'date_embauche' => '',
                'login'         => '',
                'mdp'           => '',
                'roles'         => '',
            ],
            'errors' => $_SESSION['errors'] ?? [],
        ]);
        unset($_SESSION['flash'], $_SESSION['old'], $_SESSION['errors']);
    }

    public function store(): void
    {
        if (empty($_SESSION['uid'])) $this->redirect('/');

        $nom           = trim($_POST['nom'] ?? '');
        $prenom        = trim($_POST['prenom'] ?? '');
        $adresse       = trim($_POST['adresse'] ?? '');
        $ville         = trim($_POST['ville'] ?? '');
        $CP            = trim($_POST['CP'] ?? '');
        $date_embauche = trim($_POST['date_embauche'] ?? '');
        $mdp           = trim($_POST['mdp'] ?? '');
        $login         = trim($_POST['login'] ?? '');
        $roles         = trim($_POST['roles'] ?? '');  // CORRECTION : $_POST et non $post

        $errors = [];

        if ($nom === '')    $errors['nom']    = 'Le nom est obligatoire.';
        elseif (mb_strlen($nom) > 100) $errors['nom'] = 'Le nom ne doit pas dépasser 100 caractères.';

        if ($prenom === '') $errors['prenom'] = 'Le prénom est obligatoire.';
        if ($adresse === '') $errors['adresse'] = 'L\'adresse est obligatoire.';
        if ($ville === '')  $errors['ville']  = 'La ville est obligatoire.';

        if ($CP === '') {
            $errors['CP'] = 'Le code postal est obligatoire.';
        } elseif (!preg_match('/^\d{5}$/', $CP)) {
            $errors['CP'] = 'Le code postal doit contenir 5 chiffres.';
        }

        if ($date_embauche === '') $errors['date_embauche'] = 'La date d\'embauche est obligatoire.';
        if ($login === '')  $errors['login'] = 'Le login est obligatoire.';
        if ($mdp === '')    $errors['mdp']   = 'Le mot de passe est obligatoire.';

        if (!in_array($roles, ['visiteur', 'comptable'])) {
            $errors['roles'] = 'Rôle invalide.';
        }

        if (!empty($errors)) {
            $_SESSION['errors'] = $errors;
            $_SESSION['old']    = compact('nom', 'prenom', 'adresse', 'ville', 'CP', 'date_embauche', 'login', 'roles');
            $_SESSION['flash']  = 'Merci de corriger les erreurs du formulaire.';
            $this->redirect('/visiteur/create');
        }

        try {
            visiteur::create($nom, $prenom, $adresse, $ville, $CP, $date_embauche, $login, $mdp, $roles);
            $_SESSION['flash'] = 'Visiteur créé avec succès.';
            $this->redirect('/visiteur');
        } catch (\Throwable $e) {
            $_SESSION['flash'] = 'Impossible de créer le visiteur.';
            $this->redirect('/visiteur/create');
        }
    }

    public function update(int $id): void
    {
        if (empty($_SESSION['uid'])) $this->redirect('/');

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
        if (empty($_SESSION['uid'])) $this->redirect('/');

        $nom           = trim($_POST['nom'] ?? '');
        $prenom        = trim($_POST['prenom'] ?? '');
        $adresse       = trim($_POST['adresse'] ?? '');
        $ville         = trim($_POST['ville'] ?? '');
        $CP            = trim($_POST['CP'] ?? '');
        $date_embauche = trim($_POST['date_embauche'] ?? '');
        $login         = trim($_POST['login'] ?? '');
        $roles         = trim($_POST['roles'] ?? '');
        $errors        = [];

        if ($nom === '')    $errors['nom']    = 'Nom obligatoire.';
        if ($prenom === '') $errors['prenom'] = 'Prénom obligatoire.';
        if ($CP === '' || !preg_match('/^\d{5}$/', $CP)) $errors['CP'] = 'Code postal invalide.';
        if (!in_array($roles, ['visiteur', 'comptable'])) $errors['roles'] = 'Rôle invalide.';

        if (!empty($errors)) {
            $_SESSION['errors'] = $errors;
            $_SESSION['old']    = compact('nom', 'prenom', 'adresse', 'ville', 'CP', 'date_embauche', 'login', 'roles');
            $this->redirect('/visiteur/' . $id . '/update');
        }

        try {
            visiteur::update($id, $nom, $prenom, $adresse, $ville, $CP, $date_embauche, $login, $roles);
            $_SESSION['flash'] = 'Visiteur modifié avec succès.';
            $this->redirect('/visiteur/' . $id);
        } catch (\Throwable $e) {
            $_SESSION['flash'] = 'Impossible de modifier le visiteur.';
            $this->redirect('/visiteur/' . $id . '/update');
        }
    }

    public function delete(int $id): void
    {
        if (empty($_SESSION['uid'])) $this->redirect('/');

        try {
            visiteur::delete($id);
            $_SESSION['flash'] = 'Visiteur supprimé.';
        } catch (\Throwable $e) {
            $_SESSION['flash'] = 'Impossible de supprimer le visiteur.';
        }

        $this->redirect('/visiteur');
    }
}
