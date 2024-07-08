<?php

namespace models;

use DateTime;

use Doctrine\ORM\Mapping as ORM;
use Doctrine\Common\Collections\Collection;

#[ORM\Entity]
#[ORM\Table(name: 'links')]
class Link
{
    #[ORM\Id]
    #[ORM\Column(type: 'integer')]
    #[ORM\GeneratedValue]
    private int|null $id;
    #[ORM\Column(type: 'string', length: 100)]
    private string $uri;
    #[ORM\Column(type: 'datetime')]
    private DateTime $date_created;
    #[ORM\Column(type: 'datetime')]
    private DateTime $date_expires;

    #[ORM\ManyToOne(targetEntity: Society::class, inversedBy: "links")]
    private Society $society;

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
    public function getUri(): string
    {
        return $this->uri;
    }

    /**
     * @param string $uri
     */
    public function setUri(string $uri): void
    {
        $this->uri = $uri;
    }

    /**
     * @return DateTime
     */
    public function getDateCreated(): DateTime
    {
        return $this->date_created;
    }

    public function setDateCreated(): void
    {
        $this->date_created = new DateTime("now");
        $this->date_expires = new DateTime("now");
        $this->date_expires->modify("+1 day");
    }

    /**
     * @return DateTime
     */
    public function getDateExpires(): DateTime
    {
        return $this->date_expires;
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
        $society->addLink($this);
    }

}