<?php

namespace services;

use models\Society;
use models\User;

interface SocietyService
{
    public function addNewSociety(string $name, string $desc, User $creator) : int;
    public function addBanner(int $societyId, string $banner);
    public function getSociety(int $societyId) : Society;
    public function leaveSociety(int $userId, int $societyId);
}