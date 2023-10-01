<?php

namespace Controllers;

use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Rakit\Validation\Validator;

class EditController extends Controller
{
    public function edit(ServerRequestInterface $request, ResponseInterface $response, array $args): ResponseInterface
    {
        $database = $this->container->get("db");
        $user = $database->query("select * from users where username = :username", [
            ":username" => $_SESSION['user']['username']
        ])->find();

        return $this->container->get("view")->render($response, "account/edit.view.php", [
            'filled' => $user,
            'profileimg' => $user['profpic'],
            "username" =>$_SESSION['user']['username']
        ]);
    }

    public function editAccount(ServerRequestInterface $request, ResponseInterface $response, array $args): ResponseInterface
    {
        $data = $request->getParsedBody();

        $validator = new Validator;

        $database = $this->container->get("db");
        $user = $database->query("select * from users where username = :username", [
            ":username" => $_SESSION['user']['username']
        ])->find();

        $validation = $validator->make($data, [
            'name' => 'required',
            'username' => 'required|min:6'
        ]);

        $validation->validate();

        if ($validation->fails()) {
            $errors = $validation->errors();
            return $this->container->get("view")->render($response, "account/edit.view.php", [
                'filled' => $data,
                'name' => $errors->get("name"),
                'username' => $errors->get("username")
            ]);
        } else {
            if($data['name'] !== $user['name']) {
                $database->query("update users set name = :name where username = :username",[
                    ":name" => $data['name'],
                    ":username" => $user['username']
                ]);

                $_SESSION['user']['name'] = $data['name'];
            }

            if($data['username'] !== $user['username']) {
                $username = $data['username'];

                $userDb = $database->query("select * from users where username = :username", [
                    ':username' => $username
                ])->find();

                if ($userDb) {
                    return $this->container->get("view")->render($response, "account/create.view.php", [
                        'error' => "This username already exists!"
                    ]);
                }

                $database->query("update users set username = :username_new where username = :username_old",[
                    ":username_new" => $data['username'],
                    ":username_old" => $user['username']
                ]);

                $_SESSION['user']['username'] = $data['username'];
            }
        }

        header("location: /home");

        return $response;
    }

    public function editPassword(ServerRequestInterface $request, ResponseInterface $response, array $args): ResponseInterface
    {
        $database = $this->container->get("db");
        $user = $database->query("select * from users where username = :username", [
            ":username" => $_SESSION['user']['username']
        ])->find();

        return $this->container->get("view")->render($response, "account/edit.password.view.php", [
            'filled' => $user,
            'profileimg' => $user['profpic'],
            "username" =>$_SESSION['user']['username']
        ]);
    }

    public function changePassword(ServerRequestInterface $request, ResponseInterface $response, array $args): ResponseInterface
    {
        $data = $request->getParsedBody();

        $validator = new Validator;

        $database = $this->container->get("db");
        $user = $database->query("select * from users where username = :username", [
            ":username" => $_SESSION['user']['username']
        ])->find();

        $validation = $validator->make($data, [
            'oldpassword' => 'required',
            'newpassword' => 'required|min:6',
            'repeatpassword' => 'required'
        ]);

        $validation->setMessages([
            'required' => 'This field is required.',
            'newpassword' => "The new password should be minimum 6 characters."
        ]);

        $validation->validate();

        if ($validation->fails()) {
            $errors = $validation->errors();
            return $this->container->get("view")->render($response, "account/edit.password.view.php", [
                'profileimg' => $user["profpic"],
                'oldpassword' => $errors->get("oldpassword"),
                'newpassword' => $errors->get("newpassword"),
                'repeatpassword' => $errors->get("repeatpassword"),
                "username" =>$_SESSION['user']['username']
            ]);
        } else {
            $oldPass = $data['oldpassword'];
            $newPass = $data['newpassword'];
            $repeatPass = $data['repeatpassword'];

            $passwordDb = $user['password'];

            if(!password_verify($oldPass, $passwordDb)) {
                return $this->container->get("view")->render($response, "account/edit.password.view.php", [
                    'error' => "Please write the correct password of this account.",
                    'profileimg' => $user["profpic"],
                    "username" =>$_SESSION['user']['username']
                ]);
            }

            if ($newPass !== $repeatPass) {
                return $this->container->get("view")->render($response, "account/edit.password.view.php", [
                    'error' => "Please repeat the correct password.",
                    'profileimg' => $user["profpic"],
                    "username" =>$_SESSION['user']['username']
                ]);
            }

            if ($oldPass === $newPass) {
                return $this->container->get("view")->render($response, "account/edit.password.view.php", [
                    'error' => "The new password can't be the old password.",
                    'profileimg' => $user["profpic"],
                    "username" =>$_SESSION['user']['username']
                ]);
            }

            $database->query("update users set password = :password where username = :username", [
                ":password" => password_hash($newPass, PASSWORD_BCRYPT),
                ":username" => $user['username']
            ]);

            require __DIR__ . "/../core/functions.php";

            $userDb = $database->query("select * from users where username = :username", [
                ':username' => $user['username']
            ])->find();

            login($userDb);

            header("location: /home");
        }

        return $response;
    }

    public function remove(ServerRequestInterface $request, ResponseInterface $response, array $args): ResponseInterface
    {
        $database = $this->container->get("db");
        $user = $database->query("select * from users where username = :username", [
            ":username" => $_SESSION['user']['username']
        ])->find();

        $database->query("update users set active = 0 where username = :username", [
            ":username" => $user["username"]
        ]);

        require __DIR__ . "/../core/functions.php";
        logout();
        header("location: /");
        return $response;
    }

    public function activate(ServerRequestInterface $request, ResponseInterface $response, array $args): ResponseInterface
    {
        $data = $request->getParsedBody();

        $database = $this->container->get("db");
        $username = $data['_username'];
        $password = $data['_password'];

        $user = $database->query("select * from users where username = :username", [
            ":username" => $username
        ])->find();

        $password_db = $user["password"];

        if (password_verify($password, $password_db)) {
            require __DIR__ . "/../core/functions.php";

            $database->query("update users set active = 1 where username = :username", [
                ":username" => $username
            ]);

            login($user);
            $this->container->get('flash')->addMessage('activate', 'Welcome back! You just activated your account.');
            header("location: /home");

        } else {
            return $this->container->get("view")->render($response, "account/index.view.php", [
                'error' => 'Wrong password.',
                'filled' => $data
            ]);
        }

        return $response;
    }

}