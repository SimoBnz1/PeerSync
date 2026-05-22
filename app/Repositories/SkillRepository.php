<?php
declare(strict_types=1);

require_once __DIR__ . '/../Config/Database.php';
require_once __DIR__ . '/../Entities/Skill.php';

class SkillRepository 
{
    private PDO $db;

    public function __construct() 
    {
        $database = new Database();
        $this->db = $database->getConnection();
    }

    public function findAll(): array 
    {
        $stmt = $this->db->query("SELECT * FROM skills ORDER BY name ASC");
        $skills = [];
        while ($row = $stmt->fetch()) {
            $skills[] = new Skill($row->name, (int)$row->id);
        }
        return $skills;
    }
}