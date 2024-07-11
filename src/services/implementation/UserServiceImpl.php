<?php

namespace Services\implementation;

use Doctrine\ORM\EntityManager;
use Doctrine\ORM\EntityManagerInterface;
use Models\User;
use Services\UserService;

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

    public function getUserByUsername(string $username): ?User
    {
        return $this->entityManager->getRepository(User::class)->findUserByUsername($username);
    }
}