<?php

namespace Controllers;

use DI\DependencyException;
use DI\NotFoundException;
use models\Society;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use services\UserService;
use Twig\Error\LoaderError;
use Twig\Error\RuntimeError;
use Twig\Error\SyntaxError;

class DashboardController extends Controller
{
    /**
     * @throws SyntaxError
     * @throws RuntimeError
     * @throws LoaderError
     */
    public function dashboard(ServerRequestInterface $request, ResponseInterface $response, array $args): ResponseInterface
    {
        $flash = $this->container->get('flash');
        $messages = $flash->getMessages();
        $activationMessage = '';
        if(isset($messages['activate'])) {
            $activationMessage = $messages['activate'];
        }
        $username = $_SESSION['user']['username'];

        $userDb = $this->userService->getUserByUsername($username);

        $societies = $userDb->getSocieties();

        return $this->container->get("view")->render($response, "society/index.view.php", [
            "profileimg" => $userDb->getProfilePicture(),
            "header" => "Societies",
            "societies" => $societies,
            "username" => $_SESSION['user']['username'],
            "activationMessage" => $activationMessage
        ]);
    }

    /**
     * @throws RuntimeError
     * @throws SyntaxError
     * @throws LoaderError
     */
    public function create(ServerRequestInterface $request, ResponseInterface $response, array $args): ResponseInterface
    {
        $database = $this->container->get("db");

        $username = $_SESSION['user']['username'];

        $userDb = $this->userService->getUserByUsername($username);

        return $this->container->get("view")->render($response, "society/create.view.php", [
                "profileimg" => $userDb->getProfilePicture(),
                "header" => "Create a society"
            ]
        );
    }

    /**
     * @throws DependencyException
     * @throws NotFoundException
     */
    public function insert(ServerRequestInterface $request, ResponseInterface $response, array $args): ResponseInterface
    {
        $data = $request->getParsedBody();
        $database = $this->container->get("db");

        $username = $_SESSION["user"]["username"];

        $description = $data["description"];

        $userDb = $this->userService->getUserByUsername($username);
        $entityManager = $this->container->get("entityManager");

        $name = $data["name"];

        $societyID = $this->societyService->addNewSociety($name, $description, $userDb);

        $files = $request->getUploadedFiles();

        if ($files['uploadfile']->getSize() !== 0) {
            $file = $files['uploadfile'];

            $banner = $societyID . "-banner.jpg";

            $folder = __DIR__ . "/../../public/images/society/" . $banner;

            $file->moveTo($folder);

            $this->societyService->addBanner($societyID, $banner);
        }

        require __DIR__ . "/../core/functions.php";

        header("location: /society/" . $societyID . "/invite-link");
        return $response;
    }

    public function leave(ServerRequestInterface $request, ResponseInterface $response, array $args): ResponseInterface
    {
        $societyId = $args['id'];
        $userId = $_SESSION['user']['id'];
        $this->societyService->leaveSociety($userId, $societyId);

        header("location: /home");
        return $response;
    }

    public function accept(ServerRequestInterface $request, ResponseInterface $response, array $args): ResponseInterface
    {
        $params = $request->getQueryParams();
        $id = $params['id'];
        $database = $this->container->get("db");

        $link = $database->query("select * from links where body = :body", [
            ":body" => $id
        ])->find();

        $society = $database->query("select * from societies where id = :id", [
            ":id" => $link['society']
        ])->find();
        $time = $link['created_on'];

        $members = $society['members'];
        $members = explode(";", $members);
        array_shift($members);

        $currentTime = date("y-m-d H:i:s", strtotime('-1 hours'));
        if (strtotime($time) < strtotime($currentTime)) {
            $database->query("delete from links where society = :society", [
                ":society" => $link['society']
            ]);
            return $this->container->get("view")->render($response, "errors/410.view.php");
        }

        $username = $_SESSION['user']['username'];
        $user = $database->query("select * from users where username = :username", [
            ":username" => $username
        ])->find();

        $members = $society["members"] . ";" . $username;
        $database->query("update societies set members = :members where id = :id", [
            ":members" => $members,
            ":id" => $society['id']
        ]);

        $societies = $user["societies"] . ";" . $society['id'];
        $database->query("update users set societies = :societies where username = :username", [
            ":societies" => $societies,
            ":username" => $username
        ]);

        $this->container->get('flash')->addMessage('welcome', "We are so glad to see you! Welcome to " . $society['name'] . "!");
        header("location: /society/" . $society['id']);
        return $response;
    }

    public function generate(ServerRequestInterface $request, ResponseInterface $response, array $args): ResponseInterface
    {
        require __DIR__ . "/../core/functions.php";
        $uuid = guidv4();
        $societyID = $args['id'];

        $database = $this->container->get("db");

        $society = $database->query("select * from societies where id = :id", [
            ":id" => $societyID
        ])->find();

        $oldLink = $database->query("select * from links where society = :society", [
            ":society" => $societyID
        ])->find();

        if($oldLink) {
            $time = $oldLink['created_on'];
            $currentTime = date("y-m-d H:i:s", strtotime('-1 hours'));
            $bool = strtotime($time) < strtotime($currentTime);

            if($bool) {
                $database->query("delete from links where society = :society", [
                    ":society" => $oldLink['society']
                ]);
            }
        }

        if ($oldLink && !$bool) {
            return $this->container->get("view")->render($response, "event/inviteLink.view.php", [
                "link" => $oldLink['body'],
                "society" => $society['name'],
                "societyID" => $societyID
            ]);
        }

        $database->query("insert into links(society,body,created_on) values(:society,:body,:created_on)", [
            ":society" => $societyID,
            ":body" => $uuid,
            ":created_on" => date("y-m-d H:i:s")
        ]);

        return $this->container->get("view")->render($response, "event/inviteLink.view.php", [
            "link" => $uuid,
            "society" => $society['name'],
            "societyID" => $societyID
        ]);
    }
}