<?php
declare(strict_types=1);

class Skill 
{
    private ?int $id;
    private string $name;

    public function __construct(string $name, ?int $id = null) 
    {
        $this->id = $id;
        $this->name = $name;
    }

    public function getId(): ?int { return $this->id; }
    public function getName(): string { return $this->name; }
}