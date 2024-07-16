<?php

namespace Repositories;

use Doctrine\ORM\EntityRepository;
use Models\Comment;
use Models\Event;

class CommentRepository extends EntityRepository
{
    public function saveComment(Comment $comment): void
    {
        $entityManager = $this->getEntityManager();

        $entityManager->persist($comment);
        $entityManager->flush();
    }

    public function getCommentsForEvent(Event $event): ?array
    {
        return $this->findBy(["event" => $event]);
    }

}