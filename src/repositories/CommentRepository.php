<?php

namespace Repositories;

use Doctrine\ORM\EntityRepository;
use Models\Comment;

class CommentRepository extends EntityRepository
{
    public function saveComment(Comment $comment)
    {
        $entityManager = $this->getEntityManager();

        $entityManager->persist($comment);
        $entityManager->flush();
    }

}