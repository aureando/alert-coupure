<?php

namespace App\Models;

class Coupure extends BaseModel
{
    protected string $table = 'ac_coupures';
    
    /**
     * Récupérer toutes les coupures avec détails (quartiers)
     */
    public function getAllWithDetails(): array
    {
        $sql = "SELECT c.*, 
                GROUP_CONCAT(DISTINCT q.nom ORDER BY q.nom SEPARATOR ', ') as quartiers_noms,
                GROUP_CONCAT(DISTINCT v.nom ORDER BY v.nom SEPARATOR ', ') as villes_noms,
                CONCAT(u.prenom, ' ', u.nom) as admin_nom
                FROM {$this->table} c
                LEFT JOIN ac_coupures_quartiers cq ON c.id = cq.coupure_id
                LEFT JOIN ac_quartiers q ON cq.quartier_id = q.id
                LEFT JOIN ac_villes v ON q.ville_id = v.id
                JOIN ac_users u ON c.created_by = u.id
                GROUP BY c.id
                ORDER BY c.date_debut DESC";
        return $this->query($sql);
    }
    
    /**
     * Récupérer les coupures actives pour un quartier
     */
    public function getActiveByQuartier(int $quartierId): array
    {
        $sql = "SELECT c.*, 
                GROUP_CONCAT(DISTINCT q2.nom ORDER BY q2.nom SEPARATOR ', ') as quartiers_noms,
                v.nom as ville_nom
                FROM {$this->table} c
                JOIN ac_coupures_quartiers cq ON c.id = cq.coupure_id
                JOIN ac_quartiers q ON cq.quartier_id = q.id
                JOIN ac_villes v ON q.ville_id = v.id
                LEFT JOIN ac_coupures_quartiers cq2 ON c.id = cq2.coupure_id
                LEFT JOIN ac_quartiers q2 ON cq2.quartier_id = q2.id
                WHERE cq.quartier_id = :quartier_id
                AND c.statut IN ('planifie', 'en_cours')
                AND c.date_fin >= NOW()
                GROUP BY c.id
                ORDER BY c.date_debut ASC";
        return $this->query($sql, ['quartier_id' => $quartierId]);
    }
    
    /**
     * Récupérer les coupures récentes (page accueil)
     */
    public function getRecent(int $limit = 10): array
    {
        $sql = "SELECT c.*, 
                GROUP_CONCAT(DISTINCT q.nom ORDER BY q.nom SEPARATOR ', ') as quartiers_noms,
                GROUP_CONCAT(DISTINCT v.nom ORDER BY v.nom SEPARATOR ', ') as villes_noms
                FROM {$this->table} c
                LEFT JOIN ac_coupures_quartiers cq ON c.id = cq.coupure_id
                LEFT JOIN ac_quartiers q ON cq.quartier_id = q.id
                LEFT JOIN ac_villes v ON q.ville_id = v.id
                GROUP BY c.id
                ORDER BY c.created_at DESC
                LIMIT :limit";
        $stmt = $this->db->prepare($sql);
        $stmt->bindValue(':limit', $limit, \PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->fetchAll();
    }
    
    /**
     * Rechercher des coupures avec filtres
     */
    public function search(?int $villeId = null, ?int $quartierId = null, ?string $typeService = null): array
    {
        $sql = "SELECT c.*, 
                GROUP_CONCAT(DISTINCT q2.nom ORDER BY q2.nom SEPARATOR ', ') as quartiers_noms,
                GROUP_CONCAT(DISTINCT v2.nom ORDER BY v2.nom SEPARATOR ', ') as villes_noms
                FROM {$this->table} c
                LEFT JOIN ac_coupures_quartiers cq ON c.id = cq.coupure_id
                LEFT JOIN ac_quartiers q ON cq.quartier_id = q.id
                LEFT JOIN ac_villes v ON q.ville_id = v.id
                LEFT JOIN ac_coupures_quartiers cq2 ON c.id = cq2.coupure_id
                LEFT JOIN ac_quartiers q2 ON cq2.quartier_id = q2.id
                LEFT JOIN ac_villes v2 ON q2.ville_id = v2.id
                WHERE 1=1";
        
        $params = [];
        
        if ($villeId) {
            $sql .= " AND v.id = :ville_id";
            $params['ville_id'] = $villeId;
        }
        
        if ($quartierId) {
            $sql .= " AND q.id = :quartier_id";
            $params['quartier_id'] = $quartierId;
        }
        
        if ($typeService) {
            $sql .= " AND c.type_service = :type_service";
            $params['type_service'] = $typeService;
        }
        
        $sql .= " GROUP BY c.id ORDER BY c.created_at DESC LIMIT 50";
        
        return $this->query($sql, $params);
    }
    
    /**
     * Créer une coupure avec quartiers
     */
    public function createWithQuartiers(array $data, array $quartierIds): int|false
    {
        try {
            $this->db->beginTransaction();
            
            // Créer la coupure
            $coupureId = $this->create($data);
            
            if (!$coupureId) {
                $this->db->rollBack();
                return false;
            }
            
            // Lier les quartiers
            $stmt = $this->db->prepare("INSERT INTO ac_coupures_quartiers (coupure_id, quartier_id) VALUES (?, ?)");
            foreach ($quartierIds as $quartierId) {
                $stmt->execute([$coupureId, $quartierId]);
            }
            
            $this->db->commit();
            return $coupureId;
            
        } catch (\Exception $e) {
            $this->db->rollBack();
            error_log('Erreur createWithQuartiers: ' . $e->getMessage());
            return false;
        }
    }
    
    /**
     * Récupérer les quartiers d'une coupure
     */
    public function getQuartiers(int $coupureId): array
    {
        $sql = "SELECT q.* 
                FROM ac_quartiers q
                JOIN ac_coupures_quartiers cq ON q.id = cq.quartier_id
                WHERE cq.coupure_id = :coupure_id
                ORDER BY q.nom ASC";
        return $this->query($sql, ['coupure_id' => $coupureId]);
    }
    
    /**
     * Mettre à jour une coupure avec quartiers
     */
    public function updateWithQuartiers(int $id, array $data, array $quartierIds): bool
    {
        try {
            $this->db->beginTransaction();
            
            // Mettre à jour la coupure
            if (!$this->update($id, $data)) {
                $this->db->rollBack();
                return false;
            }
            
            // Supprimer les anciennes liaisons
            $stmt = $this->db->prepare("DELETE FROM ac_coupures_quartiers WHERE coupure_id = ?");
            $stmt->execute([$id]);
            
            // Créer les nouvelles liaisons
            $stmt = $this->db->prepare("INSERT INTO ac_coupures_quartiers (coupure_id, quartier_id) VALUES (?, ?)");
            foreach ($quartierIds as $quartierId) {
                $stmt->execute([$id, $quartierId]);
            }
            
            $this->db->commit();
            return true;
            
        } catch (\Exception $e) {
            $this->db->rollBack();
            error_log('Erreur updateWithQuartiers: ' . $e->getMessage());
            return false;
        }
    }
}