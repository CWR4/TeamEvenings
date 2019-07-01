<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;
use DateTimeInterface;

/**
 * @ORM\Entity(repositoryClass="App\Repository\MovieNightRepository")
 */
class MovieNight
{
    /**
     * @ORM\Id()
     * @ORM\GeneratedValue()
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\Column(type="date")
     */
    private $date;

    /**
     * @ORM\Column(type="time")
     */
    private $time;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $location;

    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\Movie", inversedBy="movieNights")
     */
    private $movie;

    /**
     * @ORM\OneToOne(targetEntity="App\Entity\Voting", inversedBy="movieNight", cascade={"persist", "remove"})
     */
    private $voting;

    /**
     * @return int|null
     */
    public function getId(): ?int
    {
        return $this->id;
    }

    /**
     * @return DateTimeInterface|null
     */
    public function getDate(): ?DateTimeInterface
    {
        return $this->date;
    }

    /**
     * @param DateTimeInterface $date set date of movienight
     *
     * @return MovieNight
     */
    public function setDate(DateTimeInterface $date): self
    {
        $this->date = $date;

        return $this;
    }

    /**
     * @return DateTimeInterface|null
     */
    public function getTime(): ?DateTimeInterface
    {
        return $this->time;
    }

    /**
     * @param DateTimeInterface $time set time of movienight
     *
     * @return MovieNight
     */
    public function setTime(DateTimeInterface $time): self
    {
        $this->time = $time;

        return $this;
    }

    /**
     * @return string|null
     */
    public function getLocation(): ?string
    {
        return $this->location;
    }

    /**
     * @param string $location set location of movienight
     *
     * @return MovieNight
     */
    public function setLocation(string $location): self
    {
        $this->location = $location;

        return $this;
    }

    /**
     * @return Movie|null
     */
    public function getMovie(): ?Movie
    {
        return $this->movie;
    }

    /**
     * @param Movie|null $movie set movie for movienight
     *
     * @return MovieNight
     */
    public function setMovie(?Movie $movie): self
    {
        $this->movie = $movie;

        return $this;
    }

    /**
     * @return Voting|null
     */
    public function getVoting(): ?Voting
    {
        return $this->voting;
    }

    /**
     * @param Voting|null $voting set voting for movienight
     *
     * @return MovieNight
     */
    public function setVoting(?Voting $voting): self
    {
        $this->voting = $voting;

        return $this;
    }
}
