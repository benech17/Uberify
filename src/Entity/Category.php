<?php

namespace App\Entity;

use App\Repository\CategoryRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass=CategoryRepository::class)
 */
class Category
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $title;

    /**
     * @ORM\Column(type="text", nullable=true)
     */
    private $description;

    /**
     * @ORM\OneToMany(targetEntity=Exercice::class, mappedBy="category",cascade={"remove"})
     */
    private $exercices;

    /**
     * @ORM\OneToMany(targetEntity=Formation::class, mappedBy="category", orphanRemoval=true)
     */
    private $formations;

    public function __construct()
    {
        $this->exercices = new ArrayCollection();
        $this->formations = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getTitle(): ?string
    {
        return $this->title;
    }

    public function setTitle(string $title): self
    {
        $this->title = $title;

        return $this;
    }

    public function getDescription(): ?string
    {
        return $this->description;
    }

    public function setDescription(?string $description): self
    {
        $this->description = $description;

        return $this;
    }

    /**
     * @return Collection|Exercice[]
     */
    public function getExercices(): Collection
    {
        return $this->exercices;
    }

    public function addExercice(Exercice $exercice): self
    {
        if (!$this->exercices->contains($exercice)) {
            $this->exercices[] = $exercice;
            $exercice->setCategory($this);
        }

        return $this;
    }

    public function removeExercice(Exercice $exercice): self
    {
        if ($this->exercices->removeElement($exercice)) {
            // set the owning side to null (unless already changed)
            if ($exercice->getCategory() === $this) {
                $exercice->setCategory(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection|Formation[]
     */
    public function getFormations(): Collection
    {
        return $this->formations;
    }

    public function addFormation(Formation $formation): self
    {
        if (!$this->formations->contains($formation)) {
            $this->formations[] = $formation;
            $formation->setCategory($this);
        }

        return $this;
    }

    public function removeFormation(Formation $formation): self
    {
        if ($this->formations->removeElement($formation)) {
            // set the owning side to null (unless already changed)
            if ($formation->getCategory() === $this) {
                $formation->setCategory(null);
            }
        }

        return $this;
    }
}
