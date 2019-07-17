<?php

namespace App\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass="App\Repository\MoviesRepository")
 */
class Movie
{
    /**
     * @ORM\Id()
     * @ORM\GeneratedValue()
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $title;

    /**
     * @ORM\Column(type="string", length=255, unique=true)
     */
    private $imdbID;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $year;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $runtime;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $poster;

    /**
     * @ORM\Column(type="string", length=1000, nullable=true)
     */
    private $plot;

    /**
     * @ORM\ManyToMany(targetEntity="App\Entity\MovieNight", inversedBy="movies", cascade={"persist"})
     */
    private $movieNights;

    /**
     * Movie constructor.
     */
    public function __construct()
    {
        $this->movieNights = new ArrayCollection();
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
    public function getTitle(): ?string
    {
        return $this->title;
    }

    /**
     * @param string $title title of movie
     *
     * @return Movie
     */
    public function setTitle(string $title): self
    {
        $this->title = $title;

        return $this;
    }

    /**
     * @return string|null
     */
    public function getImdbID(): ?string
    {
        return $this->imdbID;
    }

    /**
     * @param string $imdbID Id in api database
     *
     * @return Movie
     */
    public function setImdbID(string $imdbID): self
    {
        $this->imdbID = $imdbID;

        return $this;
    }

    /**
     * @return string|null
     */
    public function getYear(): ?string
    {
        return $this->year;
    }

    /**
     * @param string $year Year of publishing
     *
     * @return Movie
     */
    public function setYear(string $year): self
    {
        $this->year = $year;

        return $this;
    }

    /**
     * @return string|null
     */
    public function getRuntime(): ?string
    {
        return $this->runtime;
    }

    /**
     * @param string|null $runtime movie duration
     *
     * @return Movie
     */
    public function setRuntime(?string $runtime): self
    {
        $this->runtime = $runtime;

        return $this;
    }

    /**
     * @return mixed
     */
    public function getPoster()
    {
        return $this->poster;
    }

    /**
     * @param mixed $poster hyperlink to poster
     *
     * @return Movie
     */
    public function setPoster($poster): self
    {
        $this->poster = $poster;

        return $this;
    }

    /**
     * @return string|null
     */
    public function getPlot(): ?string
    {
        return $this->plot;
    }

    /**
     * @param string|null $plot movie plot
     *
     * @return Movie
     */
    public function setPlot(?string $plot): self
    {
        $this->plot = $plot;

        return $this;
    }

    /**
     * @return Collection|MovieNight[]
     */
    public function getMovieNights(): Collection
    {
        return $this->movieNights;
    }

    /**
     * @param MovieNight $movieNight add to movie
     *
     * @return Movie
     */
    public function addMovieNight(MovieNight $movieNight): self
    {
        if (!$this->movieNights->contains($movieNight)) {
            $this->movieNights[] = $movieNight;
        }

        return $this;
    }

    /**
     * @param MovieNight $movieNight remove from movie
     *
     * @return Movie
     */
    public function removeMovieNight(MovieNight $movieNight): self
    {
        if ($this->movieNights->contains($movieNight)) {
            $this->movieNights->removeElement($movieNight);
        }

        return $this;
    }
}
