<?php 
namespace Models;
use Config\Database;
final class fraisforfait{
// Méthode statique, simple et fiable
    public static function findAll(): array
    {
        $pdo = Database::get();
        $st  = $pdo->query('SELECT id, libelle,montant FROM fraisforfait ');
        return $st->fetchAll(); // FETCH_ASSOC déjà par défaut via Database
    }
    public static function findById(int $id): ?array
    {
        $pdo = Database::get();
        $st  = $pdo->prepare('SELECT id, libelle , montant FROM fraisforfait WHERE id = :id');
        $st->execute(['id' => $id]);
        $row = $st->fetch();
        return $row ?: null;
    }
    public static function create(string $libelle, float $montant): bool
    {
        $pdo = Database::get();
        $st  = $pdo->prepare(
            'INSERT INTO fraisforfait (libelle, montant) VALUES (:libelle, :montant)'
        );

        return $st->execute([
            'libelle' => $libelle,
            'montant' => $montant
        ]);
    }
     public static function update(int $id, string $libelle, float $montant): bool
{
    $pdo = Database::get();

    $sql = "UPDATE fraisforfait
            SET libelle = :libelle,
                montant = :montant
            WHERE id = :id";

    $stmt = $pdo->prepare($sql);

    return $stmt->execute([
        'id'      => $id,
        'libelle' => $libelle,
        'montant' => $montant,
    ]);

}
 public static function delete(int $id): bool
    {
        $pdo = Database::get();

        $sql = "DELETE FROM fraisforfait
                WHERE id = :id";

        $stmt = $pdo->prepare($sql);

        return $stmt->execute([
            'id'      => $id
        ]);
    }
    
}