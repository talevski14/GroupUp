<?php

namespace Services\implementation;

use DateTime;
use Doctrine\ORM\EntityManagerInterface;
use Models\Link;
use Rakit\Validation\Rules\Boolean;
use Services\LinkService;

class LinkServiceImpl implements LinkService
{
    private EntityManagerInterface $entityManager;

    /**
     * @param EntityManagerInterface $entityManager
     */
    public function __construct(EntityManagerInterface $entityManager)
    {
        $this->entityManager = $entityManager;
    }

    public function checkIfValid($uri): bool
    {
        $link = $this->entityManager->getRepository(Link::class)->findLinkByUri($uri);
        $expirationDate = $link->getDateExpires();
        $now = new DateTime();

        if ($now < $expirationDate) {
            return true;
        }

        return false;
    }

    public function removeLink($link) : void
    {
        $this->entityManager->getRepository(Link::class)->removeLink($link);
    }

    public function findLinkByUri($uri) : ?Link
    {
        return $this->entityManager->getRepository(Link::class)->findLinkByUri($uri);
    }
}