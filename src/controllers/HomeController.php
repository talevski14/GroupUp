<?php

namespace Controllers;

use DI\Container;
use exceptions\EmailAlreadyExists;
use exceptions\UsernameAlreadyExists;
use GuzzleHttp\Client;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Twig\Error\LoaderError;
use Twig\Error\RuntimeError;
use Twig\Error\SyntaxError;
use Rakit\Validation\Validator;

class HomeController extends Controller
{
    /**
     * @throws RuntimeError
     * @throws SyntaxError
     * @throws LoaderError
     */
    public function index(ServerRequestInterface $request, ResponseInterface $response, array $args): ResponseInterface
    {
        return $this->container->get("view")->render($response, "account/index.view.php");
    }

    public function signup(ServerRequestInterface $request, ResponseInterface $response, array $args): ResponseInterface
    {
        return $this->container->get("view")->render($response, "account/create.view.php");
    }

    public function login(ServerRequestInterface $request, ResponseInterface $response, array $args): ResponseInterface
    {
        $data = $request->getParsedBody();

        $username = $data['username'];

        $userDb = $this->userService->getUserByUsername($username);

        if ($userDb !== null) {
            if(!$userDb->isActive()) {
                return $this->container->get("view")->render($response, "account/index.view.php", [
                    'errorActivation' => 'Your account is currently deactivated. Please activate your account by clicking this link.',
                    'filled' => $data,
                    'passwordfill' => $data["password"]
                ]);
            }
            $password = $data['password'];
            $password_db = $userDb->getHashedPassword();

            if (password_verify($password, $password_db)) {
                require __DIR__ . "/../core/functions.php";
                login($userDb);
                header("location: /home");
            } else {
                return $this->container->get("view")->render($response, "account/index.view.php", [
                    'error' => 'No matching account.',
                    'filled' => $data
                ]);
            }
        } else {
            return $this->container->get("view")->render($response, "account/index.view.php", [
                'error' => 'No matching account.',
            ]);
        }
        return $response;
    }

    public function create(ServerRequestInterface $request, ResponseInterface $response, array $args): ResponseInterface
    {
        $data = $request->getParsedBody();

        $validation = $this->userService->validateUserData($data);

        if ($validation->fails()) {
            $errors = $validation->errors();
            return $this->container->get("view")->render($response, "account/create.view.php", [
                'filled' => $data,
                'name' => $errors->get("name"),
                'email' => $errors->get("email"),
                'password' => $errors->get("password"),
                'username' => $errors->get("username")
            ]);
        } else {
            try {
                $user = $this->userService->createAccount($data);
            } catch (UsernameAlreadyExists $exception) {
                return $this->container->get("view")->render($response, "account/create.view.php", [
                    'error' => "This username already exists!"
                ]);
            } catch (EmailAlreadyExists $exception) {
                return $this->container->get("view")->render($response, "account/create.view.php", [
                    'error' => "There is an account registered on this email."
                ]);
            }

            require __DIR__ . "/../core/functions.php";
            login($user);

            header("location: /photo");
        }

        return $response;
    }

    public function add_photo(ServerRequestInterface $request, ResponseInterface $response, array $args): ResponseInterface
    {
        return $this->container->get("view")->render($response, "account/upload.view.php");
    }

    public function upload(ServerRequestInterface $request, ResponseInterface $response, array $args): ResponseInterface
    {
        $database = $this->container->get("db");
        $files = $request->getUploadedFiles();

        if ($files['uploadfile']->getSize() !== 0) {

            $file = $files['uploadfile'];

            $username = $_SESSION["user"]->getUsername();

            $folder = $this->userService->uploadPhoto($username);

            require __DIR__ . "/../core/functions.php";

            $user = $this->userService->getUserByUsername($username);
            login($user);

            $file->moveTo($folder);
        }
        header('location: /home');
        return $response;
    }

    public function logout(ServerRequestInterface $request, ResponseInterface $response, array $args): ResponseInterface
    {
        require __DIR__ . "/../core/functions.php";
        logout();
        header('location: /');
        return $response;
    }

}