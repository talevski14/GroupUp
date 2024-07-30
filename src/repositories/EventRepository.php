<?php

namespace Repositories;

use DateTime;
use Doctrine\ORM\EntityRepository;

class EventRepository extends EntityRepository
{
    public function findEventsByMember($user) : ?array
    {
        return $this->findBy(["creator"=>$user]);
    }

    public function findEventsBySociety($society) : ?array
    {
        return $this->findBy(["society"=>$society]);
    }

    public function deleteEvent($event) : void
    {
        $entityManager = $this->getEntityManager();

        $entityManager->remove($event);
        $entityManager->flush();
    }

    public function saveEvent($event) : void
    {
        $entityManager = $this->getEntityManager();

        $entityManager->persist($event);
        $entityManager->flush();
    }

    public function getEventsByDate(String $date): ?array
    {
        $eventsReturn = [];
        $events = $this->findAll();
        foreach ($events as $event) {
            $eventDate = $event->getDateAndTime()->format("Y-m-d");
            if($eventDate == $date)
            {
                $eventsReturn[] = $event;
            }
        }
        return $eventsReturn;
    }
}