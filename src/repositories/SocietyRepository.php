<?php

namespace repositories;

use Doctrine\ORM\EntityRepository;
use models\Society;
use models\User;

class SocietyRepository extends EntityRepository
{
    public function saveSociety(Society $society): ?int
    {
        $entityManager = $this->getEntityManager();

        $entityManager->persist($society);
        $entityManager->flush();
        return $society->getId();
    }

    public function removeSociety($society): void
    {
        $entityManager = $this->getEntityManager();

        $entityManager->remove($society);
        $entityManager->flush();
    }
}