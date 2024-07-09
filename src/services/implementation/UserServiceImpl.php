<?php

namespace services\implementation;

use Doctrine\ORM\EntityManager;
use Doctrine\ORM\EntityManagerInterface;
use models\User;
use services\UserService;

class UserServiceImpl implements UserService
{
    private EntityManagerInterface $entityManager;

    /**
     * @param EntityManagerInterface $entityManager
     */
    public function __construct(EntityManagerInterface $entityManager)
    {
        $this->entityManager = $entityManager;
    }

    public function getUserByUsername(string $username): User
    {
        return $this->entityManager->getRepository(User::class)->findUserByUsername($username);
    }
}