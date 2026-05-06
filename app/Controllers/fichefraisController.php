<?php
namespace Controllers;

use Core\Controller;
use Models\fichefrais;
use Models\Etat;
use Models\visiteur;
use Models\lignefraishorforfait;

final class fichefraisController extends Controller
{
    public function index(): void
    {
        if (empty($_SESSION['uid'])) $this->redirect('/');

        $this->render('fichefrais/index', [
            'title'  => 'Liste des fiches de frais',
            'fiches' => fichefrais::findAll(),
        ]);
    }

    public function mesFiches(): void
    {
        if (empty($_SESSION['uid'])) $this->redirect('/');

        // Affiche les fiches du visiteur connecté
        $this->render('fichefrais/index', [
            'title'  => 'Mes fiches de frais',
            'fiches' => fichefrais::findAll(),
        ]);
    }

    public function show(string $idVisiteur, string $mois): void
    {
        if (empty($_SESSION['uid'])) $this->redirect('/');

        $fiche            = fichefrais::findById($idVisiteur, $mois);
        $fraisForfait     = fichefrais::getFraisForfait($idVisiteur, $mois);
        $fraisHorsForfait = fichefrais::getFraisHorsForfait($idVisiteur, $mois);

        $this->render('fichefrais/show', [
            'title'            => 'Détail de la fiche de frais',
            'fiche'            => $fiche,
            'fraisForfait'     => $fraisForfait,
            'fraisHorsForfait' => $fraisHorsForfait,
        ]);
    }

    public function create(): void
    {
        if (empty($_SESSION['uid'])) $this->redirect('/');

        $this->render('fichefrais/create', [
            'title'    => 'Créer une fiche de frais',
            'etats'    => Etat::findAll(),
            'visiteurs' => visiteur::findAll(),
            'frais'    => lignefraishorforfait::findAll(),
        ]);
    }

    public function store(): void
    {
        if (empty($_SESSION['uid'])) $this->redirect('/');

        fichefrais::create(
            (int)$_POST['IDvisiteur'],
            $_POST['mois'],
            (int)$_POST['nbrJustificatifs'],
            (float)$_POST['montantValide'],
            (int)$_POST['idEtat'],
            (int)$_POST['idLigneFraisHorsForfait']
        );

        $this->redirect('/fichefrais');
    }

    public function update(string $idVisiteur, string $mois): void
    {
        if (empty($_SESSION['uid'])) $this->redirect('/');

        $this->render('fichefrais/update', [
            'title' => 'Modifier la fiche de frais',
            'fiche' => fichefrais::findById($idVisiteur, $mois),
            'etats' => Etat::findAll(),
        ]);
    }

    public function save(string $idVisiteur, string $mois): void
    {
        if (empty($_SESSION['uid'])) $this->redirect('/');

        fichefrais::update(
            $idVisiteur,
            $mois,
            (int)$_POST['nbrJustificatifs'],
            (float)$_POST['montantValide'],
            (int)$_POST['idEtat']
        );

        $this->redirect('/fichefrais');
    }

    public function delete(string $idVisiteur, string $mois): void
    {
        if (empty($_SESSION['uid'])) $this->redirect('/');

        fichefrais::delete($idVisiteur, $mois);
        $this->redirect('/fichefrais');
    }
}
