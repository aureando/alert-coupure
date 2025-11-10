<?php

namespace App\Models;

class Ville extends BaseModel
{
    protected string $table = 'ac_villes';
    
    /**
     * Récupérer une ville avec le nombre de quartiers
     */
    public function getWithQuartiersCount(): array
    {
        $sql = "SELECT v.*, COUNT(q.id) as nb_quartiers 
                FROM {$this->table} v
                LEFT JOIN ac_quartiers q ON v.id = q.ville_id
                GROUP BY v.id
                ORDER BY v.nom ASC";
        return $this->query($sql);
    }
    
    /**
     * Vérifier si une ville a des quartiers
     */
    public function hasQuartiers(int $villeId): bool
    {
        return $this->count('ville_id', $villeId) > 0;
    }
}