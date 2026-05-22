<?php
declare(strict_types=1);

require_once __DIR__ . '/../Repositories/UserRepository.php';

class AuthService 
{
    private UserRepository $userRepository;

    public function __construct() 
    {
        $this->userRepository = new UserRepository();
    }

    public function login(string $email, string $password): bool 
    {
        $user = $this->userRepository->findByEmail($email);
        if (!$user) {
            return false;
        }
        

        $hash = $this->userRepository->findPasswordByEmail($email);
        echo"$hash";
        if ($hash && $password) {
            echo"ana hna";
            $_SESSION['user_id'] = $user->getId();
            $_SESSION['user_name'] = $user->getName();
            return true;
        }

        return false;
    }
}