<?php


namespace App\Service;


use App\Entity\User;
use App\Repository\UserRepository;
use Symfony\Component\Security\Core\Security;

class UserService
{

    private $security;
    private $userRepository;

    public function __construct(Security $security, UserRepository $userRepository)
    {
        $this->security = $security;
        $this->userRepository = $userRepository;
    }

    public function getLoggedInUser(): ?User
    {
        $user = $this->security->getUser();
        if (!$user) {
            return null;
        }

        return $this->getUserByName($user->getUsername());
    }

    public function getUsers(): array
    {
        return $this->userRepository->findAll();
    }

    public function getUserByName($username): ?User
    {
        return $this->userRepository->findOneByName($username);
    }
}