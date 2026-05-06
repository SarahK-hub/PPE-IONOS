<?php 
namespace Models;
use Config\Database;

final class visiteur {

    public static function findAll(): array
    {
        $pdo = Database::get();
        $st  = $pdo->query('SELECT ID, NOM, PRENOM,ADRESSE,VILLE,CP,DATE_EMBAUCHE,roles  FROM visiteur');
        return $st->fetchAll();
    }

    public static function findById(int $id): ?array
    {
        $pdo = Database::get();
        $st  = $pdo->prepare('SELECT ID, NOM, PRENOM,ADRESSE,VILLE,CP,DATE_EMBAUCHE,roles FROM visiteur WHERE id = :id');
        $st->execute(['id' => $id]);
        $row = $st->fetch();
        return $row ?: null;
    }

 public static function findBySearch(string $search): array
{
    $pdo = Database::get();

    // Nettoyage de la recherche
    $search = trim(preg_replace('/\s+/', ' ', $search));
    $searchLike = '%' . $search . '%';

    // Requête SQL avec insensibilité à la casse et aux accents
    $sql = "
        SELECT ID, NOM, PRENOM, ADRESSE, VILLE, CP, DATE_EMBAUCHE
        FROM visiteur
        WHERE NOM LIKE :s1 COLLATE utf8mb4_general_ci
           OR PRENOM LIKE :s2 COLLATE utf8mb4_general_ci
    ";

    $st = $pdo->prepare($sql);
    $st->bindValue(':s1', $searchLike, \PDO::PARAM_STR);
    $st->bindValue(':s2', $searchLike, \PDO::PARAM_STR);
    $st->execute();

    return $st->fetchAll(\PDO::FETCH_ASSOC);
}


public static function create( string $nom, string $prenom,string $adresse, string $ville, string $CP,string $date_embauche,string $login,string $mdp , string $roles
): bool
{
    $pdo = Database::get();

    $st = "
        INSERT INTO visiteur (nom,prenom,adresse,ville, CP, date_embauche,login,mdp, roles) 
        VALUES (:nom,:prenom,:adresse,:ville,:CP,:date_embauche,:login,:mdp,:roles)
    ";

    $stmt = $pdo->prepare($st);

    return $stmt->execute([
        'nom'            => $nom,
        'prenom'         => $prenom,
        'adresse'        => $adresse,
        'ville'          => $ville,
        'CP'             => $CP,
        'date_embauche'  => $date_embauche,
        'login'          => $login,
        'mdp'            => $mdp,
        'roles'           => $roles

    ]);
}
public static function update(
    int $id,
    string $nom,
    string $prenom,
    string $adresse,
    string $ville,
    string $CP,
    string $date_embauche,
    string $login,
    string $roles
): bool {
    $pdo = Database::get();

    $sql = "
        UPDATE visiteur
        SET nom = :nom,
            prenom = :prenom,
            adresse = :adresse,
            ville = :ville,
            CP = :CP,
            date_embauche = :date_embauche,
            login = :login,
            roles = :roles
        WHERE id = :id
    ";

    $stmt = $pdo->prepare($sql);

    return $stmt->execute([
        'id'            => $id,
        'nom'           => $nom,
        'prenom'        => $prenom,
        'adresse'       => $adresse,
        'ville'         => $ville,
        'CP'            => $CP,
        'date_embauche' => $date_embauche,
        'login'         => $login,
        'roles'          => $roles
    ]);
}

public static function delete(int $id): bool
{
    $pdo = Database::get();

    $stmt = $pdo->prepare("DELETE FROM visiteur WHERE id = :id");
    return $stmt->execute(['id' => $id]);
}


}



    
