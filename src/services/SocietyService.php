<?php

namespace Services;

use Models\Link;
use Models\Society;
use Models\User;

interface SocietyService
{
    public function addNewSociety(string $name, string $desc, string $creatorUsername) : int;
    public function addBanner(int $societyId, string $banner);
    public function getSociety(int $societyId) : Society;
    public function leaveSociety(int $userId, int $societyId);
    public function enterSocietyByUri(string $username, string $uri);
    public function findSocietyByUri($uri) : ?Society;
    public function generateLinkForSocietyId($societyId) : ?Link;

    public function getMembersDisplay($societyId): array;
}