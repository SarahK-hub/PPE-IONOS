<?php
namespace Models;

use Config\Database;

final class User {

    public static function findByUsername(string $u): ?array {
        $st = Database::get()->prepare('
            SELECT id, login, mdp, roles 
            FROM visiteur 
            WHERE login = :l
        ');
        $st->execute([':l'=>$u]);
        $row = $st->fetch(\PDO::FETCH_ASSOC);
        return $row ?: null;
    }
}