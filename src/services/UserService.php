<?php

namespace services;

interface UserService
{
    public function getUserByUsername(String $username);
}