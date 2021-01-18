<?php


namespace App\Service;


use App\Controller\UserController;
use App\Entity\User;
use App\Repository\UserRepository;
use Ramsey\Uuid\UuidInterface;
use Symfony\Component\HttpFoundation\File\UploadedFile;
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

    public function setProfilePicture(User $user, UploadedFile $file): bool
    {
        $file->move(UserController::USER_RELATIVE . UserController::USER_DIRECTORY . $user->getId(), "/tmp.jpg");

        $image = imagecreatefromjpeg(UserController::USER_RELATIVE . UserController::USER_DIRECTORY . $user->getId() . "/tmp.jpg");
        $width = imagesx($image);
        $height = imagesy($image);
        $size = min($width, $height);

        $cropped = imagecrop($image, [
            "x" => ($width - $size) / 2,
            "y" => ($height - $size) / 2,
            "width" => $size,
            "height" => $size
        ]);

        imagedestroy($image);
        unlink(UserController::USER_RELATIVE . UserController::USER_DIRECTORY . $user->getId() . "/tmp.jpg");

        if ($cropped !== false) {
            $result = imagejpeg($cropped, UserController::USER_RELATIVE . UserController::USER_DIRECTORY . $user->getId() . UserController::PROFILE_PICTURE_FILE);

            imagedestroy($cropped);
            return $result;
        } else {
            return false;
        }
    }
}