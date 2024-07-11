<?php

namespace Services\implementation;

use Doctrine\ORM\EntityManagerInterface;
use Services\EventService;

class EventServiceImpl implements EventService
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