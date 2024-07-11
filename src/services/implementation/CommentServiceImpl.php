<?php

namespace Services\implementation;

use Doctrine\ORM\EntityManagerInterface;
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
}