<?php
declare(strict_types=1);

require_once __DIR__ . '/../Enums/TicketStatus.php';
require_once __DIR__ . '/../Services/HelpRequestService.php';
require_once __DIR__ . '/../Repositories/HelpRequestRepository.php';
require_once __DIR__ . '/../Repositories/SkillRepository.php';

class HelpRequestController 
{
    private HelpRequestService $service;
    private HelpRequestRepository $repository;
    private SkillRepository $skillRepository;

    public function __construct() 
    {
        $this->service = new HelpRequestService();
        $this->repository = new HelpRequestRepository();
        $this->skillRepository = new SkillRepository();
    }

    public function index(): array
{
    $userId = $_SESSION['user_id'];
    $requestRepository = new HelpRequestRepository();
    $skillRepository = new SkillRepository();

    // 1. الطلبات ديالي أنا
    $myRequests = $requestRepository->findByStudentId($userId);

    // 2. طلبات الآخرين اللي باقين معلقين (En attente)
    $pendingOthers = $requestRepository->findPendingOthers($userId);

    // 3. 🌟 الجديد: الطلبات اللي أنا وافقت نعاون فيها وباقي ما تسداتش
    // تأكد أن هاد الميثود موجودة عندك في الـ Repository، ولا غانوريك كيفاش تزيدها
    $myInterventions = $requestRepository->findAssignedToHelper($userId);

    $skills = $skillRepository->findAll();

    return [
        'myRequests' => $myRequests,
        'pendingOthers' => $pendingOthers,
        'myInterventions' => $myInterventions, // صيفطناها للـ View
        'skills' => $skills
    ];
}

    public function handleCreate(): void 
    {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $title = trim($_POST['title'] ?? '');
            $description = trim($_POST['description'] ?? '');
            $skillId = (int)($_POST['skill_id'] ?? 0);
            $studentId = (int)$_SESSION['user_id'];

            if ($title !== '' && $description !== '' && $skillId > 0) {
                $this->service->createRequest($title, $description, $studentId, $skillId);
                header('Location: index.php');
                exit();
            }
        }
    }

    public function handleAssign(): void 
    {
        $requestId = (int)($_GET['id'] ?? 0);
        $tutorId = (int)$_SESSION['user_id'];

        if ($requestId > 0) {
            try {
                $this->service->assignTutor($requestId, $tutorId);
                header('Location: index.php?success=1');
                exit();
            } catch (Exception $e) {
                header('Location: index.php?error=' . urlencode($e->getMessage()));
                exit();
            }
        }
    }
}