<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass="App\Repository\VoteRepository")
 */
class Vote
{
    /**
     * @ORM\Id()
     * @ORM\GeneratedValue()
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\Movie")
     * @ORM\JoinColumn(nullable=false)
     */
    private $movie;

    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\User")
     * @ORM\JoinColumn(nullable=false)
     */
    private $user;

    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\MovieNight", inversedBy="votes")
     * @ORM\JoinColumn(nullable=false)
     */
    private $movieNight;

    /**
     * @return int|null
     */
    public function getId(): ?int
    {
        return $this->id;
    }

    /**
     * @return Movie
     */
    public function getMovie(): Movie
    {
        return $this->movie;
    }

    /**
     * @param Movie|null $movie set movie relation
     *
     * @return Vote
     */
    public function setMovie(?Movie $movie): self
    {
        $this->movie = $movie;

        return $this;
    }

    /**
     * @return User|null
     */
    public function getUser(): ?User
    {
        return $this->user;
    }

    /**
     * @param User|null $user set user
     *
     * @return Vote
     */
    public function setUser(?User $user): self
    {
        $this->user = $user;

        return $this;
    }

    public function getMovieNight(): ?MovieNight
    {
        return $this->movieNight;
    }

    public function setMovieNight(?MovieNight $movieNight): self
    {
        $this->movieNight = $movieNight;

        return $this;
    }
}
