<?php

namespace App\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Serializer\Annotation\Groups;

/**
 * @ORM\Entity(repositoryClass="App\Repository\PersonRepository")
 */
class Person
{
    /**
     * @ORM\Id()
     * @ORM\GeneratedValue()
     * @ORM\Column(type="integer")
     * @Groups({"person", "movie"})
     */
    private $id;

    /**
     * @ORM\Column(type="string", length=255)
     * @Groups({"person", "movie"})
     */
    private $firstName;

    /**
     * @ORM\Column(type="string", length=255)
     * @Groups({"person", "movie"})
     */
    private $lastName;

    /**
     * @ORM\Column(type="date")
     * @Groups({"person"})
     */
    private $dob;

    /**
     * @ORM\Column(type="string", length=10)
     * @Groups({"person", "movie"})
     */
    private $nationality;

    /**
     * @ORM\OneToMany(targetEntity="App\Entity\Movie", mappedBy="director")
     * @Groups({"person"})
     */
    private $moviesDone;

    /**
     * @ORM\ManyToMany(targetEntity="App\Entity\Movie", mappedBy="actors")
     * @Groups({"person"})
     */
    private $moviesIn;

    public function __construct()
    {
        $this->moviesDone = new ArrayCollection();
        $this->moviesIn = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getFirstName(): ?string
    {
        return $this->firstName;
    }

    public function setFirstName(string $firstName): self
    {
        $this->firstName = $firstName;

        return $this;
    }

    public function getLastName(): ?string
    {
        return $this->lastName;
    }

    public function setLastName(string $lastName): self
    {
        $this->lastName = $lastName;

        return $this;
    }

    public function getDob(): ?\DateTimeInterface
    {
        return $this->dob;
    }

    public function setDob(\DateTimeInterface $dob): self
    {
        $this->dob = $dob;

        return $this;
    }

    public function getNationality(): ?string
    {
        return $this->nationality;
    }

    public function setNationality(string $nationality): self
    {
        $this->nationality = $nationality;

        return $this;
    }

    /**
     * @return Collection|Movie[]
     */
    public function getMoviesDone(): Collection
    {
        return $this->moviesDone;
    }

    public function addMoviesDone(Movie $moviesDone): self
    {
        if (!$this->moviesDone->contains($moviesDone)) {
            $this->moviesDone[] = $moviesDone;
            $moviesDone->setDirector($this);
        }

        return $this;
    }

    public function removeMoviesDone(Movie $moviesDone): self
    {
        if ($this->moviesDone->contains($moviesDone)) {
            $this->moviesDone->removeElement($moviesDone);
            // set the owning side to null (unless already changed)
            if ($moviesDone->getDirector() === $this) {
                $moviesDone->setDirector(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection|Movie[]
     */
    public function getMoviesIn(): Collection
    {
        return $this->moviesIn;
    }

    public function addMoviesIn(Movie $moviesIn): self
    {
        if (!$this->moviesIn->contains($moviesIn)) {
            $this->moviesIn[] = $moviesIn;
            $moviesIn->addActor($this);
        }

        return $this;
    }

    public function removeMoviesIn(Movie $moviesIn): self
    {
        if ($this->moviesIn->contains($moviesIn)) {
            $this->moviesIn->removeElement($moviesIn);
            $moviesIn->removeActor($this);
        }

        return $this;
    }
}
