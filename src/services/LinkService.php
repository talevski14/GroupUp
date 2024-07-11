<?php

namespace Services;

use Models\Link;
use Rakit\Validation\Rules\Boolean;

interface LinkService
{
    public function checkIfValid($uri) : Boolean;

    public function removeLink($link): void;

    public function findLinkByUri($uri) : ?Link;
}