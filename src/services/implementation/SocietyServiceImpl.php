<?php

namespace Services\implementation;

use DateTime;
use Doctrine\ORM\EntityManagerInterface;
use Models\Event;
use Models\Link;
use Models\Society;
use Models\User;
use Services\SocietyService;

class SocietyServiceImpl implements SocietyService
{
    private EntityManagerInterface $entityManager;

    /**
     * @param EntityManagerInterface $entityManager
     */
    public function __construct(EntityManagerInterface $entityManager)
    {
        $this->entityManager = $entityManager;
    }

    public function addNewSociety(string $name, string $desc, string $creatorUsername): int
    {
        $creator = $this->entityManager->getRepository(User::class)->findUserByUsername($creatorUsername);
        $society = new Society();
        $society->setName($name);
        $society->setDescription($desc);
        $society->addMember($creator);

        $this->entityManager->getRepository(Society::class)->saveSociety($society);

        return $society->getId();
    }

    public function addBanner(int $societyId, string $banner): void
    {
        $society = $this->entityManager->getRepository(Society::class)->find($societyId);
        $society->setBanner($banner);
        $this->entityManager->getRepository(Society::class)->saveSociety($society);
    }

    public function getSociety(int $societyId) : Society
    {
        return $this->entityManager->getRepository(Society::class)->find($societyId);
    }

    public function leaveSociety(int $userId, int $societyId): void
    {
        $society = $this->getSociety($societyId);
        $user = $this->entityManager->getRepository(User::class)->find($userId);

        $society->removeMember($user);
        $this->entityManager->getRepository(Society::class)->saveSociety($society);
        $this->entityManager->getRepository(User::class)->saveUser($user);

        if($society->getMembers()->isEmpty()) {
            $this->entityManager->getRepository(Society::class)->removeSociety($society);
        } else {
            $eventsBySociety = $this->entityManager->getRepository(Event::class)->findEventsBySociety($society);
            $eventsByMember = $this->entityManager->getRepository(Event::class)->findEventsByMember($user);

            foreach ($eventsByMember as $event) {
                if($eventsBySociety->contains($event)){
                    $this->entityManager->getRepository(Event::class)->deleteEvent($event);
                }
            }
        }
    }

    public function enterSocietyByUri(string $username, string $uri) : void
    {
        $user = $this->entityManager->getRepository(User::class)->findUserByUsername($username);

        $link = $this->entityManager->getRepository(Link::class)->findLinkByUri($uri);
        $society = $this->entityManager->getRepository(Society::class)->findSocietyByLink($link);

        $society->addMember($user);
        $this->entityManager->getRepository(Society::class)->saveSociety($society);
    }

    public function findSocietyByUri($uri) : ?Society {
        $link = $this->entityManager->getRepository(Link::class)->findLinkByUri($uri);
        $society = $this->entityManager->getRepository(Society::class)->findSocietyByLink($link);
        return $society;
    }

    public function generateLinkForSocietyId($societyId): ?Link
    {
        $society = $this->entityManager->getRepository(Society::class)->find($societyId);
        $link = $this->entityManager->getRepository(Link::class)->getLinkBySociety($society);
        require __DIR__ . "/../../core/functions.php";
        $now = new DateTime();

        if($link === null || $link->getDateExpires() < $now) {
            if($link !== null) {
                $this->entityManager->getRepository(Link::class)->removeLink($link);
            }
            $newUri = guidv4();
            $newLink = new Link();
            $newLink->setDateCreated();
            $newLink->setUri($newUri);
            $newLink->setSociety($society);
            $this->entityManager->getRepository(Link::class)->saveLink($newLink);
        } else {
            $newLink = $link;
        }
        return $newLink;
    }

    public function getMembersDisplay($societyId) : array
    {
        $society = $this->getSociety($societyId);

        $membersObj = $society->getMembers();
        $members = [];
        foreach ($membersObj as $member) {
            $memberDisplay = [
                'name' => $member->getName(),
                'username' => $member->getUsername(),
                'photo' => $member->getProfilePicture()
            ];
            $members[] = $memberDisplay;
        }
        return $members;
    }
}