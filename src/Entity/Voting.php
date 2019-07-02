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
     * @ORM\OneToMany(targetEntity="App\Entity\Vote", mappedBy="voting", orphanRemoval=true, fetch="EAGER")
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

    /**
     * Voting constructor.
     */
    public function __construct()
    {
        $this->movies = new ArrayCollection();
        $this->votes = new ArrayCollection();
    }

    /**
     * @return int|null
     */
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

    /**
     * @param Movie $movie add movie to voting
     *
     * @return Voting
     */
    public function addMovie(Movie $movie): self
    {
        if (!$this->movies->contains($movie)) {
            $this->movies[] = $movie;
        }

        return $this;
    }

    /**
     * @param Movie $movie remove movie from voting
     *
     * @return Voting
     */
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

    /**
     * @param Vote $vote add vote to voting
     *
     * @return Voting
     */
    public function addVote(Vote $vote): self
    {
        if (!$this->votes->contains($vote)) {
            $this->votes[] = $vote;
            $vote->setVoting($this);
        }

        return $this;
    }

    /**
     * @param Vote $vote remove vote from voting
     *
     * @return Voting
     */
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

    /**
     * @return bool|null
     */
    public function getOpen(): ?bool
    {
        return $this->open;
    }

    /**
     * @param bool $open open or close
     *
     * @return Voting
     */
    public function setOpen(bool $open): self
    {
        $this->open = $open;

        return $this;
    }

    /**
     * @return MovieNight|null
     */
    public function getMovieNight(): ?MovieNight
    {
        return $this->movieNight;
    }

    /**
     * @param MovieNight|null $movieNight set relation to movienight
     *
     * @return Voting
     */
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
