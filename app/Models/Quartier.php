<?php

namespace App\Models;

class Quartier extends BaseModel
{
    protected string $table = 'ac_quartiers';
    
    /**
     * Récupérer les quartiers d'une ville
     */
    public function getByVille(int $villeId): array
    {
        return $this->where('ville_id', $villeId, 'nom ASC');
    }
    
    /**
     * Récupérer tous les quartiers avec nom de ville
     */
    public function getAllWithVille(): array
    {
        $sql = "SELECT q.*, v.nom as ville_nom, v.code as ville_code 
                FROM {$this->table} q
                JOIN ac_villes v ON q.ville_id = v.id
                ORDER BY v.nom ASC, q.nom ASC";
        return $this->query($sql);
    }
}