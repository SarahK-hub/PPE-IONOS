<?php
namespace Models;

use Config\Database;

final class fichefrais
{
    // ─────────────────────────────────────────────
    // Toutes les fiches (comptable) avec nom du visiteur + totaux calculés
    // ─────────────────────────────────────────────
    public static function findAllAvecVisiteur(): array
    {
        $pdo = Database::get();

        $sql = "
        SELECT
            ff.IDvisiteur,
            v.NOM,
            v.PRENOM,
            ff.mois,
            ff.nbrJustificatifs,
            ff.montantValide,
            ff.dateModif,
            ff.idEtat,
            e.libelle AS etat,
            -- Total frais forfait (somme quantite * montant pour ce visiteur/mois)
            COALESCE((
                SELECT SUM(l.quantite * f.montant)
                FROM lignefraisforfait l
                JOIN fraisforfait f ON l.IDfraisforfait = f.ID
                WHERE l.IDvisiteur = ff.IDvisiteur AND l.mois = ff.mois
            ), 0) AS totalForfait,
            -- Total frais hors forfait (via la FK idLigneFraisHorsForfait)
            COALESCE((
                SELECT lhf.montant
                FROM lignefraishorforfait lhf
                WHERE lhf.ID = ff.idLigneFraisHorsForfait
            ), 0) AS totalHorsForfait
        FROM fichefrais ff
        JOIN visiteur v ON ff.IDvisiteur = v.ID
        JOIN etat e ON ff.idEtat = e.ID
        ORDER BY ff.mois DESC, v.NOM ASC
        ";

        return $pdo->query($sql)->fetchAll();
    }

    // ─────────────────────────────────────────────
    // Fiches d'un visiteur précis (vue visiteur)
    // ─────────────────────────────────────────────
    public static function findByVisiteur(int $idVisiteur): array
    {
        $pdo = Database::get();

        $sql = "
        SELECT ff.*, e.libelle AS etat
        FROM fichefrais ff
        JOIN etat e ON ff.idEtat = e.ID
        WHERE ff.IDvisiteur = :idVisiteur
        ORDER BY ff.mois DESC
        ";

        $stmt = $pdo->prepare($sql);
        $stmt->execute(['idVisiteur' => $idVisiteur]);
        return $stmt->fetchAll();
    }

