<?php

namespace Models;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;

use Doctrine\ORM\Mapping as ORM;
use Repositories\UserRepository;

#[ORM\Entity(repositoryClass: UserRepository::class)]
#[ORM\Table(name: 'users')]
class User
{
    #[ORM\Id]
    #[ORM\Column(type: 'integer')]
    #[ORM\GeneratedValue]
    private int|null $id;
    #[ORM\Column(type: 'string', length: 50)]
    private string $username;
    #[ORM\Column(type: 'string', length: 50)]
    private string $name;
    #[ORM\Column(type: 'string', length: 50)]
    private string $email;
    #[ORM\Column(type: 'string', length: 1000)]
    private string $password;
    #[ORM\Column(type: 'string', length: 1000, nullable: true, options: ["default"=>"/images/account/default.jpg"])]
    private string $profilePicture;
    #[ORM\Column(type: 'boolean', nullable: true, options: ["default"=>true])]
    private bool $active;

    /** @var Collection<int, Comment> */
    #[ORM\OneToMany(targetEntity: Comment::class, mappedBy: 'user')]
    private Collection $comments;

    /** @var Collection<int, Event> */
    #[ORM\ManyToMany(targetEntity: Event::class, inversedBy: 'attendees')]
    #[ORM\JoinTable(name: "attendees")]
    private Collection $eventsAttended;

    /** @var Collection<int, Event> */
    #[ORM\OneToMany(targetEntity: Event::class, mappedBy: 'creator')]
    private Collection $eventsCreated;

    /** @var Collection<int, User> */
    #[ORM\ManyToMany(targetEntity: Society::class, inversedBy: 'members')]
    #[ORM\JoinTable(name: "members")]
    private Collection $societies;

    public function __construct()
    {
        $this->comments = new ArrayCollection();
        $this->eventsAttended = new ArrayCollection();
        $this->eventsCreated = new ArrayCollection();
        $this->societies = new ArrayCollection();
        $this->profilePicture = "/images/account/default.jpg";
        $this->active = true;
    }

    /**
     * @return int|null
     */
    public function getUserId(): ?int
    {
        return $this->id;
    }

    /**
     * @return string
     */
    public function getUsername(): string
    {
        return $this->username;
    }

    /**
     * @param string $username
     */
    public function setUsername(string $username): void
    {
        $this->username = $username;
    }

    /**
     * @return string
     */
    public function getName(): string
    {
        return $this->name;
    }

    /**
     * @param string $name
     */
    public function setName(string $name): void
    {
        $this->name = $name;
    }

    /**
     * @return string
     */
    public function getEmail(): string
    {
        return $this->email;
    }

    /**
     * @param string $email
     */
    public function setEmail(string $email): void
    {
        $this->email = $email;
    }

    /**
     * @return string
     */
    public function getHashedPassword(): string
    {
        return $this->password;
    }

    /**
     * @param string $password
     */
    public function setPasswordHash(string $password): void
    {
        $this->password = password_hash($password, PASSWORD_BCRYPT);
    }

    /**
     * @return string
     */
    public function getProfilePicture(): string
    {
        return $this->profilePicture;
    }

    /**
     * @param string $profilePicture
     */
    public function setProfilePicture(string $profilePicture): void
    {
        $this->profilePicture = $profilePicture;
    }

    /**
     * @return bool
     */
    public function isActive(): bool
    {
        return $this->active;
    }

    /**
     * @param bool $active
     */
    public function setActive(bool $active): void
    {
        $this->active = $active;
    }

    /**
     * @return Collection
     */
    public function getComments(): Collection
    {
        return $this->comments;
    }

    /**
     * @param Comment $comment
     */
    public function addComment(Comment $comment): void
    {
        $this->comments->add($comment);
    }

    /**
     * @return Collection
     */
    public function getEventsAttended(): Collection
    {
        return $this->eventsAttended;
    }

    /**
     * @param Event $event
     * @return User
     */
    public function attendEvent(Event $event): User
    {
        if (!$this->eventsAttended->contains($event)) {
            $this->eventsAttended->add($event);
            $event->addAttendee($this);
        }

        return $this;
    }

    /**
     * @return Collection
     */
    public function getEventsCreated(): Collection
    {
        return $this->eventsCreated;
    }

    /**
     * @param Event $event
     */
    public function createEvent(Event $event): void
    {
        $this->eventsCreated->add($event);
    }

    /**
     * @param Society $society
     * @return User
     */
    public function enterSociety(Society $society): User
    {
        if(!$this->societies->contains($society)) {
            $this->societies->add($society);
            $society->addMember($this);
        }

        return $this;
    }

    /**
     * @return Collection
     */
    public function getSocieties(): Collection
    {
        return $this->societies;
    }

    public function leaveSociety(Society $society) : User
    {
        if($this->societies->contains($society)) {
            $this->societies->remove($society);
            $society->removeMember($this);
        }

        return $this;
    }

}