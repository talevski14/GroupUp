<?php

namespace Controllers;

use Core\Weather;
use DI\DependencyException;
use DI\NotFoundException;
use GuzzleHttp\Exception\GuzzleException;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Rakit\Validation\Validator;

class EventController extends Controller
{
    /**
     * @throws NotFoundException
     * @throws GuzzleException
     * @throws DependencyException
     */
    public function index(ServerRequestInterface $request, ResponseInterface $response, array $args): ResponseInterface
    {
        $flash = $this->container->get("flash");
        $messages = $flash->getMessages();
        $welcomeMessage = '';
        if(isset($messages['welcome'])) {
            $welcomeMessage = $messages['welcome'];
        }

        $passedPage = isset($_GET['passed']) && $_GET['passed'] === 'true';

        $id = (int)$args['id'];
        $members = $this->societyService->getMembersDisplay($id);
        $events = $this->eventService->getOnGoingEventsForSocietyDisplay($id);
        $eventIDs = "";
        $eventsPassed = $this->eventService->getPassedEventsForSocietyDisplay($id);
        $eventIDsPassed = "";
        $eventsWeather = [];

        $eventsObj = $this->eventService->getEventsForSociety($id);

        if (!$eventsObj->isEmpty()) {
            foreach ($eventsObj as $event) {
                if($passedPage && $event->isPassed()){
                    $eventIDsPassed = $eventIDsPassed . $event->getId() . " ";
                } elseif(!$passedPage && !$event->isPassed()) {
                    $weatherDate = $this->eventService->getWeatherEvent($event);
                    $weatherAPI = $this->container->get("weather");
                    $weather = $weatherAPI->getWeather($weatherDate, $event->getLat(), $event->getLon());
                    $eventIDs = $eventIDs . $event->getId() . " ";
                    $eventsWeather[$event->getId()] = [
                        "weather" => $weather
                    ];
                }
            }
        }

        if($passedPage){
            return $this->container->get('view')->render($response, '/event/index.view.php', [
                "society" => $this->societyService->getSociety($id),
                "members" => $members,
                "events" => $eventsPassed,
                "eventIDs" => $eventIDsPassed,
                "profileimg" => $_SESSION['user']->getProfilePicture(),
                "header" => "Past Events",
                "passed" => $passedPage,
                "username" =>$_SESSION['user']->getUsername(),
                "welcomeMessage" => $welcomeMessage
            ]);
        }

        return $this->container->get('view')->render($response, '/event/index.view.php', [
            "society" => $this->societyService->getSociety($id),
            "members" => $members,
            "events" => $events,
            "eventIDs" => $eventIDs,
            "profileimg" => $_SESSION['user']->getProfilePicture(),
            "header" => "On-going events",
            "passsed" => $passedPage,
            "username" =>$_SESSION['user']->getUsername(),
            "welcomeMessage" => $welcomeMessage,
            "eventsWeather" => $eventsWeather
        ]);
    }

    public function create(ServerRequestInterface $request, ResponseInterface $response, array $args): ResponseInterface
    {
        $id = $args['id'];

        $username = $_SESSION['user']->getUsername();
        $user = $this->userService->getUserByUsername($username);

        $date = date('y-m-d h:i');
        $dateArr = explode(' ', $date);
        $date = $dateArr[0] . ' ' . $dateArr[1];

        return $this->container->get("view")->render($response, "event/create.view.php", [
                "profileimg" => $user->getProfilePicture(),
                "id" => $id,
                "currentTime" => $date,
                "header" => "Create an event",
                "username" =>$_SESSION['user']->getUsername()
            ]
        );
    }

    public function upload(ServerRequestInterface $request, ResponseInterface $response, array $args): ResponseInterface
    {
        $data = $request->getParsedBody();

        $validation = $this->eventService->validateEvent($data);

        if ($validation->fails()) {
            $errors = $validation->errors();
            return $this->container->get("view")->render($response, "event/create.view.php", [
                'profileimg' => $_SESSION["user"]->getProfilePicture(),
                'name' => $errors->get("name"),
                'id' => $args['id'],
                'event-time' => $errors->get("event-time"),
                'description' => $errors->get("description"),
                'lat' => $errors->get("_lat"),
                'lon' => $errors->get("_lon"),
                "username" =>$_SESSION['user']->getUsername()
            ]);
        }

        $event = $this->eventService->addEvent($data, $_SESSION["user"]->getUserId(), $args["id"]);
        $this->notificationService->queueNotification($event);

        header("location: /society/" . $args['id']);
        return $response;
    }

    public function comment(ServerRequestInterface $request, ResponseInterface $response, array $args): ResponseInterface
    {
        $data = $request->getParsedBody();

        $userId = $_SESSION['user']->getUserId();
        $societyId = $this->commentService->addComment($data, $userId);

        header("location: /society/" . $societyId);
        return $response;
    }

    public function response(ServerRequestInterface $request, ResponseInterface $response, array $args): ResponseInterface
    {
        $data = $request->getParsedBody();
        $eventID = (int)$data['event'];
        $userID = (int)$_SESSION['user']->getUserId();

        $_response = $data['_response'];

        $societyId = $this->eventService->attendEvent($userID, $eventID, $_response);

        header("location: /society/" . $societyId);
        return $response;
    }

    public function delete(ServerRequestInterface $request, ResponseInterface $response, array $args): ResponseInterface
    {
        $data = $request->getParsedBody();
        $eventID = $data['_eventIDDelete'];
        $societyID = $this->eventService->deleteEvent($eventID);

        header("location: /society/" . $societyID);
        return $response;
    }

    public function edit(ServerRequestInterface $request, ResponseInterface $response, array $args): ResponseInterface
    {
        $id = $args['idEvent'];

        $username = $_SESSION['user']->getUsername();

        $user = $this->userService->getUserByUsername($username);

        date_default_timezone_set("Europe/Skopje");
        $date = date('y-m-d h:i');
        $dateArr = explode(' ', $date);
        $date = $dateArr[0] . ' ' . $dateArr[1];

        $data = $this->eventService->getEventByIdForDisplay($id);

        return $this->container->get("view")->render($response, "event/edit.view.php", [
                "profileimg" => $user->getProfilePicture(),
                "filled" => $data,
                "id" => $id,
                "currentTime" => $date,
                "header" => "Edit event",
                "username" =>$username
            ]
        );
    }

    public function update(ServerRequestInterface $request, ResponseInterface $response, array $args): ResponseInterface
    {
        $data = $request->getParsedBody();

        $validation = $this->eventService->validateEvent($data);

        if ($validation->fails()) {
            $errors = $validation->errors();
            return $this->container->get("view")->render($response, "event/edit.view.php", [
                'filled' => $data,
                'profileimg' => $_SESSION["user"]->getProfilePicture(),
                'name' => $errors->get("name"),
                'event-time' => $errors->get("event-time"),
                'description' => $errors->get("description"),
                'lat' => $errors->get("_lat"),
                'lon' => $errors->get("_lon"),
                "username" =>$_SESSION['user']->getUsername()
            ]);
        }

        $societyId = $this->eventService->updateEvent($data, $args['idEvent']);
        header("location: /society/" . $societyId);
        return $response;
    }
}