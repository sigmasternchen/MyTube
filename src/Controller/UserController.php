<?php


namespace App\Controller;


use App\Service\UserService;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\BinaryFileResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\AccessDeniedHttpException;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\Routing\Annotation\Route;

class UserController extends AbstractController
{
    private const USER_RELATIVE = "../";
    private const USER_DIRECTORY = "content/users/";

    private $userService;

    public function __construct(UserService $userService)
    {
        $this->userService = $userService;
    }

    /**
     * @Route("/user/{username}/picture", name="app_user_profile_picture")
     */
    public function userProfilePicture($username): Response
    {
        $user = $this->userService->getUserByEmail($username);
        if (!$user) {
            throw new NotFoundHttpException();
        }

        $file = self::USER_RELATIVE . self::USER_DIRECTORY . $user->getId() . "/profile.png";

        if (file_exists($file)) {
            return new BinaryFileResponse($file);
        } else {
            return new BinaryFileResponse("../public/images/user.png");
        }
    }

    /**
     * @Route("/admin/users", name="app_user_list")
     */
    public function userList(): Response
    {
        if (!$this->isGranted("ROLE_ADMIN")) {
            // not logged in
            throw new AccessDeniedHttpException();
        }

        $users = $this->userService->getUsers();

        return $this->render("user/users.html.twig", [
            "users" => $users
        ]);
    }
}