<?php

namespace Services\implementation;

use DateTime;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\EntityManagerInterface;
use Models\Comment;
use Models\Event;
use Models\Society;
use Models\User;
use Predis\Client;
use Rakit\Validation\Validation;
use Rakit\Validation\Validator;
use Services\caching\CachingDataService;
use Services\caching\DataService;
use Services\EventService;

class EventServiceImpl implements EventService
{
    private EntityManagerInterface $entityManager;
    private Client $client;

    /**
     * @param EntityManagerInterface $entityManager
     * @param Client $client
     */
    public function __construct(EntityManagerInterface $entityManager, Client $client)
    {
        $this->entityManager = $entityManager;
        $this->client = $client;
    }

    public function checkIfPassed(Event $event): Event
    {
        $isPassedSet = $event->isPassed();
        if (!$isPassedSet) {
            $dateAndTime = $event->getDateAndTime();
            $now = new DateTime();
            if ($dateAndTime < $now) {
                $event->setPassed(true);
                $this->entityManager->getRepository(Event::class)->saveEvent($event);
            }
        }

        return $event;
    }

    public function getEventsForSociety(int $societyId): Collection
    {
        $society = $this->entityManager->getRepository(Society::class)->find($societyId);
        return $society->getEvents();
    }

    public function getOnGoingEventsForSocietyDisplay(int $societyId): ?array
    {
        $society = $this->entityManager->getRepository(Society::class)->find($societyId);
        $events = $this->getEventsForSociety($societyId);
        foreach ($events as $event) {
            $event = $this->checkIfPassed($event);
            if (!$event->isPassed()) {
                $attending = $this->getAttendeesForEventDisplay($event);
                $attendBool = $this->getUserAttendingEvent($_SESSION["user"]->getUsername(), $event);

                $comments = $this->getCommentsForEventDisplay($event);

                $creator = $this->getCreatorForEventDisplay($event);

                $editable = $this->getUserCanEditEvent($_SESSION["user"]->getUsername(), $event);

                $createdOn = $this->getEventCreatedOn($event);
                $date = $this->getEventDate($event);
                $time = $this->getEventTime($event);

                $eventsDisplay = [
                    "id" => $event->getId(),
                    "name" => $event->getName(),
                    "creator" => $creator,
                    "date" => $date,
                    "time" => $time,
                    "description" => $event->getDescription(),
                    "attending" => $attending,
                    "comments" => $comments,
                    "creation" => $createdOn,
                    "location" => $event->getLocation(),
                    "lat" => $event->getLat(),
                    "lon" => $event->getLon(),
                    "attendBool" => $attendBool,
                    "passed" => $this->checkIfEventPassed($event->getId()),
                    "editable" => $editable
                ];

                $eventsNew[] = $eventsDisplay;
            }
        }
        return $eventsNew;
    }

    public function getAttendeesForEventDisplay(Event $event): array
    {
        $attending = [];
        $attendBool = false;
        $attendees = $event->getAttendees();
        if (!$attendees->isEmpty()) {
            $attendBool = in_array($_SESSION['user'], (array)$attendees);

            foreach ($attendees as $attend) {
                $userDisplay = [
                    'name' => $attend->getName(),
                    'username' => $attend->getUsername(),
                    'photo' => $attend->getProfilePicture()
                ];
                $attending[] = $userDisplay;
            }
        }
        return $attending;
    }

    public function getUserAttendingEvent(string $username, Event $event): bool
    {
        $user = $this->entityManager->getRepository(User::class)->findUserByUsername($username);
        return $event->getAttendees()->contains($user);
    }

    public function getCommentsForEventDisplay(Event $event): array
    {
        $comments = [];
        $commentsEvent = $this->getCommentsForEvent($event);
        if (!empty($commentsEvent)) {
            foreach ($commentsEvent as $comment) {
                if (is_array($comment)) {
                    $comments[] = [
                        'photo' => $comment["photo"],
                        'username' => $comment["username"],
                        'body' => $comment["body"]
                    ];
                } else {
                    $comments[] = [
                        'photo' => $comment->photo,
                        'username' => $comment->username,
                        'body' => $comment->body
                    ];
                }
            }
        }
        return $comments;
    }

    public function getCommentsForEvent(Event $event): ?array
    {
        $dataService = new DataService();
        $cachingDataService = new CachingDataService($dataService, $this->client);

        return $cachingDataService->getCommentsForEvent($event->getId());
    }

    public function getCreatorForEventDisplay(Event $event): array
    {
        $creatorDb = $event->getCreator();

        $creator = [
            'photo' => $creatorDb->getProfilePicture(),
            'name' => $creatorDb->getName()
        ];
        return $creator;
    }

    public function getUserCanEditEvent(string $username, Event $event): bool
    {
        $user = $this->entityManager->getRepository(User::class)->findUserByUsername($username);
        return $event->getCreator() === $user;
    }

