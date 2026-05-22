<?php
declare(strict_types=1);

require_once __DIR__ . '/../Enums/TicketStatus.php';
require_once __DIR__ . '/../Entities/User.php';
require_once __DIR__ . '/../Entities/Skill.php';
require_once __DIR__ . '/../Entities/HelpRequest.php';
require_once __DIR__ . '/../Repositories/HelpRequestRepository.php';

class HelpRequestService 
{
    private HelpRequestRepository $repository;

    public function __construct() 
    {
        $this->repository = new HelpRequestRepository();
    }

    public function createRequest(string $title, string $description, int $studentId, int $skillId): bool 
    {
        $student = new User('', '', '', 0, $studentId);
        $skill = new Skill('', $skillId);
        
        // 🛠️ الإصلاح هنا: دوزنا TicketStatus::PENDING كبارامتر سادس بوضوح لضمان عدم ذهابه خاوياً
        $request = new HelpRequest($title, $description, $student, $skill, null, TicketStatus::PENDING);
        
        return $this->repository->save($request);
    }

    public function assignTutor(int $requestId, int $tutorId): bool 
    {
        $request = $this->repository->findById($requestId);
        if (!$request) {
            throw new Exception("الطلب غير موجود.");
        }

        $tutor = new User('', '', '', 0, $tutorId);
        $request->assignTo($tutor);
        return $this->repository->update($request);
    }
}