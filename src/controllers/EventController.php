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

        $id = $args['id'];
        $database = $this->container->get("db");
        $society = $database->query("select * from societies where id = :id", [
            ':id' => $id
        ])->find();

        $membersUsernames = explode(';', $society['members']);
        array_shift($membersUsernames);
        $members = [];
        foreach ($membersUsernames as $member) {
            $memberFull = $database->query("select * from users where username = :username", [
                ':username' => $member
            ])->find();

            $memberDisplay = [
                'name' => $memberFull['name'],
                'username' => $memberFull['username'],
                'photo' => $memberFull['profpic']
            ];
            $members[] = $memberDisplay;
        }

        $events = [];
        $eventIDs = [];
        $eventsPassed = [];
        $eventIDsPassed = [];

        if (isset($society['events'])) {
            $eventsId = explode(";", $society['events']);
            array_shift($eventsId);
            foreach ($eventsId as $event) {
                $eventDB = $database->query("select * from events where id = :id", [
                    ':id' => $event
                ])->find();
                if (strtotime($eventDB["date_and_time"]) < strtotime("now")) {
                    $database->query("update events set passed = 1 where id = :id", [
                        ":id" => $event
                    ]);
                }
                $eventDB = $database->query("select * from events where id = :id", [
                    ':id' => $event
                ])->find();


                $attending = [];
                $attendBool = false;
                if (isset($eventDB['attending'])) {
                    $attendingId = explode(';', $eventDB['attending']);
                    array_shift($attendingId);

                    $attendBool = in_array($_SESSION['user']['id'], $attendingId);
                    foreach ($attendingId as $attend) {
                        $user = $database->query("select * from users where id = :id", [
                            ':id' => $attend
                        ])->find();

                        $userDisplay = [
                            'name' => $user['name'],
                            'username' => $user['username'],
                            'photo' => $user['profpic']
                        ];
                        $attending[] = $userDisplay;
                    }
                }

                $comments = [];
                if (isset($eventDB['discussion'])) {
                    $commentsId = explode(';', $eventDB['discussion']);
                    array_shift($commentsId);
                    foreach ($commentsId as $commentId) {
                        $comment = $database->query("select * from comments where id = :id", [
                            ':id' => $commentId
                        ])->find();

                        $user = $database->query("select * from users where id = :id", [
                            ':id' => $comment['user_id']
                        ])->find();

                        $comments[] = [
                            'photo' => $user['profpic'],
                            'username' => $user['username'],
                            'body' => $comment['body']
                        ];
                    }
                }

                $creatorId = $eventDB['creator'];
                $creatorFull = $database->query("select * from users where id = :id", [
                    ":id" => $creatorId
                ])->find();

                $creator = [
                    'photo' => $creatorFull['profpic'],
                    'name' => $creatorFull['name']
                ];

                $editable = $creatorId == $_SESSION['user']['id'];

                $createdOnArr = explode(' ', $eventDB['created_on']);
                $dateArr = explode('-', $createdOnArr[0]);
                $timeArr = explode(':', $createdOnArr[1]);
                $date = $dateArr[2] . "." . $dateArr[1] . "." . $dateArr[0];
                $time = $timeArr[0] . ":" . $timeArr[1];
                $createdOn = $date . " " . $time;

                $dateTimeArr = explode(' ', $eventDB['date_and_time']);
                $dateArr = explode('-', $dateTimeArr[0]);
                $timeArr = explode(':', $dateTimeArr[1]);
                $date = $dateArr[2] . "." . $dateArr[1] . "." . $dateArr[0];
                $time = "$timeArr[0]" . ":" . "$timeArr[1]";
                $weatherDate = $dateTimeArr[0] . "T" . $timeArr[0] . ":00";

                $weatherAPI = $this->container->get("weather");
                $weather = $weatherAPI->getWeather($weatherDate, $eventDB['lat'], $eventDB['lon']);

                if($passedPage && $eventDB['passed'] === 1){
                    $eventsPassed[] = [
                        "id" => $eventDB['id'],
                        "name" => $eventDB['name'],
                        "creator" => $creator,
                        "date" => $date,
                        "time" => $time,
                        "description" => $eventDB['description'],
                        "attending" => $attending,
                        "comments" => $comments,
                        "creation" => $createdOn,
                        "location" => $eventDB['location'],
                        "lat" => $eventDB['lat'],
                        "lon" => $eventDB['lon'],
                        "weather" => $weather,
                        "attendBool" => $attendBool,
                        "passed" => $eventDB['passed']
                    ];

                    $eventIDsPassed[] = $eventDB['id'];
                } elseif(!$passedPage && $eventDB['passed'] === 0) {
                    $events[] = [
                        "id" => $eventDB['id'],
                        "name" => $eventDB['name'],
                        "creator" => $creator,
                        "date" => $date,
                        "time" => $time,
                        "description" => $eventDB['description'],
                        "attending" => $attending,
                        "comments" => $comments,
                        "creation" => $createdOn,
                        "location" => $eventDB['location'],
                        "lat" => $eventDB['lat'],
                        "lon" => $eventDB['lon'],
                        "weather" => $weather,
                        "attendBool" => $attendBool,
                        "passed" => $eventDB['passed'],
                        "editable" => $editable
                    ];

                    $eventIDs[] = $eventDB['id'];
                }
            }
        }
        $eventIDs = implode(" ", $eventIDs);
        $eventIDsPassed = implode(" ", $eventIDsPassed);

        if($passedPage){
            return $this->container->get('view')->render($response, '/event/index.view.php', [
                "society" => $society,
                "members" => $members,
                "events" => $eventsPassed,
                "eventIDs" => $eventIDsPassed,
                "profileimg" => $_SESSION['user']['profpic'],
                "header" => "Past Events",
                "passed" => $passedPage,
                "username" =>$_SESSION['user']['username'],
                "welcomeMessage" => $welcomeMessage
            ]);
        }

        return $this->container->get('view')->render($response, '/event/index.view.php', [
            "society" => $society,
            "members" => $members,
            "events" => $events,
            "eventIDs" => $eventIDs,
            "profileimg" => $_SESSION['user']['profpic'],
            "header" => "On-going events",
            "passsed" => $passedPage,
            "username" =>$_SESSION['user']['username'],
            "welcomeMessage" => $welcomeMessage
        ]);
    }

    public function create(ServerRequestInterface $request, ResponseInterface $response, array $args): ResponseInterface
    {
        $id = $args['id'];
        $database = $this->container->get("db");

        $username = $_SESSION['user']['username'];

        $userDb = $database->query("select * from users where username = :username", [
            ':username' => $username
        ])->find();

        $date = date('y-m-d h:i');
        $dateArr = explode(' ', $date);
        $date = $dateArr[0] . ' ' . $dateArr[1];

        return $this->container->get("view")->render($response, "event/create.view.php", [
                "profileimg" => $userDb["profpic"],
                "id" => $id,
                "currentTime" => $date,
                "header" => "Create an event",
                "username" =>$_SESSION['user']['username']
            ]
        );
    }

    public function upload(ServerRequestInterface $request, ResponseInterface $response, array $args): ResponseInterface
    {
        $data = $request->getParsedBody();

        $validator = new Validator;

        $validation = $validator->make($data, [
            'name' => 'required|max:15',
            'event-time' => 'required',
            'description' => 'required',
            '_lat' => 'required',
            '_lon' => 'required'
        ]);

        $validation->setMessages([
            'required' => 'This field is required.',
            'max' => 'Characters maximum is 15.'
        ]);

        $validation->validate();

        if ($validation->fails()) {
            $errors = $validation->errors();
            return $this->container->get("view")->render($response, "event/create.view.php", [
                'profileimg' => $_SESSION["user"]["profpic"],
                'name' => $errors->get("name"),
                'event-time' => $errors->get("event-time"),
                'description' => $errors->get("description"),
                'lat' => $errors->get("_lat"),
                'lon' => $errors->get("_lon"),
                "username" =>$_SESSION['user']['username']
            ]);
        }

        $database = $this->container->get("db");
        date_default_timezone_set("Europe/Skopje");
        $date = date('y-m-d H:i:s');
        $database->query("insert into events(name,society,attending,date_and_time,creator,description,created_on, lat, lon, location) values(:name,:society,:attending,:date_and_time,:creator,:description,:created_on, :lat, :lon, :location)", [
            ":name" => $data['name'],
            ":society" => $args['id'],
            ":attending" => ";" . $_SESSION['user']['id'],
            ":date_and_time" => $data['event-time'],
            ":creator" => $_SESSION['user']['id'],
            ":description" => $data['description'],
            ":created_on" => $date,
            ":lat" => $data['_lat'],
            ":lon" => $data['_lon'],
            ":location" => $data['_location']
        ]);

        $eventId = $database->getConnection()->lastInsertId();

        $society = $database->query("select * from societies where id = :id", [
            ":id" => $args['id']
        ])->find()['events'];

        $previousEvents = $society === null ? "" : $society;

        $previousEvents = $previousEvents . ";" . $eventId;

        $database->query("update societies set events = :events where id = :id", [
            ":events" => $previousEvents,
            ":id" => $args['id']
        ]);

        header("location: /society/" . $args['id']);
        return $response;
    }

    public function comment(ServerRequestInterface $request, ResponseInterface $response, array $args): ResponseInterface
    {
        $data = $request->getParsedBody();

        $userId = $_SESSION['user']['id'];
        $body = $data['body'];

        $database = $this->container->get("db");
        $database->query("insert into comments(user_id, body) values (:user_id, :body)", [
            ":user_id" => $userId,
            ":body" => $body
        ]);

        $commentId = $database->getConnection()->lastInsertId();
        $eventId = $data['_id'];

        $event = $database->query("select * from events where id = :id", [
            ":id" => $eventId
        ])->find();

        $discussion = $event['discussion'];
        $societyId = $event['society'];

        $discussion = $discussion . ";" . $commentId;

        $database->query("update events set discussion = :discussion where id = :id", [
            ":discussion" => $discussion,
            ":id" => $eventId
        ]);

        header("location: /society/" . $societyId);
        return $response;
    }

    public function response(ServerRequestInterface $request, ResponseInterface $response, array $args): ResponseInterface
    {
        $data = $request->getParsedBody();
        $eventID = $data['event'];
        $userID = $_SESSION['user']['id'];
        $db = $this->container->get("db");
        $event = $db->query("select * from events where id = :id", [
            ":id" => $eventID
        ])->find();

        $oldResponse = $event['attending'];

        if ($data['_response'] === "true") {
            $newResponse = isset($oldResponse) ? $oldResponse . ";" . $userID : ";" . $userID;
        } else {
            $responses = explode(";", $oldResponse);
            array_shift($responses);
            $newResponse = '';
            foreach ($responses as $response) {
                if ($response != $_SESSION['user']['id']) {
                    $newResponse = $newResponse . ';' . $response;
                }
            }
        }

        $db->query("update events set attending = :attending where id = :id", [
            ":attending" => $newResponse,
            ":id" => $eventID
        ]);

        header("location: /society/" . $event['society']);
        return $response;
    }

    public function delete(ServerRequestInterface $request, ResponseInterface $response, array $args): ResponseInterface
    {
        $data = $request->getParsedBody();
        $eventID = $data['_eventIDDelete'];

        $database = $this->container->get("db");

        $comments = $database->query("select * from events where id = :id", [
            ":id" => $eventID
        ])->find()['discussion'];

        if(isset($comments)){
            $comments = explode(";", $comments);
            array_shift($comments);

            foreach ($comments as $comment) {
                $database->query("delete from comments where id = :id", [
                    ":id" => $comment
                ]);
            }
        }

        $database->query("delete from events where id = :id", [
            ":id" => $eventID
        ]);

        $societyID = $data['_societyIDDelete'];
        $society = $database->query("select * from societies where id = :id", [
            ":id" => $societyID
        ])->find();

        $events = $society['events'];
        $events = explode(";", $events);
        array_shift($events);

        $newEvents = "";
        foreach ($events as $event) {
            if($event != $eventID)
            {
                $newEvents = $newEvents . ";" . $event;
            }
        }

        $database->query("update societies set events = :events where id = :id", [
            ":events" => $newEvents,
            ":id" => $societyID
        ]);

        header("location: /society/" . $societyID);
        return $response;
    }

    public function edit(ServerRequestInterface $request, ResponseInterface $response, array $args): ResponseInterface
    {
        $id = $args['idEvent'];
        $database = $this->container->get("db");

        $username = $_SESSION['user']['username'];

        $userDb = $database->query("select * from users where username = :username", [
            ':username' => $username
        ])->find();

        date_default_timezone_set("Europe/Skopje");
        $date = date('y-m-d h:i');
        $dateArr = explode(' ', $date);
        $date = $dateArr[0] . ' ' . $dateArr[1];

        $data = $database->query("select * from events where id = :id", [
            ":id" => $id
            ])->find();

        return $this->container->get("view")->render($response, "event/edit.view.php", [
                "profileimg" => $userDb["profpic"],
                "filled" => $data,
                "id" => $id,
                "currentTime" => $date,
                "header" => "Edit event",
                "username" =>$_SESSION['user']['username']
            ]
        );
    }

    public function update(ServerRequestInterface $request, ResponseInterface $response, array $args): ResponseInterface
    {
        $data = $request->getParsedBody();

        $validator = new Validator;

        $validation = $validator->make($data, [
            'name' => 'required|max:15',
            'event-time' => 'required',
            'description' => 'required',
            '_lat' => 'required',
            '_lon' => 'required'
        ]);

        $validation->setMessages([
            'required' => 'This field is required.',
            'max' => 'Characters maximum is 15.'
        ]);

        $validation->validate();

        if ($validation->fails()) {
            $errors = $validation->errors();
            return $this->container->get("view")->render($response, "event/edit.view.php", [
                'filled' => $data,
                'profileimg' => $_SESSION["user"]["profpic"],
                'name' => $errors->get("name"),
                'event-time' => $errors->get("event-time"),
                'description' => $errors->get("description"),
                'lat' => $errors->get("_lat"),
                'lon' => $errors->get("_lon"),
                "username" =>$_SESSION['user']['username']
            ]);
        }

        $database = $this->container->get("db");
        date_default_timezone_set("Europe/Skopje");
        $database->query("update events set name = :name, date_and_time = :date_and_time, description = :description, lat = :lat, lon = :lon, location = :location where id = :id", [
            ":name" => $data['name'],
            ":date_and_time" => $data['event-time'],
            ":description" => $data['description'],
            ":lat" => $data['_lat'],
            ":lon" => $data['_lon'],
            ":location" => $data['_location'],
            ":id" => $args['idEvent']
        ]);

        $society = $database->query("select * from events where id = :id", [
            ":id" => $args['idEvent']
        ])->find()['society'];

        header("location: /society/" . $society);
        return $response;
    }
}