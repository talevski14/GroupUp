<?php

namespace Repositories;

use Doctrine\ORM\EntityRepository;
use Models\Link;

class LinkRepository extends EntityRepository
{
    public function findLinkByUri(string $uri) : ?Link
    {
        return $this->findOneBy(["uri" => $uri]);
    }

    public function removeLink($link) : void
    {
        $entityManager = $this->getEntityManager();

        $entityManager->remove($link);
        $entityManager->flush();
    }

    public function getLinkBySociety($society) : ?Link
    {
        return $this->findOneBy(["society" => $society]);
    }

    public function saveLink($link): ?int
    {
        $entityManager = $this->getEntityManager();

        $entityManager->persist($link);
        $entityManager->flush();
        return $link->getId();
    }
}