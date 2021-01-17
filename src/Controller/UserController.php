<?php


namespace App\Controller;


use App\Entity\User;
use App\Form\UserType;
use App\Mapper\CustomUuidMapper;
use App\Service\UserService;
use Doctrine\DBAL\Types\ConversionException;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\BinaryFileResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\AccessDeniedHttpException;
use Symfony\Component\HttpKernel\Exception\BadRequestHttpException;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\Routing\Annotation\Route;

class UserController extends AbstractController
{
    private const USER_RELATIVE = "../";
    private const USER_DIRECTORY = "content/users/";

    public const DELETE_USER_CSRF_TOKEN_ID = "delete-user";

    private $userService;
    private $uuidMapper;

    public function __construct(
        UserService $userService,
        CustomUuidMapper $uuidMapper
    )
    {
        $this->userService = $userService;
        $this->uuidMapper = $uuidMapper;
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
        if (!$this->isGranted(User::ROLE_ADMIN)) {
            // not logged in
            throw new AccessDeniedHttpException();
        }

        $users = array_map(function ($user) {
            $user->setCustomId($this->uuidMapper->toString($user->getId()));
            return $user;
        }, $this->userService->getUsers());

        return $this->render("user/users.html.twig", [
            "current" => $this->userService->getLoggedInUser(),
            "users" => $users
        ]);
    }

    /**
     * @Route("/admin/users/delete", name="app_user_delete")
     */
    public function userDelete(Request $request): Response
    {

        if (!$this->isGranted(User::ROLE_ADMIN)) {
            // not logged in
            throw new AccessDeniedHttpException();
        }

        $token = $request->request->get("csrfToken");
        $userId = $request->request->get("userId");

        if (!$this->isCsrfTokenValid(self::DELETE_USER_CSRF_TOKEN_ID, $token)) {
            throw new AccessDeniedHttpException();
        }

        if (!$userId) {
            throw new BadRequestHttpException();
        }

        try {
            $userId = $this->uuidMapper->fromString($userId);
        } catch (ConversionException $e) {
            throw new BadRequestHttpException();
        }

        $user = $this->userService->get($userId);
        if ($user == null) {
            throw new NotFoundHttpException();
        }

        if ($user == $this->userService->getLoggedInUser()) {
            throw new BadRequestHttpException();
        }

        if ($user->isSuperAdmin()) {
            throw new AccessDeniedHttpException();
        }

        $this->userService->delete($user);

        return $this->redirectToRoute("app_user_list");
    }

    /**
     * @Route("/admin/users/new", name="app_user_add")
     */
    public function userAdd(Request $request): Response
    {
        if (!$this->isGranted(User::ROLE_ADMIN)) {
            throw new AccessDeniedHttpException();
        }

        $user = new User();
        $form = $this->createForm(UserType::class, $user);

        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $user = $form->getData();

            if ($user->isSuperAdmin()) {
                throw new BadRequestHttpException();
            }

            $this->userService->add($user);

            return $this->redirectToRoute("app_user_list");
        }

        return $this->render("user/user-new.html.twig", [
            "form" => $form->createView()
        ]);
    }
}