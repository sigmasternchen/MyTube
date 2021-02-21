<?php


namespace App\Service;


use App\Entity\Set;
use App\Entity\User;
use App\Repository\SetRepository;
use Ramsey\Uuid\UuidInterface;

class SetService
{

    private $userService;

    private $setRepository;

    public function __construct(
        UserService $userService,
        SetRepository $setRepository
    )
    {
        $this->userService = $userService;
        $this->setRepository = $setRepository;
    }

    public function getAll(User $user): array
    {
        return $this->setRepository->findByCreator($user);
    }

    public function add(Set $set)
    {
        $set->setCreated();
        $set->setCreator($this->userService->getLoggedInUser());
        $set->clearVideos();

        $this->setRepository->save($set);
    }

    public function get(UuidInterface $setId): ?Set
    {
        return $this->setRepository->findOneById($setId);
    }

    public function delete(Set $set)
    {
        $this->setRepository->delete($set);
    }

    public function update($set)
    {
        $this->setRepository->update($set);
    }

}