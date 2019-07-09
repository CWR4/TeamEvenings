<?php

namespace App\Entity;

use DateTimeInterface;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

use Symfony\Component\Validator\Constraints as Assert;

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
     * @ORM\Column(type="string", length=255)
     *
     * @Assert\NotBlank()
     */
    private $location;

    /**
     * @ORM\OneToMany(targetEntity="App\Entity\Vote", mappedBy="movieNight", orphanRemoval=true)
     */
    private $votes;

    /**
     * @ORM\ManyToMany(targetEntity="App\Entity\Movie", mappedBy="movieNights", cascade={"persist"})
     */
    private $movies;

    /**
     * @ORM\Column(type="datetime")
     *
     * @Assert\GreaterThanOrEqual("now")
     */
    private $dateAndTime;

    /**
     * MovieNight constructor.
     */
    public function __construct()
    {
        $this->votes = new ArrayCollection();
        $this->movies = new ArrayCollection();
    }

    /**
     * @return int|null
     */
    public function getId(): ?int
    {
        return $this->id;
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
     * @return Collection|Vote[]
     */
    public function getVotes(): Collection
    {
        return $this->votes;
    }

    /**
     * @param Vote $vote add to movie night
     *
     * @return MovieNight
     */
    public function addVote(Vote $vote): self
    {
        if (!$this->votes->contains($vote)) {
            $this->votes[] = $vote;
            $vote->setMovieNight($this);
        }

        return $this;
    }

    /**
     * @param Vote $vote remove from movie night
     *
     * @return MovieNight
     */
    public function removeVote(Vote $vote): self
    {
        if ($this->votes->contains($vote)) {
            $this->votes->removeElement($vote);
            // set the owning side to null (unless already changed)
            if ($vote->getMovieNight() === $this) {
                $vote->setMovieNight(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection|Movie[]
     */
    public function getMovies(): Collection
    {
        return $this->movies;
    }

    /**
     * @param Movie $movie movie
     *
     * @return MovieNight
     */
    public function addMovie(Movie $movie): self
    {
        if (!$this->movies->contains($movie)) {
            $this->movies[] = $movie;
            $movie->addMovieNight($this);
        }

        return $this;
    }

    /**
     * @param Movie $movie movie
     *
     * @return MovieNight
     */
    public function removeMovie(Movie $movie): self
    {
        if ($this->movies->contains($movie)) {
            $this->movies->removeElement($movie);
            $movie->removeMovieNight($this);
        }

        return $this;
    }

    /**
     * @return Movie|null
     */
    public function getVotedMovie(): ?Movie
    {
        $votes = [];

        foreach ($this->getMovies() as $movie) {
            $votes[$movie->getId()] = 0;
        }

        foreach ($this->getVotes() as $vote) {
            ++$votes[$vote->getMovie()->getId()];
        }

        if ($votes) {
            foreach ($this->getMovies() as $movie) {
                if ($movie->getId() === array_keys($votes, max($votes))[0]) {
                    return $movie;
                }
            }
        }

        return null;
    }

    /**
     * @return DateTimeInterface|null
     */
    public function getDateAndTime(): ?DateTimeInterface
    {
        return $this->dateAndTime;
    }

    /**
     * @param DateTimeInterface $dateAndTime value to set
     *
     * @return MovieNight
     */
    public function setDateAndTime(DateTimeInterface $dateAndTime): self
    {
        $this->dateAndTime = $dateAndTime;

        return $this;
    }
}
