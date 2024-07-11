<?php

namespace Services;

interface UserService
{
    public function getUserByUsername(String $username);
}