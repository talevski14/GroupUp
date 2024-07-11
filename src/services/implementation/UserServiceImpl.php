<?php

namespace Services\implementation;

use Doctrine\ORM\EntityManager;
use Doctrine\ORM\EntityManagerInterface;
use exceptions\EmailAlreadyExists;
use exceptions\UsernameAlreadyExists;
use Models\User;
use Rakit\Validation\Validation;
use Rakit\Validation\Validator;
use Services\UserService;

class UserServiceImpl implements UserService
{
    private EntityManagerInterface $entityManager;

    /**
     * @param EntityManagerInterface $entityManager
     */
    public function __construct(EntityManagerInterface $entityManager)
    {
        $this->entityManager = $entityManager;
    }

    public function getUserByUsername(string $username): ?User
    {
        return $this->entityManager->getRepository(User::class)->findUserByUsername($username);
    }

    public function getUserByEmail(string $email): ?User
    {
        return $this->entityManager->getRepository(User::class)->findUserByEmail($email);
    }

    /**
     * @throws UsernameAlreadyExists
     * @throws EmailAlreadyExists
     */
    public function createAccount(object|array|null $data): ?User
    {
        // TODO: Implement createAccount() method.
        $name = $data["name"];
        $email = $data["email"];
        $username = $data["username"];
        $password = $data["password"];

        $usernameExists = $this->getUserByUsername($username);
        if($usernameExists !== null) {
            throw new UsernameAlreadyExists();
        }

        $emailExists = $this->getUserByEmail($email);
        if($emailExists !== null) {
            throw new EmailAlreadyExists();
        }

        $user = new User();
        $user->setName($name);
        $user->setEmail($email);
        $user->setUsername($username);
        $user->setPasswordHash(password_hash($password, PASSWORD_BCRYPT));

        $this->entityManager->getRepository(User::class)->saveUser($user);

        return $user;
    }

    public function validateUserData(object|array|null $data): Validation
    {
        $validator = new Validator;

        $validation = $validator->make($data, [
            'name' => 'required',
            'email' => 'required|email',
            'password' => 'required|min:6',
            'username' => 'required|min:6'
        ]);

        $validation->validate();

        return $validation;
    }

    public function uploadPhoto(string $username): string
    {
        $user = $this->getUserByUsername($username);
        $folder =__DIR__ . "/../../../public/images/account/" . $user->getUserId() . ".jpg";
        $user->setProfilePicture("/images/account/" . $user->getUserId() . ".jpg");
        $this->entityManager->getRepository(User::class)->saveUser($user);

        return $folder;
    }
}