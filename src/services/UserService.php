<?php

namespace Services;

use Models\User;
use Rakit\Validation\Validation;
use Rakit\Validation\Validator;

interface UserService
{
    public function getUserByUsername(string $username): ?User;

    public function createAccount(object|array|null $data): ?User;

    public function getUserByEmail(string $email): ?User;

    public function validateUserData(object|array|null $data): Validation;

    public function uploadPhoto(string $username): String;
}