<?php


namespace App\Controller;


use App\Entity\User;
use App\Form\UserType;
use App\Mapper\CustomUuidMapper;
use App\Service\UserService;
use Doctrine\DBAL\Types\ConversionException;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Form\FormError;
use Symfony\Component\HttpFoundation\BinaryFileResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\AccessDeniedHttpException;
use Symfony\Component\HttpKernel\Exception\BadRequestHttpException;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\Routing\Annotation\Route;

class UserController extends AbstractController
{
    public const USER_RELATIVE = "../";
    public const USER_DIRECTORY = "content/users/";

    public const PROFILE_PICTURE_FILE = "/profile.jpg";

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
     * @Route("/user/{id}/picture", name="app_user_profile_picture")
     */
    public function userProfilePicture($id): Response
    {
        try {
            $id = $this->uuidMapper->fromString($id);
        } catch (ConversionException $e) {
            throw new NotFoundHttpException();
        }

        $user = $this->userService->get($id);
        if (!$user) {
            throw new NotFoundHttpException();
        }

        $file = self::USER_RELATIVE . self::USER_DIRECTORY . $user->getId() . self::PROFILE_PICTURE_FILE;

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

    /**
     * @Route("/admin/users/edit", name="app_user_edit")
     */
    public function userEdit(Request $request): Response
    {
        if (!$this->isGranted(User::ROLE_ADMIN)) {
            throw new AccessDeniedHttpException();
        }

        $userId = $request->query->get("user");

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

        $form = $this->createForm(UserType::class, $user, [
            "password_optional" => true
        ]);

        $okay = false;

        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $user = $form->getData();

            if ($user->isSuperAdmin()) {
                throw new BadRequestHttpException();
            }

            $this->userService->update($user);

            $okay = true;
        }

        return $this->render("user/user-edit.html.twig", [
            "ok" => $okay,
            "form" => $form->createView()
        ]);
    }

    /**
     * @Route("/settings", name="app_settings")
     */
    public function settings(Request $request): Response
    {
        if (!$this->isGranted(User::ROLE_USER)) {
            throw new AccessDeniedHttpException();
        }

        $user = $this->userService->getLoggedInUser();


        $form = $this->createForm(UserType::class, $user, [
            "password_optional" => true,
            "roles" => false,
            "profile_picture" => true,
        ]);

        $okay = false;

        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $roles = $user->getRoles();

            $user = $form->getData();

            $user->setRoles($roles);

            $okay = true;

            $file = $form->get("file")->getData();
            if ($file) {
                if (!$this->userService->setProfilePicture($user, $file)) {
                    $form->addError(new FormError("Error while processing profile picture."));
                    $okay = false;
                }
            }

            if ($okay) {
                $this->userService->update($user);
            }
        }

        return $this->render("user/settings.html.twig", [
            "ok" => $okay,
            "form" => $form->createView()
        ]);
    }
}