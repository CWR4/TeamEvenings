<?php

namespace App\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass="App\Repository\VotingRepository")
 */
class Voting
{
    /**
     * @ORM\Id()
     * @ORM\GeneratedValue()
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\ManyToMany(targetEntity="App\Entity\Movie", inversedBy="votings")
     */
    private $movies;

    /**
     * @ORM\OneToMany(targetEntity="App\Entity\Vote", mappedBy="Voting", orphanRemoval=true, fetch="EAGER")
     */
    private $votes;

    /**
     * @ORM\Column(type="boolean")
     */
    private $open;

    /**
     * @ORM\OneToOne(targetEntity="App\Entity\MovieNight", mappedBy="voting", cascade={"persist", "remove"})
     */
    private $movieNight;

    public function __construct()
    {
        $this->movies = new ArrayCollection();
        $this->votes = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    /**
     * @return Collection|Movie[]
     */
    public function getMovies(): Collection
    {
        return $this->movies;
    }

    public function addMovie(Movie $movie): self
    {
        if (!$this->movies->contains($movie)) {
            $this->movies[] = $movie;
        }

        return $this;
    }

    public function removeMovie(Movie $movie): self
    {
        if ($this->movies->contains($movie)) {
            $this->movies->removeElement($movie);
        }

        return $this;
    }

    /**
     * @return Collection|Vote[]
     */
    public function getVotes(): Collection
    {
        return $this->votes;
    }

    public function addVote(Vote $vote): self
    {
        if (!$this->votes->contains($vote)) {
            $this->votes[] = $vote;
            $vote->setVoting($this);
        }

        return $this;
    }

    public function removeVote(Vote $vote): self
    {
        if ($this->votes->contains($vote)) {
            $this->votes->removeElement($vote);
            // set the owning side to null (unless already changed)
            if ($vote->getVoting() === $this) {
                $vote->setVoting(null);
            }
        }

        return $this;
    }

    public function getOpen(): ?bool
    {
        return $this->open;
    }

    public function setOpen(bool $open): self
    {
        $this->open = $open;

        return $this;
    }

    public function getMovieNight(): ?MovieNight
    {
        return $this->movieNight;
    }

    public function setMovieNight(?MovieNight $movieNight): self
    {
        $this->movieNight = $movieNight;

        // set (or unset) the owning side of the relation if necessary
        $newVoting = $movieNight === null ? null : $this;
        if ($newVoting !== $movieNight->getVoting()) {
            $movieNight->setVoting($newVoting);
        }

        return $this;
    }
}
