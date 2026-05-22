<?php
declare(strict_types=1);

require_once __DIR__ . '/../Config/Database.php';
require_once __DIR__ . '/../Enums/TicketStatus.php';
require_once __DIR__ . '/../Entities/User.php';
require_once __DIR__ . '/../Entities/Skill.php';
require_once __DIR__ . '/../Entities/HelpRequest.php';

class HelpRequestRepository 
{
    private PDO $db;

    public function __construct() 
    {
        $database = new Database();
        $this->db = $database->getConnection();
    }

    public function save(HelpRequest $request): bool 
{
    $stmt = $this->db->prepare("INSERT INTO help_requests (title, description, student_id, skill_id, status) VALUES (?, ?, ?, ?, ?)");
    return $stmt->execute([
        $request->getTitle(),
        $request->getDescription(),
        $request->getStudent()->getId(),
        $request->getSkill()->getId(),
        $request->getStatus() 
    ]);
}
public function findAssignedToHelper(int $helperId): array
{
    $query = "SELECT hr.*, 
                     student.name as student_name, student.email as student_email,
                     skill.name as skill_name
              FROM help_requests hr
              JOIN users student ON hr.student_id = student.id
              JOIN skills skill ON hr.skill_id = skill.id
              WHERE hr.tutor_id = :helper_id 
                AND hr.status = 'assigned'
              ORDER BY hr.id DESC";

    
        // $this->db هو كائن الـ PDO اللي عندك في الـ Repository
        $stmt = $this->db->prepare($query);
        $stmt->execute(['helper_id' => $helperId]);
        
        $interventions = [];
        
        while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
            // 1. كاريي كائن الطالب اللي دار الطلب
            $student = new User(
                $row['student_name'], 
                $row['student_email'], 
                '', // الباسورد ما محتاجينوش هنا
                0, 
                (int)$row['student_id']
            );
            
            // 2. كاريي كائن التقنية
            $skill = new Skill(
                $row['skill_name'], 
                (int)$row['skill_id']
            );
            
            // 3. كاريي كائن طلب المساعدة المكتمل
            $request = new HelpRequest(
                $row['title'],
                $row['description'],
                $student,
                $skill,
                null, // الـ Tutor هو أنت، ما محتاجينش نـكارييوه وسط راسو
                $row['status'],
                (int)$row['id']
            );
            
            $interventions[] = $request;
        }
        
        return $interventions;
  
}

    public function findById(int $id): ?HelpRequest 
    {
        $stmt = $this->db->prepare("SELECT * FROM help_requests WHERE id = ?");
        $stmt->execute([$id]);
        $row = $stmt->fetch();

        if (!$row) return null;

        $student = new User('', '', '', 0, (int)$row->student_id);
        $skill = new Skill('', (int)$row->skill_id);
        $tutor = $row->tutor_id ? new User('', '', '', 0, (int)$row->tutor_id) : null;

        return new HelpRequest($row->title, $row->description, $student, $skill, $tutor, $row->status, (int)$row->id);
    }

    // 🆕 دالة جديدة: جلب طلبات المساعدة الخاصة بالمستخدم الحالي فقط
    public function findByStudentId(int $studentId): array 
    {
        $sql = "SELECT hr.*, s.name as skill_name 
                FROM help_requests hr
                JOIN skills s ON hr.skill_id = s.id
                WHERE hr.student_id = ? ORDER BY hr.id DESC";
                
        $stmt = $this->db->prepare($sql);
        $stmt->execute([$studentId]);
        
        $requests = [];
        while ($row = $stmt->fetch()) {
            $student = new User('', '', '', 0, $studentId);
            $skill = new Skill($row->skill_name, (int)$row->skill_id);
            
            $requests[] = new HelpRequest($row->title, $row->description, $student, $skill, null, $row->status, (int)$row->id);
        }
        return $requests;
    }

    // 🔄 تعديل: جلب طلبات الانتظار الخاصة بالآخرين فقط (استثناء المستخدم الحالي)
    public function findPendingOthers(int $currentUserId): array 
    {
        $sql = "SELECT hr.*, u.name as student_name, s.name as skill_name 
                FROM help_requests hr
                JOIN users u ON hr.student_id = u.id
                JOIN skills s ON hr.skill_id = s.id
                WHERE hr.status = ? AND hr.student_id != ? ORDER BY hr.id DESC";
                
        $stmt = $this->db->prepare($sql);
        $stmt->execute([TicketStatus::PENDING, $currentUserId]);
        
        $requests = [];
        while ($row = $stmt->fetch()) {
            $student = new User($row->student_name, '', '', 0, (int)$row->student_id);
            $skill = new Skill($row->skill_name, (int)$row->skill_id);
            
            $requests[] = new HelpRequest($row->title, $row->description, $student, $skill, null, $row->status, (int)$row->id);
        }
        return $requests;
    }

    public function update(HelpRequest $request): bool 
    {
        $stmt = $this->db->prepare("UPDATE help_requests SET tutor_id = ?, status = ? WHERE id = ?");
        return $stmt->execute([
            $request->getTutor() ? $request->getTutor()->getId() : null,
            $request->getStatus(),
            $request->getId()
        ]);
    }
}