<?php

namespace Repositories;

use Doctrine\ORM\EntityRepository;
use Models\Link;
use Models\Society;
use Models\User;

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

    public function findSocietyByLink(Link $link) : ?Society
    {
        return $link->getSociety();
    }
}