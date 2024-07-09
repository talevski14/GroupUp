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

        $database = $this->container->get("db");

//        find user
//        $userDb = $database->query("select * from users where username = :username", [
//            ':username' => $username
//        ])->find();
        $userDb = $this->userService->getUserByUsername($username);

//        find societies by user
        $societies = $userDb->getSocieties();

//        if ($userDb["societies"]) {
//            $societiesStr = explode(";", $userDb["societies"]);
//            array_shift($societiesStr);
//
//            foreach ($societiesStr as $society) {
//                $societyDb = $database->query("select * from societies where id = :id", [
//                    ":id" => $society
//                ])->find();
//                $societies[] = $societyDb;
//            }
//        }

        return $this->container->get("view")->render($response, "society/index.view.php", [
            // find profile picture by username
            "profileimg" => $userDb->getProfilePicture(),
//            "profileimg" => $userDb["profpic"],
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

//        $userDb = $database->query("select * from users where username = :username", [
//            ':username' => $username
//        ])->find();
        $userDb = $this->userService->getUserByUsername($username);

        return $this->container->get("view")->render($response, "society/create.view.php", [
            // find profile picture by username
                "profileimg" => $userDb->getProfilePicture(),
//                "profileimg" => $userDb["profpic"],
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

//        $userDb = $database->query("select * from users where username = :username", [
//            ':username' => $username
//        ])->find();
        $userDb = $this->userService->getUserByUsername($username);
        $entityManager = $this->container->get("entityManager");

        $name = $data["name"];

        $societyID = $this->societyService->addNewSociety($name, $description, $userDb);

//        $database->query("insert into societies(name,members,description) values(:name, :members, :description)", [
//            ":name" => $name,
//            ":members" => ";" . $username,
//            ":description" => $description
//        ]);

//        $societyID = $database->getConnection()->lastInsertId();

        $files = $request->getUploadedFiles();

        if ($files['uploadfile']->getSize() !== 0) {
            $file = $files['uploadfile'];

//            $society = $database->query("select * from societies where id = :id", [
//                ":id" => $societyID
//            ])->find();

//            $banner = $society['id'] . "-banner.jpg";
            $banner = $societyID . "-banner.jpg";

            $folder = __DIR__ . "/../../public/images/society/" . $banner;

            $file->moveTo($folder);

//            $database->query("update societies set banner = :banner where id = :id", [
//                ":banner" => "/images/society/" . $banner,
//                ":id" => $societyID
//            ]);
            $this->societyService->addBanner($societyID, $banner);
//            $society->setBanner("/images/society/" . $banner);
//            $entityManager->persist($society);
//            $entityManager->flush($society);
        }

//        $societies = $userDb["societies"] . ";" . $societyID;
//        $database->query("update users set societies = :societies where username = :username", [
//            ":societies" => $societies,
//            ":username" => $username
//        ]);

        require __DIR__ . "/../core/functions.php";

        header("location: /society/" . $societyID . "/invite-link");
        return $response;
    }

    public function leave(ServerRequestInterface $request, ResponseInterface $response, array $args): ResponseInterface
    {
        $societyID = $args['id'];
        $database = $this->container->get("db");

        $society = $database->query("select * from societies where id = :id", [
            ":id" => $societyID
        ])->find();

        $user = $database->query("select * from users where id = :id", [
            ":id" => $_SESSION['user']['id']
        ])->find();

        $oldSocieties = $user['societies'];
        $oldSocieties = explode(";", $oldSocieties);
        array_shift($oldSocieties);

        $newSocieties = "";

        foreach ($oldSocieties as $oldSociety) {
            if ($oldSociety != $societyID) {
                $newSocieties = $newSocieties . ";" . $oldSociety;
            }
        }

        $database->query("update users set societies = :societies where id = :id", [
            ":societies" => $newSocieties,
            ":id" => $user['id']
        ]);

        $oldMembers = $society['members'];
        $oldMembers = explode(";", $oldMembers);
        array_shift($oldMembers);

        $newMembers = "";

        foreach ($oldMembers as $oldMember) {
            if ($oldMember != $user['username']) {
                $newMembers = $newMembers . ";" . $oldMember;
            }
        }

        $database->query("update societies set members = :members where id = :id", [
            ":members" => $newMembers,
            ":id" => $society['id']
        ]);

        if ($newMembers == "") {
//            $invites = $society["invites"];
//
//            $invites = explode(";", $invites);
//            array_shift($invites);

            $database->query("delete from events where society = :society", [
                ":society" => $societyID
            ]);

            $database->query("delete from societies where id = :id", [
                ":id" => $societyID
            ]);
            if (file_exists(__DIR__ . "/../../public/images/society/" . $societyID . "-banner.jpg")) {
                unlink(__DIR__ . "/../../public/images/society/" . $societyID . "-banner.jpg");
            }
        } else {
            $events = $database->query("select id from events where creator = :creator and society = :society", [
                ":creator" => $user['id'],
                ":society" => $societyID
            ])->get();

            $newEvents = [];
            foreach ($events as $event) {
                $newEvents[] = $event['id'];
            }
            $events = $newEvents;

            $oldEvents = $database->query("select * from societies where id = :id", [
                ":id" => $societyID
            ])->find()['events'];

            $newEvents = "";

            if (isset($oldEvents)) {
                $oldEvents = explode(";", $oldEvents);
                array_shift($oldEvents);

                foreach ($oldEvents as $event) {
                    if (!in_array($event, $events)) {
                        $newEvents = $newEvents . ";" . $event;
                    }
                }
            }

            foreach ($events as $event) {
                $database->query("delete from events where id = :id", [
                    ":id" => $event
                ]);
            }

            $database->query("update societies set events = :events where id = :id", [
                ":events" => $newEvents,
                ":id" => $societyID
            ]);
        }

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