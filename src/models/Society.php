<?php

namespace Models;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;

use Doctrine\ORM\Mapping as ORM;
use Repositories\SocietyRepository;

#[ORM\Entity (repositoryClass: SocietyRepository::class)]
#[ORM\Table(name: 'societies')]
class Society
{
    #[ORM\Id]
    #[ORM\Column(type: 'integer')]
    #[ORM\GeneratedValue]
    private int|null $id;
    #[ORM\Column(type: 'string', length: 100)]
    private string $name;
    #[ORM\Column(type: 'string', length: 500, nullable: true, options: ["default" => ""])]
    private string $description;
    #[ORM\Column(type: 'string', length: 200, nullable: true, options: ["default" => "/images/society/banner.jpg"])]
    private string $banner;

    /** @var Collection<int, Link> */
    #[ORM\OneToMany(targetEntity: Link::class, mappedBy: "society", cascade: ['persist', 'remove'])]
    private Collection $links;

    /** @var Collection<int, Event> */
    #[ORM\OneToMany(targetEntity: Event::class, mappedBy: "society", cascade: ['persist', 'remove'])]
    private Collection $events;

    /** @var Collection<int, User> */
    #[ORM\ManyToMany(targetEntity: User::class, inversedBy: "society")]
    #[ORM\JoinTable(name: "members")]
    private Collection $members;

    public function __construct()
    {
        $this->links = new ArrayCollection();
        $this->events = new ArrayCollection();
        $this->members = new ArrayCollection();
        $this->banner = "/images/society/banner.jpg";
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
     * @return string
     */
    public function getBanner(): string
    {
        return $this->banner;
    }

    /**
     * @param string $banner
     */
    public function setBanner(string $banner): void
    {
        $this->banner = $banner;
    }

    /**
     * @return Collection
     */
    public function getLinks(): Collection
    {
        return $this->links;
    }

    /**
     * @param Link $link
     */
    public function addLink(Link $link): void
    {
        $this->links->add($link);
    }

    /**
     * @return Collection
     */
    public function getEvents(): Collection
    {
        return $this->events;
    }

    /**
     * @param Event $event
     */
    public function addEvent(Event $event): void
    {
        $this->events->add($event);
    }

    /**
     * @return Collection
     */
    public function getMembers(): Collection
    {
        return $this->members;
    }

    /**
     * @param User $member
     */
    public function addMember(User $member): void
    {
        $this->members->add($member);
        $member->enterSociety($this);
    }

    public function removeMember(User $member): void
    {
        $member->leaveSociety($this);
        $this->members->removeElement($member);
    }
}