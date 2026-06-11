<?php
namespace Models;

use Config\Database;
use DateTime;

final class lignefraishorforfait
{
    public static function findAll(): array
    {
        $pdo = Database::get();

        return $pdo->query("
            SELECT ID, libelle,montant
            FROM lignefraishorforfait
        ")->fetchAll();
    }

    public static function findById(int $id): ?array
    {
        $pdo = Database::get();
        $st  = $pdo->prepare('SELECT id, date_frais, libelle , montant  FROM lignefraishorforfait WHERE id = :id');
        $st->execute(['id' => $id]);
        $row = $st->fetch();
        return $row ?: null;
    }
 public static function create( DateTime $date_frais, string $libelle, float $montant): int
{
    $pdo = Database::get();

   $stmt = $pdo->prepare(
    'INSERT INTO lignefraishorforfait (date_frais, libelle, montant)
     VALUES (:date_frais, :libelle, :montant)'
);


    $ok = $stmt->execute([
        'date_frais'    => $date_frais->format('Y-m-d'),
        'libelle' => $libelle,
        'montant' => $montant
    ]);

    if (!$ok) {
        throw new \Exception('Insertion échouée');
    }

    return (int) $pdo->lastInsertId();
}



     public static function update(int $id, DateTime $date_frais, string $libelle, float $montant): bool
{
    $pdo = Database::get();

   $sql = "UPDATE lignefraishorforfait
        SET date_frais = :date_frais,
            libelle = :libelle,
            montant = :montant
        WHERE id = :id";


    $stmt = $pdo->prepare($sql);

    return $stmt->execute([
        'id'      => $id,
        'date_frais' => $date_frais->format('Y-m-d'),
        'libelle' => $libelle,
        'montant' => $montant,
    ]);

}
 public static function delete(int $id): bool
    {
        $pdo = Database::get();

        $sql = "DELETE FROM lignefraishorforfait
                WHERE id = :id";

        $stmt = $pdo->prepare($sql);

        return $stmt->execute([
            'id'      => $id
        ]);
    }
    
    public static function updateMontant(int $id, float $montant): bool
{
    $pdo = Database::get();

    $stmt = $pdo->prepare("
        UPDATE lignefraishorforfait
        SET montant = :montant
        WHERE ID = :id
    ");

    return $stmt->execute([
        'id' => $id,
        'montant' => $montant
    ]);
}
public static function updateLibelleEtMontant(int $id, string $libelle, float $montant): bool
{
    $pdo = Database::get();

    $stmt = $pdo->prepare("
        UPDATE lignefraishorforfait
        SET libelle = :libelle,
            montant = :montant
        WHERE ID = :id
    ");

    return $stmt->execute([
        'id' => $id,
        'libelle' => $libelle,
        'montant' => $montant
    ]);
}
}
