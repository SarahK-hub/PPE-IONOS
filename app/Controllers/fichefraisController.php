<?php
namespace Controllers;

use Core\Controller;
use Config\Database;
use Models\fichefrais;
use Models\Etat;
use Models\fraisforfait;
use Models\visiteur;
use Models\lignefraishorforfait;

final class fichefraisController extends Controller
{
    // ─────────────────────────────────────────────
    // INDEX — liste des fiches
    // ─────────────────────────────────────────────
    public function index(): void
    {
        if (empty($_SESSION['user'])) $this->redirect('/index.php');

        $role = $_SESSION['user']['roles'];

        if ($role === 'visiteur') {
            $fiches = fichefrais::findByVisiteur((int)$_SESSION['user']['id']);
            $this->render('fichefrais/index', [
                'title'  => 'Mes fiches de frais',
                'fiches' => $fiches,
                'role'   => 'visiteur',
            ]);
        } else {
            $fiches = fichefrais::findAllAvecVisiteur();
            $this->render('fichefrais/index_comptable', [
                'title'  => 'Suivi des fiches de frais',
                'fiches' => $fiches,
                'role'   => 'comptable',
            ]);
        }
    }

    // ─────────────────────────────────────────────
    // DETAIL JSON — pour la vue comptable (AJAX)
    // ─────────────────────────────────────────────
    public function detailJson(string $idVisiteur, string $mois): void
    {
        if (empty($_SESSION['user'])) {
            http_response_code(403);
            echo json_encode(['error' => 'Non connecté']);
            return;
        }

        if ($_SESSION['user']['roles'] !== 'comptable') {
            http_response_code(403);
            echo json_encode(['error' => 'Accès refusé']);
            return;
        }

        $fiche            = fichefrais::findById($idVisiteur, $mois);
        $fraisForfait     = fichefrais::getFraisForfait($idVisiteur, $mois);
        $fraisHorsForfait = fichefrais::getFraisHorsForfait($idVisiteur, $mois);

        header('Content-Type: application/json; charset=utf-8');
        echo json_encode([
            'fiche'            => $fiche,
            'fraisForfait'     => $fraisForfait,
            'fraisHorsForfait' => $fraisHorsForfait,
        ]);
    }

    // ─────────────────────────────────────────────
    // SHOW — détail d'une fiche (page complète)
    // ─────────────────────────────────────────────
// ─────────────────────────────────────────────
// SHOW
// ─────────────────────────────────────────────
public function show(string $idVisiteur, string $mois): void
{
    if (empty($_SESSION['user'])) {
        $this->redirect('/index.php');
    }

    $role = $_SESSION['user']['roles'];

    if (
        $role === 'visiteur'
        && (int)$idVisiteur !== (int)$_SESSION['user']['id']
    ) {
        $_SESSION['flash'] = 'Accès refusé.';
        $this->redirect('/index.php/fichefrais');
    }

    $fiche = fichefrais::findById($idVisiteur, $mois);

    if (!$fiche) {
        $_SESSION['flash'] = 'Fiche introuvable.';
        $this->redirect('/index.php/fichefrais');
    }

    $fraisForfait = fichefrais::getFraisForfait($idVisiteur, $mois);
    $fraisHorsForfait = fichefrais::getFraisHorsForfait($idVisiteur, $mois);

    $estCloturee = false;

    if (
        isset($fiche['etat'])
        && strtoupper((string)$fiche['etat']) === 'CL'
    ) {
        $estCloturee = true;
    }

    if (
        isset($fiche['idEtat'])
        && (int)$fiche['idEtat'] === 2
    ) {
        $estCloturee = true;
    }

    $this->render('fichefrais/show', [
        'title'            => 'Détail de la fiche de frais',
        'fiche'            => $fiche,
        'fraisForfait'     => $fraisForfait,
        'fraisHorsForfait' => $fraisHorsForfait,
        'role'             => $role,
        'estCloturee'      => $estCloturee,
    ]);
}

