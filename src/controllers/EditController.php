<?php

namespace Controllers;

use exceptions\NewPasswordSameAsOldException;
use exceptions\UsernameAlreadyExists;
use exceptions\WrongPasswordException;
use exceptions\WrongRepeatedPasswordException;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;

class EditController extends Controller
{
    public function edit(ServerRequestInterface $request, ResponseInterface $response, array $args): ResponseInterface
    {
        $user = $this->userService->getUserByUsername($_SESSION["user"]->getUsername());
        $data = $this->userService->getDataFromUser($user);

        return $this->container->get("view")->render($response, "account/edit.view.php", [
            'filled' => $data,
            'profileimg' => $user->getProfilePicture(),
            "username" => $_SESSION['user']->getUsername()
        ]);
    }

    public function editAccount(ServerRequestInterface $request, ResponseInterface $response, array $args): ResponseInterface
    {
        $data = $request->getParsedBody();
        $validation = $this->userService->validateNameAndUsername($data);
        $username = $_SESSION["user"]->getUsername();

        if ($validation->fails()) {
            $errors = $validation->errors();
            return $this->container->get("view")->render($response, "account/edit.view.php", [
                'filled' => $data,
                'name' => $errors->get("name"),
                'username' => $errors->get("username")
            ]);
        } else {
            try {
                $newUsername = $this->userService->updateUserAccount($username, $data);
            } catch (UsernameAlreadyExists $exception) {
                return $this->container->get("view")->render($response, "account/create.view.php", [
                    'error' => "This username already exists!"
                ]);
            }

            $user = $this->userService->getUserByUsername($newUsername);
            $_SESSION["user"] = $user;
        }

        header("location: /home");

        return $response;
    }

    public function editPassword(ServerRequestInterface $request, ResponseInterface $response, array $args): ResponseInterface
    {
        $user = $this->userService->getUserByUsername($_SESSION['user']->getUsername());

        return $this->container->get("view")->render($response, "account/edit.password.view.php", [
            'profileimg' => $user->getProfilePicture(),
            "username" => $user->getUsername()
        ]);
    }

    public function changePassword(ServerRequestInterface $request, ResponseInterface $response, array $args): ResponseInterface
    {
        $data = $request->getParsedBody();

        $validation = $this->userService->validatePassword($data);
        $username = $_SESSION['user']->getUsername();
        $user = $this->userService->getUserByUsername($username);

        if ($validation->fails()) {
            $errors = $validation->errors();
            return $this->container->get("view")->render($response, "account/edit.password.view.php", [
                'profileimg' => $user->getProfilePicture(),
                'oldpassword' => $errors->get("oldpassword"),
                'newpassword' => $errors->get("newpassword"),
                'repeatpassword' => $errors->get("repeatpassword"),
                "username" => $username
            ]);
        } else {
            try {
                $this->userService->editUserPassword($username, $data);
            } catch (WrongPasswordException $exception) {
                return $this->container->get("view")->render($response, "account/edit.password.view.php", [
                    'error' => "Please write the correct password of this account.",
                    'profileimg' => $user->getProfilePicture(),
                    "username" => $username
                ]);
            } catch (WrongRepeatedPasswordException $exception) {
                return $this->container->get("view")->render($response, "account/edit.password.view.php", [
                    'error' => "Please repeat the correct password.",
                    'profileimg' => $user->getProfilePicture(),
                    "username" => $username
                ]);
            } catch (NewPasswordSameAsOldException $exception) {
                return $this->container->get("view")->render($response, "account/edit.password.view.php", [
                    'error' => "The new password can't be the old password.",
                    'profileimg' => $user->getProfilePicture(),
                    "username" => $username
                ]);
            }

            require __DIR__ . "/../core/functions.php";

            $user = $this->userService->getUserByUsername($username);

            login($user);

            header("location: /home");
        }

        return $response;
    }

    public function remove(ServerRequestInterface $request, ResponseInterface $response, array $args): ResponseInterface
    {
        $this->userService->deactivateAccount($_SESSION["user"]->getUsername());

        require __DIR__ . "/../core/functions.php";
        logout();
        header("location: /");
        return $response;
    }

    public function activate(ServerRequestInterface $request, ResponseInterface $response, array $args): ResponseInterface
    {
        $data = $request->getParsedBody();

        $username = $data['_username'];
        $password = $data['_password'];

        try {
            $this->userService->activateAccount($username, $password);
        } catch (WrongPasswordException $exception) {
            return $this->container->get("view")->render($response, "account/index.view.php", [
                'error' => 'Wrong password.',
                'filled' => $data
            ]);
        }
        $user = $this->userService->getUserByUsername($username);
        login($user);
        $this->container->get('flash')->addMessage('activate', 'Welcome back! You just activated your account.');
        header("location: /home");
        return $response;
    }

}