    public function getEventCreatedOn(Event $event): string
    {
        return $event->getCreatedOn()->format("m.d.Y H:i");
    }

    public function getEventDate(Event $event): string
    {
        return $event->getDateAndTime()->format("m.d.Y");
    }

    public function getEventTime(Event $event): string
    {
        return $event->getDateAndTime()->format("H:i");
    }

    public function getWeatherEvent(Event $event): string
    {
        return $event->getDateAndTime()->format("Y-m-d") . "T" . $event->getDateAndTime()->format("H") . ":00";
    }

    public function getPassedEventsForSocietyDisplay(int $societyId): ?array
    {
        $society = $this->entityManager->getRepository(Society::class)->find($societyId);
        $events = $this->getEventsForSociety($societyId);
        foreach ($events as $event) {
            $event = $this->checkIfPassed($event);
            if ($event->isPassed()) {
                $attending = $this->getAttendeesForEventDisplay($event);
                $attendBool = $this->getUserAttendingEvent($_SESSION["user"]->getUsername(), $event);
                $comments = $this->getCommentsForEventDisplay($event);
                $creator = $this->getCreatorForEventDisplay($event);
                $createdOn = $this->getEventCreatedOn($event);
                $date = $this->getEventDate($event);
                $time = $this->getEventTime($event);

                $eventsDisplay = [
                    "id" => $event->getId(),
                    "name" => $event->getName(),
                    "creator" => $creator,
                    "date" => $date,
                    "time" => $time,
                    "description" => $event->getDescription(),
                    "attending" => $attending,
                    "comments" => $comments,
                    "creation" => $createdOn,
                    "location" => $event->getLocation(),
                    "lat" => $event->getLat(),
                    "lon" => $event->getLon(),
                    "attendBool" => $attendBool,
                    "passed" => $this->checkIfEventPassed($event->getId())
                ];

                $eventsNew[] = $eventsDisplay;
            }
        }
        return $eventsNew;
    }

    private function checkIfEventPassed(int $eventId): bool
    {
        $event = $this->getEventById($eventId);
        if ($event->isPassed()) {
            return true;
        }

        return false;
    }

    public function validateEvent(object|array|null $data): Validation
    {
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

        return $validation;
    }

    /**
     * @throws \Exception
     */
    public function addEvent(object|array|null $data, int $creatorId, int $societyId): int
    {
        $user = $this->entityManager->getRepository(User::class)->find($creatorId);
        $society = $this->entityManager->getRepository(Society::class)->find($societyId);

        $event = new Event();
        $event->setName($data["name"]);
        $event->setPassed(false);
        $event->setSociety($society);
        $event->setCreator($user);
        $event->setCreatedOn();
        $event->setDescription($data["description"]);
        $event->setDateAndTime(new DateTime($data["event-time"]));
        $event->setLocation($data["_location"], $data["_lat"], $data["_lon"]);
        $this->entityManager->getRepository(Event::class)->saveEvent($event);
        return $event->getId();
    }

    public function attendEvent(int $userId, int $eventId, string $response): int
    {
        $user = $this->entityManager->getRepository(User::class)->find($userId);
        $event = $this->entityManager->getRepository(Event::class)->find($eventId);
        if ($response == "true") {
            $event->addAttendee($user);
        } else {
            $event->removeAttendee($user);
        }

        $this->entityManager->getRepository(Event::class)->saveEvent($event);

        return $event->getSociety()->getId();
    }

    public function deleteEvent(int $eventID): int
    {
        $event = $this->entityManager->getRepository(Event::class)->find($eventID);
        $societyId = $event->getSociety()->getId();

        $this->entityManager->getRepository(Event::class)->deleteEvent($event);

        return $societyId;
    }

    /**
     * @throws \Exception
     */
    public function updateEvent(object|array|null $data, int $idEvent): int
    {
        $event = $this->entityManager->getRepository(Event::class)->find($idEvent);
        $event->setName($data["name"]);
        $event->setDescription($data["description"]);
        $event->setDateAndTime(new DateTime($data["event-time"]));
        $event->setLocation($data["_location"], $data["_lat"], $data["_lon"]);

        $this->entityManager->getRepository(Event::class)->saveEvent($event);

        return $event->getSociety()->getId();
    }

    public function getEventById(int $id): Event
    {
        return $this->entityManager->getRepository(Event::class)->find($id);
    }

    public function getEventByIdForDisplay(int $id): array
    {
        $event = $this->getEventById($id);

        $eventDisplay = [
            "society" => $event->getSociety()->getId(),
            "name" => $event->getName(),
            "date_and_time" => $event->getDateAndTime()->format('Y-m-d\TH:i'),
            "lat" => $event->getLat(),
            "lon" => $event->getLon(),
            "location" => $event->getLocation(),
            "description" => $event->getDescription()
        ];

        return $eventDisplay;
    }
}