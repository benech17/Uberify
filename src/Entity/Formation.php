<?php

namespace App\Entity;

use Cocur\Slugify\Slugify;
use Doctrine\ORM\Mapping as ORM;
use App\Repository\FormationRepository;
use Doctrine\Common\Collections\Collection;
use Doctrine\Common\Collections\ArrayCollection;
use Exception;

/**
 * @ORM\Entity(repositoryClass=FormationRepository::class)
 */
class Formation
{
    /**
     * @ORM\Id()
     * @ORM\GeneratedValue()
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\OneToMany(targetEntity=Exercice::class, mappedBy="formation",cascade={"remove"})
     */
    private $exercices;

    /**
     * @ORM\ManyToMany(targetEntity=User::class, inversedBy="formations")
     */
    private $inscrits;

    /**
     * @ORM\Column(type="datetime")
     */
    private $created_at;

    /**
     * @ORM\ManyToOne(targetEntity=User::class,inversedBy="formationsCreated")
     * @ORM\JoinColumn(nullable=false)
     */
    private $author;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $title;

    /**
     * @ORM\Column(type="text")
     */
    private $description;

    /**
     * @ORM\ManyToOne(targetEntity=Category::class, inversedBy="formations")
     * @ORM\JoinColumn(nullable=false)
     */
    private $category;

    public function __construct()
    {
        $this->created_at = new \DateTime();
        $this->exercices = new ArrayCollection();
        $this->inscrits = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
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
            $exercice->setFormation($this);
        }

        return $this;
    }

    public function removeExercice(Exercice $exercice): self
    {
        if ($this->exercices->contains($exercice)) {
            $this->exercices->removeElement($exercice);
            // set the owning side to null (unless already changed)
            if ($exercice->getFormation() === $this) {
                $exercice->setFormation(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection|User[]
     */
    public function getInscrits(): Collection
    {
        return $this->inscrits;
    }

    public function addInscrit(User $inscrit): self
    {
        if (!$this->inscrits->contains($inscrit)) {
            $this->inscrits[] = $inscrit;
        }
        return $this;
    }

    public function removeInscrit(User $inscrit): self
    {
        if ($this->inscrits->contains($inscrit)) {
            $this->inscrits->removeElement($inscrit);
        }

        return $this;
    }

    public function getCreatedAt(): ?\DateTimeInterface
    {
        return $this->created_at;
    }

    public function setCreatedAt(\DateTimeInterface $created_at): self
    {
        $this->created_at = $created_at;

        return $this;
    }

    public function getAuthor(): ?User
    {
        return $this->author;
    }

    public function setAuthor(?User $author): self
    {
        $this->author = $author;

        return $this;
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

    public function setDescription(string $description): self
    {
        $this->description = $description;

        return $this;
    }

    public function getCategory(): ?Category
    {
        return $this->category;
    }

    public function setCategory(?Category $category): self
    {
        $this->category = $category;

        return $this;
    }
    public function getSlug(): string
    {
        return (new Slugify())->slugify($this->title);
    }

    /**
     * @param User $user
     * @return boolean
     */
    public function isFinishedByUser(User $user): bool
    {
        foreach ($this->exercices as $exercice) {
            if (!$exercice->isResolvedByUser($user)) {
                return false;
            }
        }
        return true;
    }

    /**
     * @param User $user
     * @return boolean
     */
    public function isHalfFinishedByUser(User $user): bool
    {
        $count = 0;
        foreach ($this->exercices as $exercice) {
            if ($exercice->isResolvedByUser($user)) {
                $count += 1;
            }
        }
        try {
            $res = $count / (count($this->exercices));
        } catch (Exception $e) {
            return true; //considerons que si aucun exercice alors on peut mettre vert
        }
        return ($res>=0.5);
    }

    /**
     * get Progression % of an user on the current formation
     *
     * @param User $user
     * @return Integer
     */
    public function getProgression(User $user)
    {
        $nb_resolu = 0;
        foreach ($this->getExercices() as $exercice) {
            if ($exercice->isResolvedByUser($user)) {
                $nb_resolu = $nb_resolu + 1;
            }
        }
        return $nb_resolu;
    }

    /**
     * get Progression % of all the subscribers user on the current formation
     *
     * @return String
     */
    public function getProgressionGeneral()
    {
        $nb_exo_resolus = 0;
        $i = 0;
        foreach ($this->getInscrits() as $user) {

            if (!in_array("ROLE_ENSEIGNANT", $user->getRoles())) {  //count only students results
                $nb_exo_resolus = $nb_exo_resolus + $this->getProgression($user);
                $i = $i + 1;
            }
        }
        try {
            $result = round($nb_exo_resolus * 100 / (count($this->getExercices()) * $i));
        } catch (\Exception $e) {
            return " aucune participation pour le moment !";
        }
        return $result . '%';
    }

    /**
     * get Progression to set green or red the bg color of all the subscribers user on the current formation
     *
     * @return boolean
     */
    public function getProgressionGeneralNumber()
    {
        $nb_exo_resolus = 0;
        $i = 0;
        foreach ($this->getInscrits() as $user) {

            if (!in_array("ROLE_ENSEIGNANT", $user->getRoles())) {  //count only students results
                $nb_exo_resolus = $nb_exo_resolus + $this->getProgression($user);
                $i = $i + 1;
            }
        }
        try {
            $result = round($nb_exo_resolus * 100 / (count($this->getExercices()) * $i));
        } catch (\Exception $e) {
            return true;
        }
        return ($result>=50);
    }
}
