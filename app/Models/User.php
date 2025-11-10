<?php

namespace App\Models;

class User extends BaseModel
{
    protected string $table = 'ac_users';
    
    /**
     * Trouver un utilisateur par email
     */
    public function findByEmail(string $email): ?object
    {
        return $this->findBy('email', $email);
    }
    
    /**
     * Vérifier le mot de passe
     */
    public function verifyPassword(string $password, string $hash): bool
    {
        return password_verify($password, $hash);
    }
    
    /**
     * Créer un nouvel utilisateur
     */
    public function createUser(array $data): int|false
    {
        $data['password'] = password_hash($data['password'], PASSWORD_DEFAULT);
        $data['created_at'] = date('Y-m-d H:i:s');
        return $this->create($data);
    }
    
    /**
     * Récupérer les utilisateurs avec infos quartier/ville
     */
    public function getAllWithLocation(): array
    {
        $sql = "SELECT u.*, q.nom as quartier_nom, v.nom as ville_nom 
                FROM {$this->table} u
                LEFT JOIN ac_quartiers q ON u.quartier_id = q.id
                LEFT JOIN ac_villes v ON q.ville_id = v.id
                ORDER BY u.created_at DESC";
        return $this->query($sql);
    }
}