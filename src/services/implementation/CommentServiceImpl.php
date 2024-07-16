<?php

namespace Services\implementation;

use Doctrine\ORM\EntityManagerInterface;
use Models\Comment;
use Models\Event;
use Models\User;
use Predis\Client;
use Services\CommentService;

class CommentServiceImpl implements CommentService
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

    public function addComment(object|array|null $data, int $userId): int
    {
        $event = $this->entityManager->getRepository(Event::class)->find($data["_id"]);
        $user = $this->entityManager->getRepository(User::class)->find($userId);

        $comment = new Comment();
        $comment->setEvent($event);
        $comment->setBody($data["body"]);
        $comment->setUser($user);

        $this->entityManager->getRepository(Comment::class)->saveComment($comment);
        $this->addToCache($event->getId());

        return $event->getSociety()->getId();
    }

    public function addToCache(int $eventId): void
    {
        $key = "event_{$eventId}_comments";
        $this->client -> del([$key]);

        $event = $this->entityManager->getRepository(Event::class)->find($eventId);
        $comments = $this->entityManager->getRepository(Comment::class)->getCommentsForEvent($event);
        $commentsDisplay = [];


        foreach ($comments as $comment) {
            $commentsDisplay[] = [
                "username" => $comment->getUser()->getUsername(),
                "photo" => $comment->getUser()->getProfilePicture(),
                "body" => $comment->getBody()
            ];
        }

        $this->client->set($key, json_encode($commentsDisplay));
        $this->client->expire($key, 600);
    }
}