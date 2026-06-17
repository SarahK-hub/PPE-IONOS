<?php

namespace Models;

use Config\Database;

/**
 * Modèle Etat
 *
 * Gère toutes les opérations liées à la table "etat".
 */
final class Etat
{
    /**
     * Récupère tous les états de la base de données.
     *
     * @return array Liste des états
     */
    public static function findAll(): array
    {
        // Connexion à la base de données
        $pdo = Database::get();

        // Requête SQL de récupération
        $sql = "SELECT id, libelle
                FROM etat
                ORDER BY id";

        // Exécution de la requête et récupération des résultats
        return $pdo->query($sql)->fetchAll();
    }

    /**
     * Recherche un état par son identifiant.
     *
     * @param int $id Identifiant de l'état
     * @return array|null Retourne l'état ou null s'il n'existe pas
     */
    public static function findById(int $id): ?array
    {
        // Connexion à la base
        $pdo = Database::get();

        // Préparation de la requête sécurisée
        $st = $pdo->prepare(
            'SELECT id, libelle
             FROM etat
             WHERE id = :id'
        );

        // Exécution avec liaison du paramètre
        $st->execute([
            'id' => $id
        ]);

        // Récupération d'une seule ligne
        $row = $st->fetch();

        // Retourne null si aucun résultat
        return $row ?: null;
    }

    /**
     * Crée un nouvel état.
     *
     * @param string $libelle Libellé de l'état
     * @return int Identifiant généré
     */
    public static function create(string $libelle): int
    {
        // Connexion à la base
        $pdo = Database::get();

        // Préparation de la requête d'insertion
        $st = $pdo->prepare(
            "INSERT INTO etat (libelle)
             VALUES (:libelle)"
        );

        // Exécution de l'insertion
        $st->execute([
            'libelle' => $libelle
        ]);

        // Retour de l'identifiant généré automatiquement
        return (int) $pdo->lastInsertId();
    }

    /**
     * Met à jour un état existant.
     *
     * @param int $id Identifiant de l'état
     * @param string $libelle Nouveau libellé
     * @return bool Succès ou échec
     */
    public static function update(int $id, string $libelle): bool
    {
        // Connexion à la base
        $pdo = Database::get();

        // Requête SQL de mise à jour
        $sql = "
            UPDATE etat
            SET libelle = :libelle
            WHERE id = :id
        ";

        // Préparation de la requête
        $stmt = $pdo->prepare($sql);

        // Exécution avec paramètres
        return $stmt->execute([
            'id'      => $id,
            'libelle' => $libelle,
        ]);
    }

    /**
     * Supprime un état.
     *
     * @param int $id Identifiant à supprimer
     * @return bool Succès ou échec
     */
    public static function delete(int $id): bool
    {
        // Connexion à la base
        $pdo = Database::get();

        // Requête SQL de suppression
        $sql = "
            DELETE FROM etat
            WHERE id = :id
        ";

        // Préparation
        $stmt = $pdo->prepare($sql);

        // Exécution
        return $stmt->execute([
            'id' => $id
        ]);
    }
}