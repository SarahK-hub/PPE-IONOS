<?php
namespace Controllers;

use Core\Controller;
use DateTime;
use Models\lignefraishorforfait;

final class frais_hors_forfaitController extends Controller
{
    public function index(): void
    {
        if (empty($_SESSION['user'])) {
            $this->redirect('/');
        }

        try {
            $frais_hors_forfaits = lignefraishorforfait::findAll(); // appel statique aligné avec le modèle
        } catch (\Throwable $e) {
            // Pour déboguer, active temporairement la ligne suivante :
            // error_log($e->getMessage());
            $_SESSION['flash'] = 'Impossible de charger les frais hors forfaits.';
            $frais_hors_forfaits = [];
        }

        $this->render('frais_hors_forfait/index', [
            'title'   => 'Liste des frais hors forfaits',
            'frais_hors_forfaits'   => $frais_hors_forfaits,
            'message' => $_SESSION['flash'] ?? '',
        ]);
        unset($_SESSION['flash']);
    }


    public function show($id): void
    {
        if (empty($_SESSION['user'])) $this->redirect('/');

        $id = (int)$id;

        try {
            $frais_hors_forfait = \Models\lignefraishorforfait::findById($id);
            if (!$frais_hors_forfait) {
                http_response_code(404);
                $_SESSION['flash'] = 'frais hors forfait introuvable.';
                $this->redirect('/frais_hors_forfait');
            }
        } catch (\Throwable $e) {
            // error_log($e->getMessage()); // utile en debug
            $_SESSION['flash'] = 'Erreur lors du chargement du frais hors forfait.';
            $frais_hors_forfait = null;
        }

        $this->render('frais_hors_forfait/show', [
            'title' => 'Détail du frais hors forfait',
            'frais_hors_forfait'  => $frais_hors_forfait,
            'message' => $_SESSION['flash'] ?? '',
        ]);
        unset($_SESSION['flash']);
}
public function create(): void
    {
        if (empty($_SESSION['user'])) $this->redirect('/');

        $this->render('frais_hors_forfait/create', [
            'title'   => 'Créer un frais hors forfait',
            'message' => $_SESSION['flash'] ?? '',
            'old' => $_SESSION['old'] ?? [
            'date_frais' => '',
            'libelle' => '',
            'montant' => ''
],

            'errors'  => $_SESSION['errors'] ?? [],
        ]);

        unset($_SESSION['flash'], $_SESSION['old'], $_SESSION['errors']);
    }

    public function store(): void
{
    if (empty($_SESSION['user'])) $this->redirect('/');

    $date_frais    = trim($_POST['date_frais'] ?? '');
    $libelle = trim($_POST['libelle'] ?? '');
    $montant = trim($_POST['montant'] ?? '');

    $errors = [];

    if ($date_frais === '')     $errors['date_frais'] = 'La date est obligatoire.';
    if ($libelle === '')  $errors['libelle'] = 'Le libellé est obligatoire.';
    if ($montant === '')  $errors['montant'] = 'Le montant est obligatoire.';

    if (!empty($errors)) {
        $_SESSION['errors'] = $errors;
        $_SESSION['old'] = compact('date_frais', 'libelle', 'montant');
        $this->redirect('./frais_hors_forfait/create');
    }

    try {
        $dateObj = new DateTime($date_frais);
        $id = lignefraishorforfait::create($dateObj, $libelle, (float)$montant);

        $_SESSION['flash'] = 'Frais hors forfait créé avec succès.';
        $this->redirect('./frais_hors_forfait/' . $id);
    } catch (\Throwable $e) {
        $_SESSION['flash'] = 'Impossible de créer le frais hors forfait.';
        $this->redirect('./frais_hors_forfait/create');
    }

}

 public function update(int $id): void
{
    if (empty($_SESSION['user'])) {
        $this->redirect('/');
    }

    $frais_hors_forfait = lignefraishorforfait::findById($id);

    if (!$frais_hors_forfait) {
        $_SESSION['flash'] = 'frais hors forfait introuvable.';
        $this->redirect('./frais_hors_forfait');
    }

    $this->render('frais_hors_forfait/update', [
    'title' => 'Modifier un frais hors forfait',
    'frais_hors_forfait'  => $frais_hors_forfait,
    'old' => $_SESSION['old'] ?? [
     'date_frais' => $frais_hors_forfait['date_frais'],   
    'libelle' => $frais_hors_forfait['libelle'],
    'montant' => $frais_hors_forfait['montant'],
],

    'errors'=> $_SESSION['errors'] ?? [],
    'message'=> $_SESSION['flash'] ?? '',
]);

    unset($_SESSION['flash'], $_SESSION['old'], $_SESSION['errors']);
}

    
    public function save(int $id): void
    {
        if (empty($_SESSION['user'])) {
            $this->redirect('/');
        }
        $date_frais = trim($_POST['date_frais'] ?? '');
        $libelle = trim($_POST['libelle'] ?? '');
        $montant = trim($_POST['montant'] ?? '');
        $errors  = [];

        if ($libelle === ''|| $montant==='') {
            $errors['libelle'] = 'la date,le libellé et le montant sont obligatoires.';
        } elseif (mb_strlen($libelle) > 100) {
            $errors['libelle'] = 'Le libellé ne doit pas dépasser 100 caractères.';
        }

        if (!empty($errors)) {
            $_SESSION['errors'] = $errors;
            $_SESSION['old']    = ['date_frais' => $date_frais];
            $_SESSION['old']    = ['libelle' => $libelle];
            $_SESSION['old']    = ['montant' => $montant];
            $_SESSION['flash']  = 'Merci de corriger les erreurs du formulaire.';
            $this->redirect('./frais_hors_forfait/' . $id . '/update');
        }


           try {
            $dateObj = new DateTime($date_frais);
            lignefraishorforfait::update($id, $dateObj, $libelle, (float)$montant);

            $_SESSION['flash'] = 'frais hors forfait modifié avec succès.';
            $this->redirect('./frais_hors_forfait/' . $id);

            } catch (\Throwable $e) {
            $_SESSION['flash'] = 'Impossible de modifier le frais hors forfait.';
            $this->redirect('./frais_hors_forfait/' . $id . '/update');
}

    }
     public function delete(int $id): void
{
        if (empty($_SESSION['user'])) {
        $this->redirect('/');
    }

    try {
       lignefraishorforfait::delete($id);
        $_SESSION['flash'] = 'frais hors forfait supprimé avec succès.';
    } catch (\Throwable $e) {
        $_SESSION['flash'] = 'Impossible de supprimer le frais hors forfait.';
    }

    $this->redirect('./frais_hors_forfait');
}
 
public function updateMontant(int $id): void
{
    if (empty($_SESSION['user'])) {
        $this->redirect('/');
    }

    $montant = trim($_POST['montant'] ?? '');

    if ($montant === '' || !is_numeric($montant)) {
        $_SESSION['flash'] = "Montant invalide.";
        $this->redirect($_SERVER['HTTP_REFERER']);
    }

    try {
        \Models\lignefraishorforfait::updateMontant($id, (float)$montant);

        $_SESSION['flash'] = "Montant modifié avec succès.";

    } catch (\Throwable $e) {
        $_SESSION['flash'] = "Erreur modification.";
    }

    // retour page fiche
    $this->redirect($_SERVER['HTTP_REFERER']);
}
}