<?php
declare(strict_types=1);

require_once __DIR__ . '/../Enums/TicketStatus.php';
require_once __DIR__ . '/User.php';
require_once __DIR__ . '/Skill.php';

class HelpRequest 
{
    private ?int $id;
    private string $title;
    private string $description;
    private User $student;
    private Skill $skill;
    private ?User $tutor;
    private string $status;

    // 🛠️ تأكيد الـ default value هنا في البارامتر
    public function __construct(string $title, string $description, User $student, Skill $skill, ?User $tutor = null, string $status = 'pending', ?int $id = null) 
    {
        $this->id = $id;
        $this->title = $title;
        $this->description = $description;
        $this->student = $student;
        $this->skill = $skill;
        $this->tutor = $tutor;
        
        // 🛠️ حماية إضافية: إلى دوز لينا شي حد string خاوي، تانعطيوها 'pending' فوراً
        $this->status = ($status !== '') ? $status : 'pending';
    }

    public function assignTo(User $tutor): void 
    {
        if ($this->student->getId() === $tutor->getId()) {
            throw new Exception("ما يمكنش تقدم المساعدة لراسك يا بطل!");
        }
        $this->tutor = $tutor;
        $this->status = 'assigned';
    }

    public function resolve(): void 
    {
        $this->status = 'resolved';
    }

    public function getId(): ?int { return $this->id; }
    public function getTitle(): string { return $this->title; }
    public function getDescription(): string { return $this->description; }
    public function getStudent(): User { return $this->student; }
    public function getSkill(): Skill { return $this->skill; }
    public function getTutor(): ?User { return $this->tutor; }
    
    // 🛠️ تأكيد رجوع الـ status
    public function getStatus(): string 
    { 
        return ($this->status !== '') ? $this->status : 'pending'; 
    }
}