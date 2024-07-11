<?php

namespace Controllers;

use DI\DependencyException;
use DI\NotFoundException;
use Models\Society;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Services\UserService;
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

        $link = $this->linkService->findLinkByUri($id);
        if($link !== null) {

            $isValid = $this->linkService->checkIfValid($id);

            $username = $_SESSION['user']['username'];
            $society = $this->societyService->findSocietyByUri($id);

            if (!$isValid) {
                $this->linkService->removeLink($link);
                return $this->container->get("view")->render($response, "errors/410.view.php");
            } else {
                $this->societyService->enterSocietyByUri($username, $id);
            }

            $this->container->get('flash')->addMessage('welcome', "We are so glad to see you! Welcome to " . $society->getName() . "!");
            header("location: /society/" . $society->getId());
            return $response;
        }
        else return $this->container->get("view")->render($response, "errors/404.view.php");
    }

    public function generate(ServerRequestInterface $request, ResponseInterface $response, array $args): ResponseInterface
    {
        $societyID = $args['id'];

        $link = $this->societyService->generateLinkForSocietyId($societyID);
        return $this->container->get("view")->render($response, "event/inviteLink.view.php", [
            "link" => $link->getUri(),
            "society" => $link->getSociety()->getName(),
            "societyID" => $societyID
        ]);
    }
}