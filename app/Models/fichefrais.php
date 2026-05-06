<?php
namespace Models;

use Config\Database;

final class fichefrais
{
    public static function findAll(): array
    {
        $pdo = Database::get();

        $sql = "
        SELECT ff.*, e.libelle AS etat
        FROM fichefrais ff
        JOIN etat e ON ff.idEtat = e.id
        ORDER BY ff.mois DESC
        ";

        return $pdo->query($sql)->fetchAll();
    }

    public static function findById(string $IDvisiteur, string $mois): ?array
    {
        $pdo = Database::get();

        $stmt = $pdo->prepare("
        SELECT ff.*, e.libelle AS etat
        FROM fichefrais ff
        JOIN etat e ON ff.idEtat = e.id
        WHERE ff.IDvisiteur = :IDvisiteur
        AND ff.mois = :mois
        ");

        $stmt->execute([
            'IDvisiteur' => $IDvisiteur,
            'mois' => $mois
        ]);

        return $stmt->fetch() ?: null;
    }

    public static function create(
        int $IDvisiteur,
        string $mois,
        int $nbrJustificatifs,
        float $montantValide,
        int $idEtat,
        int $idLigneFraisHorsForfait
    ): bool
    {
        $pdo = Database::get();

        $stmt = $pdo->prepare("
        INSERT INTO fichefrais
        (IDvisiteur, mois, nbrJustificatifs, montantValide, dateModif, idEtat, idLigneFraisHorsForfait)
        VALUES
        (:IDvisiteur, :mois, :nbrJustificatifs, :montantValide, NOW(), :idEtat, :idLigneFraisHorsForfait)
        ");

        return $stmt->execute([
            'IDvisiteur' => $IDvisiteur,
            'mois' => $mois,
            'nbrJustificatifs' => $nbrJustificatifs,
            'montantValide' => $montantValide,
            'idEtat' => $idEtat,
            'idLigneFraisHorsForfait' => $idLigneFraisHorsForfait
        ]);
    }

    public static function update(
        string $IDvisiteur,
        string $mois,
        int $nbrJustificatifs,
        float $montantValide,
        int $idEtat
    ): bool
    {
        $pdo = Database::get();

        $stmt = $pdo->prepare("
        UPDATE fichefrais
        SET nbrJustificatifs = :nbrJustificatifs,
            montantValide = :montantValide,
            idEtat = :idEtat,
            dateModif = NOW()
        WHERE IDvisiteur = :IDvisiteur
        AND mois = :mois
        ");

        return $stmt->execute([
            'nbrJustificatifs' => $nbrJustificatifs,
            'montantValide' => $montantValide,
            'idEtat' => $idEtat,
            'IDvisiteur' => $IDvisiteur,
            'mois' => $mois
        ]);
    }

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
            'mois' => $mois
        ]);
    }
    public function store(): void
{
    if(empty($_POST['idLigneFraisHorsForfait'])){
        die("Erreur : frais hors forfait obligatoire");
    }

    fichefrais::create(
        (int)$_POST['IDvisiteur'],
        $_POST['mois'],
        (int)$_POST['nbrJustificatifs'],
        (float)$_POST['montantValide'],
        (int)$_POST['idEtat'],
        (int)$_POST['idLigneFraisHorsForfait']
    );
    

   // header('Location: ' . BASE_URL . 'fichefrais'); // ✅ FIX REDIRECT
    //exit;
}
public static function getFraisForfait(string $idVisiteur, string $mois): array
{
    $pdo = Database::get();

    $stmt = $pdo->prepare("
        SELECT f.libelle, l.quantite, f.montant
        FROM lignefraisforfait l
        JOIN fraisforfait f ON l.IDFraisForfait = f.ID
        WHERE l.IDvisiteur = :idVisiteur
        AND l.mois = :mois
    ");

    $stmt->execute([
        'idVisiteur' => $idVisiteur,
        'mois' => $mois
    ]);

    return $stmt->fetchAll();
}

public static function getFraisHorsForfait(string $idVisiteur, string $mois): array
{
    $pdo = Database::get();

    $stmt = $pdo->prepare("
        SELECT l.ID, l.libelle, l.date_frais, l.montant
        FROM lignefraishorforfait l
        JOIN fichefrais f 
            ON f.idLigneFraisHorsForfait = l.ID
        WHERE f.IDvisiteur = :idVisiteur
        AND f.mois = :mois
    ");

    $stmt->execute([
        'idVisiteur' => $idVisiteur,
        'mois' => $mois
    ]);

    return $stmt->fetchAll();
}
}