<?php

namespace repositories;

use Doctrine\ORM\EntityRepository;

class UserRepository extends EntityRepository
{
    public function findUserByUsername(String $username)
    {
        return $this->findOneBy(['username'=>$username]);
    }
}