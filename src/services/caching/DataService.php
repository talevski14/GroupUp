<?php

namespace Services\caching;

use Doctrine\ORM\EntityManagerInterface;
use Models\Comment;
use Models\Event;

class DataService implements DataServiceInterface
{
    private EntityManagerInterface $entityManager;

    public function __construct()
    {
        $this->entityManager = require __DIR__ . '/../../../config/doctrine.php';
    }

    public function getCommentsForEvent($id): ?array
    {
        $event = $this->entityManager->getRepository(Event::class)->find($id);
        $comments = $this->entityManager->getRepository(Comment::class)->getCommentsForEvent($event);
        $commentsDisplay = [];

        foreach ($comments as $comment) {
            $commentsDisplay[] = [
                "username" => $comment->getUser()->getUsername(),
                "photo" => $comment->getUser()->getProfilePicture(),
                "body" => $comment->getBody()
            ];
        }

        return $commentsDisplay;
    }
}