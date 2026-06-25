<?php
// /src/modules/template/template-repository.php
require_once __DIR__ . '/../../core/database.php';

class TemplateRepository {
    private $db;

    public function __construct() {
        $this->db = Database::getConnection();
    }

    public function getAllTaskTemplates() {
        $sql = "SELECT material_category, material_name, title, category, days_offset, sort_order, weight 
                FROM public.task_template 
                ORDER BY material_category ASC, material_name ASC, sort_order ASC";
                
        return $this->db->query($sql)->fetchAll(PDO::FETCH_ASSOC);
    }

    // --- NEW ENUM FETCHING METHODS ---

// --- BULLETPROOF ENUM FETCHING METHODS ---

public function getDistinctMaterialCategories() {
        // Since the ENUM query is likely blocked by permissions, 
        // we flip the logic to prioritize the actual data currently in your table.
        
        $sql = "SELECT DISTINCT material_category 
                FROM public.task_template 
                WHERE material_category IS NOT NULL 
                ORDER BY material_category ASC";
        
        $results = $this->db->query($sql)->fetchAll(PDO::FETCH_COLUMN);
        
        // If the table is empty, return these as reliable fallbacks
        return !empty($results) ? $results : ['general_works', 'structural_works', 'finishing_works'];
    }

    public function getDistinctTaskCategories() {
        $sql = "SELECT DISTINCT category 
                FROM public.task_template 
                WHERE category IS NOT NULL 
                ORDER BY category ASC";
        
        $results = $this->db->query($sql)->fetchAll(PDO::FETCH_COLUMN);
        
        return !empty($results) ? $results : ['preparation', 'installation', 'inspection', 'turnover'];
    }

    // --- SAFE ENUM ALTERATION METHOD ---

    public function addEnumValue($enumType, $newValue) {
        $allowedEnums = ['material_category', 'task_category'];
        if (!in_array($enumType, $allowedEnums)) return;
        
        // 1. Check if the Postgres ENUM type actually exists
        // If it doesn't exist, you are using standard text columns, so we don't need to ALTER anything!
        $typeCheck = $this->db->prepare("SELECT 1 FROM pg_type WHERE typname = ?");
        $typeCheck->execute([$enumType]);
        if (!$typeCheck->fetch()) return; 

        // 2. Check if the value already exists in the enum to prevent fatal errors
        $checkSql = "SELECT 1 FROM pg_enum e JOIN pg_type t ON e.enumtypid = t.oid WHERE t.typname = ? AND e.enumlabel = ?";
        $stmt = $this->db->prepare($checkSql);
        $stmt->execute([$enumType, $newValue]);
        if ($stmt->fetch()) return; 

        // 3. Securely quote the new value and execute ALTER TYPE
        $quotedValue = $this->db->quote($newValue);
        $sql = "ALTER TYPE {$enumType} ADD VALUE {$quotedValue}";
        $this->db->exec($sql);
    }
        
    // --- EXISTING TRANSACTION ---

    public function saveTemplateTransaction($materialCategory, $materialName, $tasks) {
        try {
            $this->db->beginTransaction();
            
            $sql = "INSERT INTO public.task_template 
                    (material_category, material_name, title, category, days_offset, sort_order, weight) 
                    VALUES (:mc, :mn, :title, :cat, :doff, :so, :wt)";
            $stmt = $this->db->prepare($sql);
            
            for ($i = 0; $i < count($tasks['titles']); $i++) {
                $stmt->execute([
                    ':mc'    => $materialCategory,
                    ':mn'    => $materialName,
                    ':title' => $tasks['titles'][$i],
                    ':cat'   => $tasks['categories'][$i],
                    ':doff'  => !empty($tasks['days_offset'][$i]) ? $tasks['days_offset'][$i] : 0,
                    ':so'    => !empty($tasks['sort_orders'][$i]) ? $tasks['sort_orders'][$i] : ($i + 1),
                    ':wt'    => !empty($tasks['weights'][$i]) ? $tasks['weights'][$i] : 0.00
                ]);
            }
            
            $this->db->commit();
            return true;
            
        } catch (Exception $e) {
            $this->db->rollBack();
            throw $e;
        }
    }
}