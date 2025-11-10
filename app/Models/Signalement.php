<?php

namespace App\Models;

class Signalement extends BaseModel
{
    protected string $table = 'ac_signalements';
    
    /**
     * Récupérer les signalements d'un utilisateur
     */
    public function getByUser(int $userId): array
    {
        $sql = "SELECT s.*, q.nom as quartier_nom, v.nom as ville_nom
                FROM {$this->table} s
                JOIN ac_quartiers q ON s.quartier_id = q.id
                JOIN ac_villes v ON q.ville_id = v.id
                WHERE s.user_id = :user_id
                ORDER BY s.created_at DESC";
        return $this->query($sql, ['user_id' => $userId]);
    }
    
    /**
     * Récupérer tous les signalements avec détails (admin)
     */
    public function getAllWithDetails(): array
    {
        $sql = "SELECT s.*, 
                CONCAT(u.prenom, ' ', u.nom) as user_nom,
                u.email as user_email,
                q.nom as quartier_nom,
                v.nom as ville_nom
                FROM {$this->table} s
                JOIN ac_users u ON s.user_id = u.id
                JOIN ac_quartiers q ON s.quartier_id = q.id
                JOIN ac_villes v ON q.ville_id = v.id
                ORDER BY s.created_at DESC";
        return $this->query($sql);
    }
    
    /**
     * Compter les signalements par statut
     */
    public function countByStatus(string $statut): int
    {
        return $this->count('statut', $statut);
    }
}