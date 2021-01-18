<?php


namespace App\Service;


use App\Entity\User;
use App\Repository\UserRepository;
use Ramsey\Uuid\UuidInterface;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;
use Symfony\Component\Security\Core\Security;

class UserService
{

    private $security;
    private $userRepository;
    private $passwordEncoder;

    public function __construct(
        Security $security,
        UserRepository $userRepository,
        UserPasswordEncoderInterface $passwordEncoder
    )
    {
        $this->security = $security;
        $this->userRepository = $userRepository;
        $this->passwordEncoder = $passwordEncoder;
    }

    public function getLoggedInUser(): ?User
    {
        $user = $this->security->getUser();
        if (!$user) {
            return null;
        }

        return $this->getUserByEmail($user->getUsername());
    }

    public function getUsers(): array
    {
        return $this->userRepository->findAll();
    }

    public function getUserByEmail($email): ?User
    {
        return $this->userRepository->findOneByEmail($email);
    }

    public function delete($user)
    {
        $this->userRepository->delete($user);
    }

    public function get(UUIDInterface $userId)
    {
        return $this->userRepository->findOneById($userId);
    }

    public function add(User $user)
    {
        $user->setCreated();
        $user->setCreator($this->getLoggedInUser());
        $user->setPassword($this->passwordEncoder->encodePassword($user, $user->getNewPassword()));

        $this->userRepository->save($user);
    }

    public function update(User $user)
    {
        if ($user->getNewPassword() != null && $user->getNewPassword() != "") {
            $user->setPassword($this->passwordEncoder->encodePassword($user, $user->getNewPassword()));
        }

        $this->userRepository->update($user);
    }
}