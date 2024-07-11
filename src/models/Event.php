<?php

namespace Models;

use DateTime;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;

use Doctrine\ORM\Mapping as ORM;
use Repositories\EventRepository;
use Services\EventService;

#[ORM\Entity (repositoryClass: EventRepository::class)]
#[ORM\Table(name: 'events')]
class Event
{
    #[ORM\Id]
    #[ORM\Column(type: 'integer')]
    #[ORM\GeneratedValue]
    private int|null $id;
    #[ORM\Column(type: 'string', length: 100)]
    private string $name;
    #[ORM\Column(type: 'string', length: 500)]
    private string $description;
    #[ORM\Column(type: 'datetime')]
    private DateTime $createdOn;
    #[ORM\Column(type: 'float')]
    private float $lat;
    #[ORM\Column(type: 'float')]
    private float $lon;
    #[ORM\Column(type: 'string', length: 300, nullable: true, options: ["default"=>""])]
    private string $location;
    #[ORM\Column(type: 'datetime')]
    private DateTime $dateAndTime;
    #[ORM\Column(type: 'boolean', nullable: true, options: ["default"=>false])]
    private bool $passed;

    #[ORM\ManyToOne(targetEntity: Society::class, inversedBy: "events")]
    private Society $society;

    /** @var Collection<int, Comment> */
    #[ORM\OneToMany(targetEntity: Comment::class, mappedBy: "event")]
    private Collection $comments;
    #[ORM\ManyToOne(targetEntity: User::class, inversedBy: "eventsCreated")]
    private User $creator;

    /** @var Collection<int, User> */
    #[ORM\ManyToMany(targetEntity: User::class, mappedBy: "eventsAttended")]
    private Collection $attendees;

    public function __construct()
    {
        $this->comments = new ArrayCollection();
        $this->attendees = new ArrayCollection();
    }

    /**
     * @return int|null
     */
    public function getId(): ?int
    {
        return $this->id;
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
    public function getDescription(): string
    {
        return $this->description;
    }

    /**
     * @param string $description
     */
    public function setDescription(string $description): void
    {
        $this->description = $description;
    }

    /**
     * @return DateTime
     */
    public function getCreatedOn(): DateTime
    {
        return $this->createdOn;
    }

    public function setCreatedOn(): void
    {
        $this->createdOn = new DateTime("now");
    }

    /**
     * @return float
     */
    public function getLat(): float
    {
        return $this->lat;
    }

    /**
     * @return float
     */
    public function getLon(): float
    {
        return $this->lon;
    }

    /**
     * @return string
     */
    public function getLocation(): string
    {
        return $this->location;
    }

    /**
     * @param string $location
     * @param float $lat
     * @param float $lon
     */
    public function setLocation(string $location, float $lat, float $lon): void
    {
        $this->location = $location;
        $this->lat = $lat;
        $this->lon = $lon;
    }

    /**
     * @return DateTime
     */
    public function getDateAndTime(): DateTime
    {
        return $this->dateAndTime;
    }

    /**
     * @param DateTime $dateAndTime
     */
    public function setDateAndTime(DateTime $dateAndTime): void
    {
        $this->dateAndTime = $dateAndTime;
    }

    /**
     * @return bool
     */
    public function isPassed(): bool
    {
        return $this->passed;
    }

    /**
     * @param bool $passed
     */
    public function setPassed(bool $passed): void
    {
        $this->passed = $passed;
    }

    /**
     * @return Society
     */
    public function getSociety(): Society
    {
        return $this->society;
    }

    /**
     * @param Society $society
     */
    public function setSociety(Society $society): void
    {
        $this->society = $society;
        $society->addEvent($this);
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
     * @return User
     */
    public function getCreator(): User
    {
        return $this->creator;
    }

    /**
     * @param User $creator
     */
    public function setCreator(User $creator): void
    {
        $this->creator = $creator;
        $creator->createEvent($this);
    }

    /**
     * @return Collection
     */
    public function getAttendees(): Collection
    {
        return $this->attendees;
    }

    /**
     * @param User $attendee
     * @return Event
     */
    public function addAttendee(User $attendee): Event
    {
        if (!$this->attendees->contains($attendee)) {
            $this->attendees->add($attendee);
            $attendee->attendEvent($this);
        }

        return $this;
    }


}