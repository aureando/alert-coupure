<?php

namespace App\Models;

use App\Config\Database;
use PDO;

/**
 * Classe BaseModel
 * Tous les modèles héritent de cette classe
 * Fournit les opérations CRUD de base
 */
class BaseModel
{
    /**
     * Instance PDO
     * @var PDO
     */
    protected PDO $db;
    
    /**
     * Nom de la table
     * @var string
     */
    protected string $table;
    
    /**
     * Clé primaire
     * @var string
     */
    protected string $primaryKey = 'id';
    
    /**
     * Constructeur
     */
    public function __construct()
    {
        $this->db = Database::getInstance();
    }
    
    /**
     * Récupérer tous les enregistrements
     * 
     * @param string $orderBy
     * @return array
     */
    public function all(string $orderBy = 'id DESC'): array
    {
        $sql = "SELECT * FROM {$this->table} ORDER BY {$orderBy}";
        $stmt = $this->db->query($sql);
        return $stmt->fetchAll();
    }
    
    /**
     * Trouver un enregistrement par ID
     * 
     * @param int $id
     * @return object|null
     */
    public function find(int $id): ?object
    {
        $sql = "SELECT * FROM {$this->table} WHERE {$this->primaryKey} = :id LIMIT 1";
        $stmt = $this->db->prepare($sql);
        $stmt->execute(['id' => $id]);
        
        $result = $stmt->fetch();
        return $result ?: null;
    }
    
    /**
     * Trouver avec condition
     * 
     * @param string $column
     * @param mixed $value
     * @return object|null
     */
    public function findBy(string $column, $value): ?object
    {
        $sql = "SELECT * FROM {$this->table} WHERE {$column} = :value LIMIT 1";
        $stmt = $this->db->prepare($sql);
        $stmt->execute(['value' => $value]);
        
        $result = $stmt->fetch();
        return $result ?: null;
    }
    
    /**
     * Récupérer plusieurs enregistrements avec condition
     * 
     * @param string $column
     * @param mixed $value
     * @param string $orderBy
     * @return array
     */
    public function where(string $column, $value, string $orderBy = 'id DESC'): array
    {
        $sql = "SELECT * FROM {$this->table} WHERE {$column} = :value ORDER BY {$orderBy}";
        $stmt = $this->db->prepare($sql);
        $stmt->execute(['value' => $value]);
        return $stmt->fetchAll();
    }
    
    /**
     * Créer un nouvel enregistrement
     * 
     * @param array $data
     * @return int|false ID inséré ou false
     */
    public function create(array $data)
    {
        $columns = implode(', ', array_keys($data));
        $placeholders = ':' . implode(', :', array_keys($data));
        
        $sql = "INSERT INTO {$this->table} ({$columns}) VALUES ({$placeholders})";
        $stmt = $this->db->prepare($sql);
        
        if ($stmt->execute($data)) {
            return (int) $this->db->lastInsertId();
        }
        
        return false;
    }
    
    /**
     * Mettre à jour un enregistrement
     * 
     * @param int $id
     * @param array $data
     * @return bool
     */
    public function update(int $id, array $data): bool
    {
        $setParts = [];
        foreach (array_keys($data) as $key) {
            $setParts[] = "{$key} = :{$key}";
        }
        $setClause = implode(', ', $setParts);
        
        $sql = "UPDATE {$this->table} SET {$setClause} WHERE {$this->primaryKey} = :id";
        $stmt = $this->db->prepare($sql);
        
        $data['id'] = $id;
        return $stmt->execute($data);
    }
    
    /**
     * Supprimer un enregistrement
     * 
     * @param int $id
     * @return bool
     */
    public function delete(int $id): bool
    {
        $sql = "DELETE FROM {$this->table} WHERE {$this->primaryKey} = :id";
        $stmt = $this->db->prepare($sql);
        return $stmt->execute(['id' => $id]);
    }
    
    /**
     * Compter les enregistrements
     * 
     * @param string|null $column
     * @param mixed $value
     * @return int
     */
    public function count(?string $column = null, $value = null): int
    {
        if ($column && $value !== null) {
            $sql = "SELECT COUNT(*) FROM {$this->table} WHERE {$column} = :value";
            $stmt = $this->db->prepare($sql);
            $stmt->execute(['value' => $value]);
        } else {
            $sql = "SELECT COUNT(*) FROM {$this->table}";
            $stmt = $this->db->query($sql);
        }
        
        return (int) $stmt->fetchColumn();
    }
    
    /**
     * Exécuter une requête SQL personnalisée
     * 
     * @param string $sql
     * @param array $params
     * @return array
     */
    protected function query(string $sql, array $params = []): array
    {
        $stmt = $this->db->prepare($sql);
        $stmt->execute($params);
        return $stmt->fetchAll();
    }
}