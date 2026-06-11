<?php
namespace Controllers;

use Core\Controller;
use Models\fraisforfait;

final class fraisforfaitController extends Controller
{
    public function index(): void
    {
        if (empty($_SESSION['uid'])) {
            $this->redirect('/');
        }

        try {
            $fraisforfaits = fraisforfait::findAll(); // appel statique aligné avec le modèle
        } catch (\Throwable $e) {
            // Pour déboguer, active temporairement la ligne suivante :
            // error_log($e->getMessage());
            $_SESSION['flash'] = 'Impossible de charger les frais forfaits.';
            $fraisforfaits = [];
        }

        $this->render('fraisforfait/index', [
            'title'   => 'Liste des frais forfaits',
            'fraisforfaits'   => $fraisforfaits,
            'message' => $_SESSION['flash'] ?? '',
        ]);
        unset($_SESSION['flash']);
    }
    


    public function show($id): void
    {
        if (empty($_SESSION['uid'])) $this->redirect('/');

        $id = (int)$id;

        try {
            $fraisforfait = \Models\fraisforfait::findById($id);
            if (!$fraisforfait) {
                http_response_code(404);
                $_SESSION['flash'] = 'frais forfait introuvable.';
                $this->redirect('/fraisforfait');
            }
        } catch (\Throwable $e) {
            // error_log($e->getMessage()); // utile en debug
            $_SESSION['flash'] = 'Erreur lors du chargement du frais forfait.';
            $fraisforfait = null;
        }

        $this->render('fraisforfait/show', [
            'title' => 'Détail du frais forfait',
            'fraisforfait'  => $fraisforfait,
            'message' => $_SESSION['flash'] ?? '',
        ]);
        unset($_SESSION['flash']);
}
public function create(): void
    {
        if (empty($_SESSION['uid'])) $this->redirect('/');

        $this->render('fraisforfait/create', [
            'title'   => 'Créer un frais forfait',
            'message' => $_SESSION['flash'] ?? '',
            'old' => $_SESSION['old'] ?? [
            'libelle' => '',
            'montant' => ''
],

            'errors'  => $_SESSION['errors'] ?? [],
        ]);

        unset($_SESSION['flash'], $_SESSION['old'], $_SESSION['errors']);
    }

    public function store(): void
{
    if (empty($_SESSION['uid'])) $this->redirect('/');

    $libelle = trim($_POST['libelle'] ?? '');
    $montant = trim($_POST['montant'] ?? '');

    $errors = [];

    if ($libelle === '') {
        $errors['libelle'] = 'Le libellé est obligatoire.';
    } elseif (mb_strlen($libelle) > 100) {
        $errors['libelle'] = 'Le libellé ne doit pas dépasser 100 caractères.';
    }elseif($montant===''){
        $errors['montant'] = 'Le montant est obligatoire.';
    }


    if (!empty($errors)) {
        $_SESSION['errors'] = $errors;
        $_SESSION['old'] = [
        'libelle' => $libelle,
        'montant' => $montant];

        $_SESSION['flash']  = 'Merci de corriger les erreurs du formulaire.';
        $this->redirect('./fraisforfait/create');
    }

    try {
        $id=\Models\fraisforfait::create($libelle, (float)$montant);
        $_SESSION['flash'] = 'frais forfait créé avec succès.';
        $this->redirect('./fraisforfait/'.$id );

    } catch (\Throwable $e) {
        $_SESSION['flash'] = 'Impossible de créer le frais forfait.';
        $this->redirect('./fraisforfait/create');
    }
}
 public function update(int $id): void
{
    if (empty($_SESSION['uid'])) {
        $this->redirect('/');
    }

    $fraisforfait = fraisforfait::findById($id);

    if (!$fraisforfait) {
        $_SESSION['flash'] = 'frais forfait introuvable.';
        $this->redirect('./fraisforfait');
    }

    $this->render('fraisforfait/update', [
    'title' => 'Modifier un frais forfait',
    'fraisforfait'  => $fraisforfait,
    'old' => $_SESSION['old'] ?? [
    'libelle' => $fraisforfait['libelle'],
    'montant' => $fraisforfait['montant'],
],

    'errors'=> $_SESSION['errors'] ?? [],
    'message'=> $_SESSION['flash'] ?? '',
]);

    unset($_SESSION['flash'], $_SESSION['old'], $_SESSION['errors']);
}

    
    public function save(int $id): void
    {
        if (empty($_SESSION['uid'])) {
            $this->redirect('/');
        }

        $libelle = trim($_POST['libelle'] ?? '');
        $montant = trim($_POST['montant'] ?? '');
        $errors  = [];

        if ($libelle === ''|| $montant==='') {
            $errors['libelle'] = 'Le libellé et le montant sont obligatoires.';
        } elseif (mb_strlen($libelle) > 100) {
            $errors['libelle'] = 'Le libellé ne doit pas dépasser 100 caractères.';
        }

        if (!empty($errors)) {
            $_SESSION['errors'] = $errors;
            $_SESSION['old']    = ['libelle' => $libelle];
            $_SESSION['old']    = ['montant' => $montant];
            $_SESSION['flash']  = 'Merci de corriger les erreurs du formulaire.';
            $this->redirect('./fraisforfait/' . $id . '/update');
        }

        try {
            \Models\fraisforfait::update( $id,$libelle,$montant);

            $_SESSION['flash'] = 'frais forfait modifié avec succès.';
            $this->redirect('./fraisforfait/' . $id);
        } catch (\Throwable $e) {
            $_SESSION['flash'] = 'Impossible de modifier le frais forfait.';
            $this->redirect('./fraisforfait/' . $id . '/update');
        }
    }
     public function delete(int $id): void
{
        if (empty($_SESSION['uid'])) {
        $this->redirect('/');
    }

    try {
       fraisforfait::delete($id);
        $_SESSION['flash'] = 'frais forfait supprimé avec succès.';
    } catch (\Throwable $e) {
        $_SESSION['flash'] = 'Impossible de supprimer le frais forfait.';
    }

    $this->redirect('./fraisforfait');
}
 

}