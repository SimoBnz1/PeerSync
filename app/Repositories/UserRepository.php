<?php
declare(strict_types=1);

require_once __DIR__ . '/../Config/Database.php';
require_once __DIR__ . '/../Entities/User.php';

class UserRepository 
{
    private PDO $db;

    public function __construct() 
    {
        $database = new Database();
        $this->db = $database->getConnection();
    }

    public function findByEmail(string $email): ?User 
    {
        $stmt = $this->db->prepare("SELECT * FROM users WHERE email = ?");
        $stmt->execute([$email]);
        $row = $stmt->fetch();

        if (!$row) return null;

        return new User($row->name, $row->email, $row->role, (int)$row->points, (int)$row->id);
    }

    public function findPasswordByEmail(string $email): ?string 
    {
        $stmt = $this->db->prepare("SELECT password FROM users WHERE email = ?");
        $stmt->execute([$email]);
        $row = $stmt->fetch();
        return $row ? $row->password : null;
    }
}