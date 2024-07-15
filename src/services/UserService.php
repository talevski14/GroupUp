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

    public function deactivateAccount(string $username): void;

    public function activateAccount(string $username, string $password);
    public function getDataFromUser(User $user): array;

    public function updateUserAccount(String $username, array $data): string;

    public function validateNameAndUsername(object|array|null $data): Validation;

    public function validatePassword(object|array|null $data): Validation;

    public function editUserPassword(string $username, object|array|null $data);
}