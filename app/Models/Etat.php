<?php 
namespace Models;
use Config\Database;
final class Etat{
// Méthode statique, simple et fiable
    public static function findAll(): array
{
    $pdo = Database::get();
    $sql = "SELECT id, libelle FROM etat ORDER BY id";
     return $pdo->query($sql)->fetchAll();
}
    public static function findById(int $id): ?array
    {
        $pdo = Database::get();
        $st  = $pdo->prepare('SELECT id, libelle FROM etat WHERE id = :id');
        $st->execute(['id' => $id]);
        $row = $st->fetch();
        return $row ?: null;
    }
   //  public static function create(string $libelle): bool
    //{
    //    $pdo = Database::get();
    //   $st = "INSERT INTO etat (libelle) VALUES (:libelle)";
   //    $stmt = $pdo->prepare($st);

  //   return $stmt->execute([
  //     'libelle' => $libelle]);
  //  }
    public static function create(string $libelle): int
{
    $pdo = Database::get();
    $st = $pdo->prepare("INSERT INTO etat (libelle) VALUES (:libelle)");
    $st->execute(['libelle' => $libelle]);

    return (int) $pdo->lastInsertId();
}

    public static function update(int $id, string $libelle): bool
    {
        $pdo = Database::get();

        $sql = "UPDATE etat 
                SET libelle = :libelle 
                WHERE id = :id";

        $stmt = $pdo->prepare($sql);

        return $stmt->execute([
            'id'      => $id,
            'libelle' => $libelle,
        ]);
    }
         public static function delete(int $id): bool
    {
        $pdo = Database::get();

        $sql = "DELETE FROM etat 
                WHERE id = :id";

        $stmt = $pdo->prepare($sql);

        return $stmt->execute([
            'id'      => $id
        ]);
    }
            
    //public static function findById(int $id): ?array


    
}
    