    // ─────────────────────────────────────────────
// CREATE
// ─────────────────────────────────────────────
public function create(): void
{
    if (empty($_SESSION['user'])) {
        $this->redirect('/index.php');
    }

    if ($_SESSION['user']['roles'] !== 'visiteur') {
        $this->redirect('/index.php/fichefrais');
    }

    $this->render('fichefrais/create', [
        'title' => 'Créer une fiche de frais',

        // plus besoin des états
        'visiteurs' => visiteur::findAll(),
        'frais' => lignefraishorforfait::findAll(),
        'fraisforfait' => fraisforfait::findAll(),
    ]);
}

// ─────────────────────────────────────────────
// STORE
// ─────────────────────────────────────────────
public function store(): void
{
    if (empty($_SESSION['user'])) {
        $this->redirect('/index.php');
    }

    if ($_SESSION['user']['roles'] !== 'visiteur') {
        $this->redirect('/index.php/fichefrais');
    }

    $idVisiteur = (int) $_SESSION['user']['id'];
    $mois = trim($_POST['mois'] ?? '');

    // Vérification du mois
    if ($mois === '') {

        $_SESSION['flash'] = 'Le mois est obligatoire.';
        $this->redirect('/index.php/fichefrais/create');
    }

    // Vérification doublon
    if (fichefrais::existe($idVisiteur, $mois)) {

        $_SESSION['flash'] =
            'Une fiche existe déjà pour ce mois.';

        $this->redirect('/index.php/fichefrais/create');
    }

    // Etat "Créé"
    $idEtat = 7;
    $idLigneHF = (int)($_POST['idLigneFraisHorsForfait'] ?? 0);
    $idForfait = (int)($_POST['idfraisforfait'] ?? 0);
    $quantite  = (int)($_POST['quantite'] ?? 0);

    $fraisHF = lignefraishorforfait::findById($idLigneHF);
    $fraisFF = fraisforfait::findById($idForfait);

    $montantHF = $fraisHF['montant'] ?? 0;
    $montantFF = $fraisFF['montant'] ?? 0;

    $montantValide = $montantHF + ($quantite * $montantFF);

    $okFiche = fichefrais::create(
    $idVisiteur,
    $mois,
    (int)($_POST['nbrJustificatifs'] ?? 0),
    $montantValide,
    $idEtat,
    (int)($_POST['idLigneFraisHorsForfait'] ?? 0)
);

$okForfait = fichefrais::createfraisforfait(
    $idVisiteur,
    $mois,
    (int)($_POST['quantite'] ?? 0),
    (int)($_POST['idfraisforfait'] ?? 0)
);

if ($okFiche && $okForfait) {

    $_SESSION['flash'] =
        'La fiche de frais a été créée avec succès.';

} else {

    $_SESSION['flash'] =
        'Erreur lors de la création de la fiche.';
}

$this->redirect('/index.php/fichefrais');
}

    // ─────────────────────────────────────────────
    // UPDATE / SAVE — modification (bloquée si clôturée)
    // ─────────────────────────────────────────────
   // ─────────────────────────────────────────────
// UPDATE
// ─────────────────────────────────────────────
public function update(string $idVisiteur, string $mois): void
{
    if (empty($_SESSION['user'])) {
        $this->redirect('/index.php');
    }

    $role = $_SESSION['user']['roles'];

    if (
        $role === 'visiteur'
        && (int)$idVisiteur !== (int)$_SESSION['user']['id']
    ) {
        $this->redirect('/index.php/fichefrais');
    }

    $fiche = fichefrais::findById($idVisiteur, $mois);

    if (!$fiche) {
        $_SESSION['flash'] = 'Fiche introuvable.';
        $this->redirect('/index.php/fichefrais');
    }

    $estCloturee = false;

    if (
        isset($fiche['etat'])
        && strtoupper((string)$fiche['etat']) === 'CL'
    ) {
        $estCloturee = true;
    }

    if (
        isset($fiche['idEtat'])
        && (int)$fiche['idEtat'] === 2
    ) {
        $estCloturee = true;
    }

    if ($estCloturee) {
        $_SESSION['flash'] = 'Cette fiche est clôturée.';
        $this->redirect('/index.php/fichefrais/' . $idVisiteur . '/' . $mois);
    }

    $this->render('fichefrais/update', [
        'title' => 'Modifier la fiche',
        'fiche' => $fiche,
        'etats' => Etat::findAll(),
        'role'  => $role,
    ]);
}

// ─────────────────────────────────────────────
// SAVE
// ─────────────────────────────────────────────
public function save(string $idVisiteur, string $mois): void
{
    if (empty($_SESSION['user'])) {
        $this->redirect('/index.php');
    }

    $fiche = fichefrais::findById($idVisiteur, $mois);

    if (!$fiche) {
        $this->redirect('/index.php/fichefrais');
    }

    $role = $_SESSION['user']['roles'];

    // =========================
    // 👤 VISITEUR
    // =========================
    if ($role === 'visiteur') {

    // Le visiteur ne peut modifier que les fiches à l'état "Créé"
    if ((int)$fiche['idEtat'] !== 7) {

        $_SESSION['flash'] =
            "Cette fiche n'est plus modifiable.";

        $this->redirect("/index.php/fichefrais/$idVisiteur/$mois");
        return;
    }

    $libelle = trim($_POST['libelle'] ?? '');
    $montant = (float)($_POST['montant'] ?? 0);

    if ($libelle === '') {

        $_SESSION['flash'] =
            "Libellé obligatoire.";

        $this->redirect("/index.php/fichefrais/$idVisiteur/$mois/update");
        return;
    }

    lignefraishorforfait::updateLibelleEtMontant(
        $fiche['idLigneFraisHorsForfait'],
        $libelle,
        $montant
    );

    $_SESSION['flash'] =
        "Fiche mise à jour.";

    $this->redirect("/index.php/fichefrais/$idVisiteur/$mois");
    return;
}

    // =========================
    // 👨‍💼 COMPTABLE
    // =========================
    if ($role === 'comptable') {

        $idEtat = (int)($_POST['idEtat'] ?? 0);

        fichefrais::updateEtatOnly($idVisiteur, $mois, $idEtat);

        $_SESSION['flash'] = "État mis à jour.";
        $this->redirect("/index.php/fichefrais/$idVisiteur/$mois");
        return;
    }

    $this->redirect('/index.php/fichefrais');
}
  // ─────────────────────────────────────────────
// DELETE
// ─────────────────────────────────────────────
public function delete(string $idVisiteur, string $mois): void
{
    if (empty($_SESSION['user'])) {
        $this->redirect('/index.php');
    }

    $fiche = fichefrais::findById($idVisiteur, $mois);

    if (!$fiche) {
        $this->redirect('/index.php/fichefrais');
    }

    $estCloturee = false;

    if (
        isset($fiche['etat'])
        && strtoupper((string)$fiche['etat']) === 'CL'
    ) {
        $estCloturee = true;
    }

    if (
        isset($fiche['idEtat'])
        && (int)$fiche['idEtat'] === 2
    ) {
        $estCloturee = true;
    }

    if ($estCloturee) {
        $_SESSION['flash'] = 'Cette fiche est clôturée.';
        $this->redirect('/index.php/fichefrais/' . $idVisiteur . '/' . $mois);
    }

    fichefrais::delete($idVisiteur, $mois);

    $_SESSION['flash'] = 'Fiche supprimée.';

    $this->redirect('/index.php/fichefrais');
}

