<?php

namespace Controllers;

use DI\Container;
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

        $database = $this->container->get("db");


        $userDb = $database->query("select * from users where username = :username", [
            ':username' => $username
        ])->find();

        if ($userDb) {
            if($userDb['active'] === 0) {
                return $this->container->get("view")->render($response, "account/index.view.php", [
                    'errorActivation' => 'Your account is currently deactivated. Please activate your account by clicking this link.',
                    'filled' => $data,
                    'passwordfill' => $data["password"]
                ]);
            }
            $password = $data['password'];
            $password_db = $userDb["password"];

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

        $validator = new Validator;

        $database = $this->container->get("db");

        $validation = $validator->make($data, [
            'name' => 'required',
            'email' => 'required|email',
            'password' => 'required|min:6',
            'username' => 'required|min:6'
        ]);

        $validation->validate();

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
            $username = $data['username'];

            $userDb = $database->query("select * from users where username = :username", [
                ':username' => $username
            ])->find();

            if ($userDb) {
                return $this->container->get("view")->render($response, "account/create.view.php", [
                    'error' => "This username already exists!"
                ]);
            }

            $email = $data['email'];

            $userDb = $database->query("select * from users where email = :email", [
                ':email' => $email
            ])->find();

            if ($userDb) {
                return $this->container->get("view")->render($response, "account/create.view.php", [
                    'error' => "There is an account registered on this email."
                ]);
            }

            $database->query("insert into users(name,email,username,password) values(:name, :email, :username, :password)", [
                ':name' => $data['name'],
                ':email' => $data['email'],
                ':username' => $data['username'],
                ':password' => password_hash($data['password'], PASSWORD_BCRYPT)
            ]);

            $userDb = $database->query("select * from users where email = :email", [
                ':email' => $email
            ])->find();

            require __DIR__ . "/../core/functions.php";
            login($userDb);

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

            $username = $_SESSION["user"]["username"];

            $user = $database->query("select * from users where username = :username", [
                ':username' => $username
            ])->find();

            $folder =__DIR__ . "/../../public/images/account/" . $user['id'] . ".jpg";

            $database->query("update users set profpic = :image where username = :username", [
                ":image" => "/images/account/" . $user['id'] . ".jpg",
                ":username" => $username
            ]);

            $userDb = $database->query("select * from users where username = :username", [
                ':username' => $username
            ])->find();

            require __DIR__ . "/../core/functions.php";
            login($userDb);

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