    // ─────────────────────────────────────────────
    // Détail d'une fiche (visiteur + nom)
    // ─────────────────────────────────────────────
    public static function findById(string $IDvisiteur, string $mois): ?array
    {
        $pdo = Database::get();

        $stmt = $pdo->prepare("
        SELECT ff.*, e.libelle AS etat, v.NOM, v.PRENOM
        FROM fichefrais ff
        JOIN etat e ON ff.idEtat = e.ID
        JOIN visiteur v ON ff.IDvisiteur = v.ID
        WHERE ff.IDvisiteur = :IDvisiteur
        AND ff.mois = :mois
        ");

        $stmt->execute([
            'IDvisiteur' => $IDvisiteur,
            'mois'       => $mois,
        ]);

        return $stmt->fetch() ?: null;
    }

    // ─────────────────────────────────────────────
    // Frais forfait d'une fiche
    // ─────────────────────────────────────────────
    public static function getFraisForfait(string $idVisiteur, string $mois): array
    {
        $pdo = Database::get();

        $stmt = $pdo->prepare("
            SELECT f.libelle, l.quantite, f.montant,
                   (l.quantite * f.montant) AS total_ligne
            FROM lignefraisforfait l
            JOIN fraisforfait f ON l.IDfraisforfait = f.ID
            WHERE l.IDvisiteur = :idVisiteur
            AND l.mois = :mois
        ");

        $stmt->execute([
            'idVisiteur' => $idVisiteur,
            'mois'       => $mois,
        ]);

        return $stmt->fetchAll();
    }

    // ─────────────────────────────────────────────
    // Frais hors forfait d'une fiche (via FK fichefrais.idLigneFraisHorsForfait)
    // ─────────────────────────────────────────────
    public static function getFraisHorsForfait(string $idVisiteur, string $mois): array
    {
        $pdo = Database::get();

        $stmt = $pdo->prepare("
            SELECT
                lhf.ID,
                lhf.libelle,
                lhf.date_frais,
                lhf.montant,

                ff.nbrJustificatifs,
                ff.montantValide,
                ff.dateModif,

                ff.idEtat,

                e.libelle AS etat

            FROM lignefraishorforfait lhf

            JOIN fichefrais ff
                ON ff.idLigneFraisHorsForfait = lhf.ID

            JOIN etat e
                ON ff.idEtat = e.ID

            WHERE ff.IDvisiteur = :idVisiteur
            AND ff.mois = :mois
        ");

        $stmt->execute([
            'idVisiteur' => $idVisiteur,
            'mois'       => $mois,
        ]);

        return $stmt->fetchAll();
    }

    // ─────────────────────────────────────────────
    // Création
    // ─────────────────────────────────────────────
    public static function create(
        int $IDvisiteur,
        string $mois,
        int $nbrJustificatifs,
        float $montantValide,
        int $idEtat,
        int $idLigneFraisHorsForfait
    ): bool {
        $pdo = Database::get();

        $stmt = $pdo->prepare("
        INSERT INTO fichefrais
        (IDvisiteur, mois, nbrJustificatifs, montantValide, dateModif, idEtat, idLigneFraisHorsForfait)
        VALUES
        (:IDvisiteur, :mois, :nbrJustificatifs, :montantValide, NOW(), :idEtat, :idLigneFraisHorsForfait)
        ");

        return $stmt->execute([
            'IDvisiteur'             => $IDvisiteur,
            'mois'                   => $mois,
            'nbrJustificatifs'       => $nbrJustificatifs,
            'montantValide'          => $montantValide,
            'idEtat'                 => $idEtat,
            'idLigneFraisHorsForfait' => $idLigneFraisHorsForfait,
        ]);
    }
    public static function createfraisforfait(
        int $IDvisiteur,
        string $mois,
        int $quantite,
        int $idfraisforfait
    ): bool {
        $pdo = Database::get();

        $stmt = $pdo->prepare("
        INSERT INTO lignefraisforfait
        (IDvisiteur, mois,  quantite,  IDfraisforfait)
        VALUES
        (:IDvisiteur, :mois, :quantite, :IDfraisforfait)
        ");

        return $stmt->execute([
            'IDvisiteur'             => $IDvisiteur,
            'mois'                   => $mois,
            'quantite'               => $quantite,
            'IDfraisforfait'         => $idfraisforfait
        ]);
    }

    // ─────────────────────────────────────────────
    // Mise à jour
    // ─────────────────────────────────────────────
    public static function updateEtatOnly(string $idVisiteur, string $mois, int $idEtat): bool
{
    $pdo = Database::get();

    $stmt = $pdo->prepare("
        UPDATE fichefrais
        SET idEtat = :idEtat,
            dateModif = NOW()
        WHERE IDvisiteur = :IDvisiteur
        AND mois = :mois
    ");

    return $stmt->execute([
        'idEtat' => $idEtat,
        'IDvisiteur' => $idVisiteur,
        'mois' => $mois
    ]);
}

    // ─────────────────────────────────────────────
    // Suppression
    // ─────────────────────────────────────────────
    public static function delete(string $IDvisiteur, string $mois): bool
    {
        $pdo = Database::get();

        $stmt = $pdo->prepare("
        DELETE FROM fichefrais
        WHERE IDvisiteur = :IDvisiteur
        AND mois = :mois
        ");

        return $stmt->execute([
            'IDvisiteur' => $IDvisiteur,
            'mois'       => $mois,
        ]);
    }

public static function isCloturee(string $idVisiteur, string $mois): bool
{
    $fiche = self::findById($idVisiteur, $mois);

    if (!$fiche) {
        return false;
    }

    return in_array((int)$fiche['idEtat'], [2, 3, 4]);
}
public static function updateEtatEtDateModif(
    string $idVisiteur,
    string $mois,
    int $idEtat
): bool {

    $pdo = Database::get();

    $stmt = $pdo->prepare("
        UPDATE fichefrais
        SET
            idEtat = :idEtat,
            dateModif = NOW()
        WHERE IDvisiteur = :idVisiteur
        AND mois = :mois
    ");

    return $stmt->execute([
        'idEtat'     => $idEtat,
        'idVisiteur' => $idVisiteur,
        'mois'       => $mois
    ]);
}
public static function modifierHorsForfait(
    int $idLigne,
    float $nouveauMontant
): bool {

    $pdo = Database::get();

    $pdo->beginTransaction();

    try {

        $stmt = $pdo->prepare("
            UPDATE lignefraishorforfait
            SET montant = :montant
            WHERE ID = :id
        ");

        $stmt->execute([
            'montant' => $nouveauMontant,
            'id'      => $idLigne
        ]);

        $stmt = $pdo->prepare("
            UPDATE fichefrais
            SET dateModif = NOW(),
                idEtat = 2
            WHERE idLigneFraisHorsForfait = :id
        ");

        $stmt->execute([
            'id' => $idLigne
        ]);

        $pdo->commit();

        return true;

    } catch (\Throwable $e) {

        $pdo->rollBack();

        return false;
    }
}
public static function existe(int $idVisiteur, string $mois): bool
{
    $pdo = Database::get();

    $stmt = $pdo->prepare("
        SELECT COUNT(*)
        FROM fichefrais
        WHERE IDvisiteur = :idVisiteur
        AND mois = :mois
    ");

    $stmt->execute([
        'idVisiteur' => $idVisiteur,
        'mois' => $mois
    ]);

    return (int)$stmt->fetchColumn() > 0;
}
}
