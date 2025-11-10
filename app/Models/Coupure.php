<?php

namespace App\Models;

class Coupure extends BaseModel
{
    protected string $table = 'ac_coupures';
    
    /**
     * Récupérer toutes les coupures avec détails
     */
    public function getAllWithDetails(): array
    {
        $sql = "SELECT c.*, v.nom as ville_nom, v.code as ville_code,
                CONCAT(u.prenom, ' ', u.nom) as admin_nom
                FROM {$this->table} c
                JOIN ac_villes v ON c.ville_id = v.id
                JOIN ac_users u ON c.created_by = u.id
                ORDER BY c.date_debut DESC";
        return $this->query($sql);
    }
    
    /**
     * Récupérer les coupures actives pour un quartier
     */
    public function getActiveByQuartier(int $quartierId): array
    {
        $sql = "SELECT c.*, v.nom as ville_nom 
                FROM {$this->table} c
                JOIN ac_villes v ON c.ville_id = v.id
                JOIN ac_quartiers q ON q.ville_id = v.id
                WHERE q.id = :quartier_id 
                AND c.statut IN ('planifie', 'en_cours')
                AND c.date_fin >= NOW()
                ORDER BY c.date_debut ASC";
        return $this->query($sql, ['quartier_id' => $quartierId]);
    }
    
    /**
     * Récupérer les coupures récentes (page accueil)
     */
    public function getRecent(int $limit = 10): array
    {
        $sql = "SELECT c.*, v.nom as ville_nom 
                FROM {$this->table} c
                JOIN ac_villes v ON c.ville_id = v.id
                ORDER BY c.created_at DESC
                LIMIT :limit";
        $stmt = $this->db->prepare($sql);
        $stmt->bindValue(':limit', $limit, \PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->fetchAll();
    }
}