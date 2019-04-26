<?php

namespace App\Entity;

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
    private $Title;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $imdbID;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $Year;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $Runtime;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $Poster;

    /**
     * @ORM\Column(type="string", length=1000, nullable=true)
     */
    private $Plot;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getTitle(): ?string
    {
        return $this->Title;
    }

    public function setTitle(string $Title): self
    {
        $this->Title = $Title;

        return $this;
    }

    public function getImdbID(): ?string
    {
        return $this->imdbID;
    }

    public function setImdbID(string $imdbID): self
    {
        $this->imdbID = $imdbID;

        return $this;
    }

    public function getYear(): ?string
    {
        return $this->Year;
    }

    public function setYear(string $Year): self
    {
        $this->Year = $Year;

        return $this;
    }

    public function getRuntime(): ?string
    {
        return $this->Runtime;
    }

    public function setRuntime(?string $Runtime): self
    {
        $this->Runtime = $Runtime;

        return $this;
    }

    public function getPoster()
    {
        return $this->Poster;
    }

    public function setPoster($Poster): self
    {
        $this->Poster = $Poster;

        return $this;
    }

    public function getPlot(): ?string
    {
        return $this->Plot;
    }

    public function setPlot(?string $Plot): self
    {
        $this->Plot = $Plot;

        return $this;
    }
}
