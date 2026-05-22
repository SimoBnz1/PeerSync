<?php
declare(strict_types=1);

class User 
{
    private ?int $id;
    private string $name;
    private string $email;
    private string $role;
    private int $points;

    public function __construct(string $name, string $email, string $role, int $points = 0, ?int $id = null) 
    {
        $this->id = $id;
        $this->name = $name;
        $this->email = $email;
        $this->role = $role;
        $this->points = $points;
    }

    public function getId(): ?int { return $this->id; }
    public function getName(): string { return $this->name; }
    public function getEmail(): string { return $this->email; }
    public function getRole(): string { return $this->role; }
    public function getPoints(): int { return $this->points; }
}