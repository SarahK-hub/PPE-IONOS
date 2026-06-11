<?php
namespace Controllers;

use Core\Controller;
use Models\Etat;

final class EtatController extends Controller
{
    public function index(): void
    {
        if (empty($_SESSION['uid'])) {
            $this->redirect('/index.php');
        }

        try {
            $etats = Etat::findAll(); // appel statique aligné avec le modèle
        } catch (\Throwable $e) {
            // Pour déboguer, active temporairement la ligne suivante :
            // error_log($e->getMessage());
            $_SESSION['flash'] = 'Impossible de charger les états.';
            $etats = [];
        }

        $this->render('etat/index', [
            'title'   => 'Liste des États',
            'etats'   => $etats,
            'message' => $_SESSION['flash'] ?? '',
        ]);
        unset($_SESSION['flash']);
    }


    public function show($id): void
    {
        if (empty($_SESSION['uid'])) $this->redirect('/index.php');

        $id = (int)$id;

        try {
            $etat = \Models\Etat::findById($id);
            if (!$etat) {
                http_response_code(404);
                $_SESSION['flash'] = 'État introuvable.';
                $this->redirect('/index.php/etat');
            }
        } catch (\Throwable $e) {
            // error_log($e->getMessage()); // utile en debug
            $_SESSION['flash'] = 'Erreur lors du chargement de l’état.';
            $etat = null;
        }

        $this->render('etat/show', [
            'title' => 'Détail de l’état',
            'etat'  => $etat,
            'message' => $_SESSION['flash'] ?? '',
        ]);
        unset($_SESSION['flash']);
}

 public function create(): void
    {
        if (empty($_SESSION['uid'])) $this->redirect('/index.php');

        $this->render('etat/create', [
            'title'   => 'Créer un état',
            'message' => $_SESSION['flash'] ?? '',
            'old'     => $_SESSION['old'] ?? ['libelle' => ''],
            'errors'  => $_SESSION['errors'] ?? [],
        ]);

        unset($_SESSION['flash'], $_SESSION['old'], $_SESSION['errors']);
    }

    public function store(): void
{
    if (empty($_SESSION['uid'])) $this->redirect('/index.php');

    $libelle = trim($_POST['libelle'] ?? '');

    $errors = [];

    if ($libelle === '') {
        $errors['libelle'] = 'Le libellé est obligatoire.';
    } elseif (mb_strlen($libelle) > 100) {
        $errors['libelle'] = 'Le libellé ne doit pas dépasser 100 caractères.';
    }

    if (!empty($errors)) {
        $_SESSION['errors'] = $errors;
        $_SESSION['old']    = ['libelle' => $libelle];
        $_SESSION['flash']  = 'Merci de corriger les erreurs du formulaire.';
        $this->redirect('/index.php/etat/create');
    }

    try {
        $id = \Models\Etat::create($libelle); // maintenant avec ?
        $_SESSION['flash'] = 'État créé avec succès.';
        $this->redirect('/index.php/etat/' . $id);
    } catch (\Throwable $e) {
        $_SESSION['flash'] = 'Impossible de créer l’état.';
        $this->redirect('/index.php/etat/create');
    }
}
   public function update(int $id): void
{
    if (empty($_SESSION['uid'])) {
        $this->redirect('/index.php');
    }

    $etat = Etat::findById($id);

    if (!$etat) {
        $_SESSION['flash'] = 'État introuvable.';
        $this->redirect('/index.php/etat');
    }

    $this->render('etat/update', [
    'title' => 'Modifier un état',
    'etat'  => $etat,
    'old'   => $_SESSION['old'] ?? ['libelle' => $etat['libelle']],
    'errors'=> $_SESSION['errors'] ?? [],
    'message'=> $_SESSION['flash'] ?? '',
]);

    unset($_SESSION['flash'], $_SESSION['old'], $_SESSION['errors']);
}

    
    public function save(int $id): void
    {
        if (empty($_SESSION['uid'])) {
            $this->redirect('/index.php');
        }

        $libelle = trim($_POST['libelle'] ?? '');
        $errors  = [];

        if ($libelle === '') {
            $errors['libelle'] = 'Le libellé est obligatoire.';
        } elseif (mb_strlen($libelle) > 100) {
            $errors['libelle'] = 'Le libellé ne doit pas dépasser 100 caractères.';
        }

        if (!empty($errors)) {
            $_SESSION['errors'] = $errors;
            $_SESSION['old']    = ['libelle' => $libelle];
            $_SESSION['flash']  = 'Merci de corriger les erreurs du formulaire.';
            $this->redirect('./etat/' . $id . '/update');
        }

        try {
            \Models\Etat::update($id, $libelle);

            $_SESSION['flash'] = 'État modifié avec succès.';
            $this->redirect('./etat/' . $id);
        } catch (\Throwable $e) {
            $_SESSION['flash'] = 'Impossible de modifier l’état.';
            $this->redirect('/index.php/etat/' . $id . '/update');
        }
    }
   public function delete(int $id): void
{
    if (empty($_SESSION['uid'])) {
        $this->redirect('/index.php');
    }

    try {
        Etat::delete($id);
        $_SESSION['flash'] = 'État supprimé avec succès.';
    } catch (\Throwable $e) {
        $_SESSION['flash'] = 'Impossible de supprimer l’état.';
    }

    $this->redirect('/index.php/etat');
}

}