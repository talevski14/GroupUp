<?php

namespace Services\implementation;

use Doctrine\ORM\EntityManager;
use Doctrine\ORM\EntityManagerInterface;
use exceptions\EmailAlreadyExists;
use exceptions\NewPasswordSameAsOldException;
use exceptions\UsernameAlreadyExists;
use exceptions\WrongPasswordException;
use exceptions\WrongRepeatedPasswordException;
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
        $name = $data["name"];
        $email = $data["email"];
        $username = $data["username"];
        $password = $data["password"];

        $usernameExists = $this->getUserByUsername($username);
        if ($usernameExists !== null) {
            throw new UsernameAlreadyExists();
        }

        $emailExists = $this->getUserByEmail($email);
        if ($emailExists !== null) {
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
        $folder = __DIR__ . "/../../../public/images/account/" . $user->getUserId() . ".jpg";
        $user->setProfilePicture("/images/account/" . $user->getUserId() . ".jpg");
        $this->entityManager->getRepository(User::class)->saveUser($user);

        return $folder;
    }

    public function deactivateAccount(string $username): void
    {
        $user = $this->getUserByUsername($username);
        $user->setActive(false);
        $this->entityManager->getRepository(User::class)->saveUser($user);
    }

    /**
     * @throws WrongPasswordException
     */
    public function activateAccount(string $username, string $password)
    {
        $user = $this->getUserByUsername($username);
        if (password_verify($password, $user->getHashedPassword())) {
            $user->setActive(true);
        } else {
            throw new WrongPasswordException();
        }
    }

    public function getDataFromUser(User $user): array
    {
        return [
            "username" => $user->getUsername(),
            "name" => $user->getName(),
            "email" => $user->getEmail()
        ];
    }

    /**
     * @throws UsernameAlreadyExists
     */
    public function updateUserAccount(string $username, array $data): string
    {
        $user = $this->getUserByUsername($username);
        if ($data["username"] !== $user->getUsername()) {
            $userExists = $this->getUserByUsername($data["username"]) !== null;
            if ($userExists) {
                throw new UsernameAlreadyExists();
            } else {
                $user->setUsername($data["username"]);
                $this->entityManager->getRepository(User::class)->saveUser($user);
            }
        }

        if ($data["name"] !== $user->getName()) {
            $user->setName($data["name"]);
            $this->entityManager->getRepository(User::class)->saveUser($user);
        }

        return $data["username"];
    }

    public function validateNameAndUsername(object|array|null $data): Validation
    {
        $validator = new Validator;

        $validation = $validator->make($data, [
            'name' => 'required',
            'username' => 'required|min:6'
        ]);

        $validation->validate();

        return $validation;
    }

    public function validatePassword(object|array|null $data): Validation
    {
        $validator = new Validator;

        $validation = $validator->make($data, [
            'oldpassword' => 'required',
            'newpassword' => 'required|min:6',
            'repeatpassword' => 'required'
        ]);

        $validation->setMessages([
            'required' => 'This field is required.',
            'newpassword' => "The new password should be minimum 6 characters."
        ]);

        $validation->validate();

        return $validation;
    }

    /**
     * @throws WrongPasswordException
     * @throws WrongRepeatedPasswordException
     * @throws NewPasswordSameAsOldException
     */
    public function editUserPassword(string $username, object|array|null $data)
    {
        $user = $this->getUserByUsername($username);

        $oldPass = $data['oldpassword'];
        $newPass = $data['newpassword'];
        $repeatPass = $data['repeatpassword'];

        if (!password_verify($oldPass, $user->getHashedPassword())) {
            throw new WrongPasswordException();
        }

        if($newPass !== $repeatPass) {
            throw new WrongRepeatedPasswordException();
        }

        if($oldPass === $newPass) {
            throw new NewPasswordSameAsOldException();
        }

        $user->setPasswordHash(password_hash($newPass, PASSWORD_BCRYPT));
        $this->entityManager->getRepository(User::class)->saveUser($user);
    }
}