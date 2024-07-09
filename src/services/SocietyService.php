<?php

namespace services;

use models\User;

interface SocietyService
{
    public function addNewSociety(string $name, string $desc, User $creator) : int;
    public function addBanner(int $societyId, string $banner);
}