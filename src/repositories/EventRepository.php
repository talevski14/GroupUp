<?php

namespace Repositories;

use Doctrine\ORM\EntityRepository;

class EventRepository extends EntityRepository
{
    public function findEventsByMember($user) : array
    {
        return $this->findBy(["creator"=>$user]);
    }

    public function findEventsBySociety($society) : array
    {
        return $this->findBy(["society"=>$society]);
    }

    public function deleteEvent($event) : void
    {
        $entityManager = $this->getEntityManager();

        $entityManager->remove($event);
        $entityManager->flush();
    }
}