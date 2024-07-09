<?php

namespace services\implementation;

use Doctrine\ORM\EntityManagerInterface;
use models\Society;
use models\User;
use services\SocietyService;

class SocietyServiceImpl implements SocietyService
{
    private EntityManagerInterface $entityManager;

    /**
     * @param EntityManagerInterface $entityManager
     */
    public function __construct(EntityManagerInterface $entityManager)
    {
        $this->entityManager = $entityManager;
    }

    public function addNewSociety(string $name, string $desc, User $creator): int
    {
        $society = new Society();
        $society->setName($name);
        $society->setDescription($desc);
        $society->addMember($creator);

        $this->entityManager->getRepository(Society::class)->saveSociety($society);
        return $society->getId();
    }

    public function addBanner(int $societyId, string $banner): void
    {
        $society = $this->entityManager->getRepository(Society::class)->find($societyId);
        $society->setBanner($banner);
        $this->entityManager->getRepository(Society::class)->saveSociety($society);
    }
}