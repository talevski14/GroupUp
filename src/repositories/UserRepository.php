<?php

namespace Repositories;

use Doctrine\ORM\EntityRepository;
use Models\User;

class UserRepository extends EntityRepository
{
    public function findUserByUsername(String $username): ?User
    {
        return $this->findOneBy(['username'=>$username]);
    }

    public function findUserByEmail(String $email): ?User
    {
        return $this->findOneBy(["email"=>$email]);
    }

    public function saveUser(User $user): void
    {
        $entityManager = $this->getEntityManager();

        $entityManager->persist($user);
        $entityManager->flush();
    }
}