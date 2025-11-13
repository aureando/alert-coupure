<?php

namespace App\Models;

class Coupure extends BaseModel
{
    protected string $table = 'ac_coupures';
    
    public function getAllWithDetails(): array
    {
        $sql = "SELECT c.*, 
                GROUP_CONCAT(DISTINCT CONCAT(q.nom, ' (', v.nom, ')') ORDER BY q.nom SEPARATOR ', ') as quartiers_list,
                CONCAT(u.prenom, ' ', u.nom) as admin_nom
                FROM {$this->table} c
                LEFT JOIN ac_coupures_quartiers cq ON c.id = cq.coupure_id
                LEFT JOIN ac_quartiers q ON cq.quartier_id = q.id
                LEFT JOIN ac_villes v ON q.ville_id = v.id
                LEFT JOIN ac_users u ON c.created_by = u.id
                GROUP BY c.id, c.type_service, c.date_debut, c.date_fin, c.motif, c.description, c.statut, c.created_by, c.created_at, c.updated_at, u.prenom, u.nom
                ORDER BY c.date_debut DESC";
        return $this->query($sql);
    }
    
    public function getRecent(int $limit = 50): array
    {
        $sql = "SELECT c.*, 
                q.id as quartier_id,
                q.nom as quartier_nom,
                v.nom as ville_nom,
                v.id as ville_id
                FROM {$this->table} c
                JOIN ac_coupures_quartiers cq ON c.id = cq.coupure_id
                JOIN ac_quartiers q ON cq.quartier_id = q.id
                JOIN ac_villes v ON q.ville_id = v.id
                ORDER BY c.created_at DESC, q.nom ASC
                LIMIT :limit";
        $stmt = $this->db->prepare($sql);
        $stmt->bindValue(':limit', $limit, \PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->fetchAll();
    }
    
    public function search(?int $villeId = null, ?int $quartierId = null, ?string $typeService = null, ?string $searchText = null): array
    {
        $sql = "SELECT c.*, 
                q.id as quartier_id,
                q.nom as quartier_nom,
                v.nom as ville_nom,
                v.id as ville_id
                FROM {$this->table} c
                JOIN ac_coupures_quartiers cq ON c.id = cq.coupure_id
                JOIN ac_quartiers q ON cq.quartier_id = q.id
                JOIN ac_villes v ON q.ville_id = v.id
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
        
        if ($searchText) {
            $sql .= " AND (q.nom LIKE :search OR v.nom LIKE :search OR c.description LIKE :search)";
            $params['search'] = '%' . $searchText . '%';
        }
        
        $sql .= " ORDER BY c.created_at DESC, q.nom ASC LIMIT 100";
        
        return $this->query($sql, $params);
    }
    
    public function getActiveByQuartier(int $quartierId): array
    {
        $sql = "SELECT c.*, 
                q.id as quartier_id,
                q.nom as quartier_nom,
                v.nom as ville_nom
                FROM {$this->table} c
                JOIN ac_coupures_quartiers cq ON c.id = cq.coupure_id
                JOIN ac_quartiers q ON cq.quartier_id = q.id
                JOIN ac_villes v ON q.ville_id = v.id
                WHERE q.id = :quartier_id
                AND c.statut IN ('planifie', 'en_cours')
                AND c.date_fin >= NOW()
                ORDER BY c.date_debut ASC";
        return $this->query($sql, ['quartier_id' => $quartierId]);
    }
    
    public function createWithQuartiers(array $data, array $quartierIds): int|false
    {
        try {
            $this->db->beginTransaction();
            $coupureId = $this->create($data);
            if (!$coupureId) {
                $this->db->rollBack();
                return false;
            }
            $stmt = $this->db->prepare("INSERT INTO ac_coupures_quartiers (coupure_id, quartier_id) VALUES (?, ?)");
            foreach ($quartierIds as $quartierId) {
                $stmt->execute([$coupureId, $quartierId]);
            }
            $this->db->commit();
            return $coupureId;
        } catch (\Exception $e) {
            $this->db->rollBack();
            error_log('Erreur: ' . $e->getMessage());
            return false;
        }
    }
    
    public function getQuartiers(int $coupureId): array
    {
        $sql = "SELECT q.* FROM ac_quartiers q
                JOIN ac_coupures_quartiers cq ON q.id = cq.quartier_id
                WHERE cq.coupure_id = :coupure_id ORDER BY q.nom ASC";
        return $this->query($sql, ['coupure_id' => $coupureId]);
    }
    
    public function updateWithQuartiers(int $id, array $data, array $quartierIds): bool
    {
        try {
            $this->db->beginTransaction();
            if (!$this->update($id, $data)) {
                $this->db->rollBack();
                return false;
            }
            $stmt = $this->db->prepare("DELETE FROM ac_coupures_quartiers WHERE coupure_id = ?");
            $stmt->execute([$id]);
            $stmt = $this->db->prepare("INSERT INTO ac_coupures_quartiers (coupure_id, quartier_id) VALUES (?, ?)");
            foreach ($quartierIds as $quartierId) {
                $stmt->execute([$id, $quartierId]);
            }
            $this->db->commit();
            return true;
        } catch (\Exception $e) {
            $this->db->rollBack();
            return false;
        }
    }
}