<?php

namespace Services\implementation;

use Doctrine\ORM\EntityManagerInterface;
use Models\Comment;
use Models\Event;
use Models\User;
use Services\CommentService;

class CommentServiceImpl implements CommentService
{
    private EntityManagerInterface $entityManager;

    /**
     * @param EntityManagerInterface $entityManager
     */
    public function __construct(EntityManagerInterface $entityManager)
    {
        $this->entityManager = $entityManager;
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

        return $event->getSociety()->getId();
    }
}