  public function updateFraisHorsForfait(
    string $idVisiteur,
    string $mois,
    int $id
): void
{
    if (empty($_SESSION['user'])) {
        $this->redirect('/index.php');
    }

    $fiche = fichefrais::findById($idVisiteur, $mois);

    if (!$fiche) {
        $this->redirect('/index.php/fichefrais');
    }

    $role = $_SESSION['user']['roles'];

    $idEtatFiche = (int)$fiche['idEtat'];

    // 2 = clôturée
    // 4 = remboursée

    if (in_array($idEtatFiche, [2, 4])) {

        $_SESSION['flash'] =
            "Cette fiche est verrouillée.";

        $this->redirect("/index.php/fichefrais/$idVisiteur/$mois");
    }

    // ==========================
    // VISITEUR
    // ==========================

    if ($role === 'visiteur') {

        if ((int)$idVisiteur !== (int)$_SESSION['user']['id']) {

            $_SESSION['flash'] = "Accès refusé.";

            $this->redirect('/index.php/fichefrais');
        }

        // uniquement état Créé
        if ($idEtatFiche !== 7) {

            $_SESSION['flash'] =
                "Modification impossible.";

            $this->redirect("/index.php/fichefrais/$idVisiteur/$mois");
        }

        $montant = (float)($_POST['montant'] ?? 0);

        $pdo = Database::get();

        $stmt = $pdo->prepare("
            UPDATE lignefraishorforfait
            SET montant = :montant
            WHERE ID = :id
        ");

        $stmt->execute([
            'montant' => $montant,
            'id'      => $id
        ]);

        $_SESSION['flash'] =
            "Montant modifié.";

        $this->redirect("/index.php/fichefrais/$idVisiteur/$mois");
    }

    // ==========================
    // COMPTABLE
    // ==========================

    if ($role === 'comptable') {

        $montant = (float)($_POST['montant'] ?? 0);
        $idEtat  = (int)($_POST['idEtat'] ?? 0);

        $pdo = Database::get();

        $stmt = $pdo->prepare("
            UPDATE lignefraishorforfait
            SET montant = :montant
            WHERE ID = :id
        ");

        $stmt->execute([
            'montant' => $montant,
            'id'      => $id
        ]);

        fichefrais::updateEtatOnly(
            $idVisiteur,
            $mois,
            $idEtat
        );

        $_SESSION['flash'] =
            "Frais hors forfait mis à jour.";

        $this->redirect("/index.php/fichefrais/$idVisiteur/$mois");
    }

    $this->redirect('/index.php/fichefrais');
}
}
