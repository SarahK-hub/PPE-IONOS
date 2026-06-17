<?php

namespace Controllers;

use Core\Controller;
use Models\Etat;

/**
 * Contrôleur chargé de gérer les opérations CRUD
 * (Créer, Lire, Modifier, Supprimer) sur les états.
 */
final class EtatController extends Controller
{
    /**
     * Affiche la liste complète des états.
     */
    public function index(): void
    {
        // Vérifie que l'utilisateur est connecté
        if (empty($_SESSION['user'])) {
            $this->redirect('/index.php');
        }

        try {
            // Récupération de tous les états depuis la base de données
            $etats = Etat::findAll();
        } catch (\Throwable $e) {

            // En cas d'erreur SQL ou PHP
            // error_log($e->getMessage());

            $_SESSION['flash'] = 'Impossible de charger les états.';
            $etats = [];
        }

        // Affichage de la vue avec les données récupérées
        $this->render('etat/index', [
            'title'   => 'Liste des États',
            'etats'   => $etats,
            'message' => $_SESSION['flash'] ?? '',
        ]);

        // Nettoyage du message flash après affichage
        unset($_SESSION['flash']);
    }

    /**
     * Affiche le détail d'un état.
     *
     * @param mixed $id Identifiant de l'état
     */
    public function show($id): void
    {
        // Vérification de la connexion utilisateur
        if (empty($_SESSION['user'])) {
            $this->redirect('/index.php');
        }

        // Sécurisation de l'identifiant
        $id = (int) $id;

        try {
            // Recherche de l'état correspondant à l'id
            $etat = Etat::findById($id);

            // Si aucun état trouvé
            if (!$etat) {
                http_response_code(404);

                $_SESSION['flash'] = 'État introuvable.';
                $this->redirect('/index.php/etat');
            }

        } catch (\Throwable $e) {

            // Gestion des erreurs inattendues
            // error_log($e->getMessage());

            $_SESSION['flash'] = 'Erreur lors du chargement de l’état.';
            $etat = null;
        }

        // Affichage de la page détail
        $this->render('etat/show', [
            'title'   => 'Détail de l’état',
            'etat'    => $etat,
            'message' => $_SESSION['flash'] ?? '',
        ]);

        unset($_SESSION['flash']);
    }

    /**
     * Affiche le formulaire de création.
     */
    public function create(): void
    {
        // Vérifie que l'utilisateur est connecté
        if (empty($_SESSION['user'])) {
            $this->redirect('/index.php');
        }

        // Affiche le formulaire avec les anciennes valeurs
        // en cas d'erreur de validation
        $this->render('etat/create', [
            'title'   => 'Créer un état',
            'message' => $_SESSION['flash'] ?? '',
            'old'     => $_SESSION['old'] ?? ['libelle' => ''],
            'errors'  => $_SESSION['errors'] ?? [],
        ]);

        // Nettoyage des variables temporaires
        unset(
            $_SESSION['flash'],
            $_SESSION['old'],
            $_SESSION['errors']
        );
    }

    /**
     * Traite l'enregistrement d'un nouvel état.
     */
    public function store(): void
    {
        // Vérification de la connexion
        if (empty($_SESSION['user'])) {
            $this->redirect('/index.php');
        }

        // Récupération et nettoyage du champ saisi
        $libelle = trim($_POST['libelle'] ?? '');

        $errors = [];

        // Validation : champ obligatoire
        if ($libelle === '') {
            $errors['libelle'] = 'Le libellé est obligatoire.';
        }
        // Validation : longueur maximale
        elseif (mb_strlen($libelle) > 100) {
            $errors['libelle'] =
                'Le libellé ne doit pas dépasser 100 caractères.';
        }

        // Si des erreurs existent
        if (!empty($errors)) {

            // Sauvegarde des erreurs et des anciennes valeurs
            $_SESSION['errors'] = $errors;
            $_SESSION['old'] = ['libelle' => $libelle];

            $_SESSION['flash'] =
                'Merci de corriger les erreurs du formulaire.';

            $this->redirect('/index.php/etat/create');
        }

        try {
            // Insertion en base de données
            $id = Etat::create($libelle);

            $_SESSION['flash'] =
                'État créé avec succès.';

            // Redirection vers la fiche créée
            $this->redirect('/index.php/etat/' . $id);

        } catch (\Throwable $e) {

            $_SESSION['flash'] =
                'Impossible de créer l’état.';

            $this->redirect('/index.php/etat/create');
        }
    }

    /**
     * Affiche le formulaire de modification.
     *
     * @param int $id Identifiant de l'état
     */
    public function update(int $id): void
    {
        // Contrôle d'accès
        if (empty($_SESSION['user'])) {
            $this->redirect('/index.php');
        }

        // Recherche de l'état à modifier
        $etat = Etat::findById($id);

        if (!$etat) {
            $_SESSION['flash'] = 'État introuvable.';
            $this->redirect('/index.php/etat');
        }

        // Affichage du formulaire prérempli
        $this->render('etat/update', [
            'title'   => 'Modifier un état',
            'etat'    => $etat,
            'old'     => $_SESSION['old']
                ?? ['libelle' => $etat['libelle']],
            'errors'  => $_SESSION['errors'] ?? [],
            'message' => $_SESSION['flash'] ?? '',
        ]);

        unset(
            $_SESSION['flash'],
            $_SESSION['old'],
            $_SESSION['errors']
        );
    }

    /**
     * Enregistre les modifications d'un état.
     *
     * @param int $id Identifiant de l'état
     */
    public function save(int $id): void
    {
        // Vérification utilisateur connecté
        if (empty($_SESSION['user'])) {
            $this->redirect('/index.php');
        }

        // Nettoyage des données reçues
        $libelle = trim($_POST['libelle'] ?? '');

        $errors = [];

        // Validation du champ
        if ($libelle === '') {
            $errors['libelle'] = 'Le libellé est obligatoire.';
        } elseif (mb_strlen($libelle) > 100) {
            $errors['libelle'] =
                'Le libellé ne doit pas dépasser 100 caractères.';
        }

        // Retour au formulaire si erreur
        if (!empty($errors)) {

            $_SESSION['errors'] = $errors;
            $_SESSION['old'] = ['libelle' => $libelle];

            $_SESSION['flash'] =
                'Merci de corriger les erreurs du formulaire.';

            $this->redirect('/index.php/etat/' . $id . '/update');
        }

        try {
            // Mise à jour de l'état
            Etat::update($id, $libelle);

            $_SESSION['flash'] =
                'État modifié avec succès.';

            $this->redirect('/index.php/etat/' . $id);

        } catch (\Throwable $e) {

            $_SESSION['flash'] =
                'Impossible de modifier l’état.';

            $this->redirect('/index.php/etat/' . $id . '/update');
        }
    }

    /**
     * Supprime un état.
     *
     * @param int $id Identifiant à supprimer
     */
    public function delete(int $id): void
    {
        // Vérifie que l'utilisateur est connecté
        if (empty($_SESSION['user'])) {
            $this->redirect('/index.php');
        }

        try {
            // Suppression en base de données
            Etat::delete($id);

            $_SESSION['flash'] =
                'État supprimé avec succès.';

        } catch (\Throwable $e) {

            $_SESSION['flash'] =
                'Impossible de supprimer l’état.';
        }

        // Retour vers la liste
        $this->redirect('/index.php/etat');
    }
}