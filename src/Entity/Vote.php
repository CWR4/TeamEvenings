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
     * @ORM\ManyToOne(targetEntity="App\Entity\Voting", inversedBy="votes")
     * @ORM\JoinColumn(nullable=false)
     */
    private $voting;

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
     * @return int|null
     */
    public function getId(): ?int
    {
        return $this->id;
    }

    /**
     * @return Voting|null
     */
    public function getVoting(): ?Voting
    {
        return $this->voting;
    }

    /**
     * @param Voting|null $voting set relation to voting
     *
     * @return Vote
     */
    public function setVoting(?Voting $voting): self
    {
        $this->voting = $voting;

        return $this;
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
}
