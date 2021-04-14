<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;
use Doctrine\Common\Collections\Collection;
use Doctrine\Common\Collections\ArrayCollection;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Validator\Constraints\Email;
use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;
use Symfony\Component\Validator\Context\ExecutionContextInterface;

/**
 * @ORM\Entity(repositoryClass="App\Repository\UserRepository")
 * @UniqueEntity(fields="email", message="Email deja utilisé")
 * @UniqueEntity(fields="username", message="Pseudo deja utilisé")
 */
class User implements UserInterface
{
    /**
     * @ORM\Id()
     * @ORM\GeneratedValue()
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\Column(type="string", length=255)
     * @Assert\Length(min="3",minMessage="Votre username doit faire minimum 3 characteres")
     * @Assert\NotBlank(message="Veuillez renseigner un username ")
     */
    private $username;

    /**
     * @ORM\Column(type="string", length=255)
     * @Assert\Length(min="8",minMessage="Votre mot de passe doit faire minimum 8 characteres")
     * @Assert\EqualTo(propertyPath="confirm_password",message="Mots de passe differents")
     */
    private $password;

    /** 
     * @Assert\Length(min="8",minMessage="Votre mot de passe doit faire minimum 8 characteres")
     * @Assert\EqualTo(propertyPath="password",message="Mots de passe differents")
     */
    private $confirm_password;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $email;

    /**
     * @var array
     * 
     * @ORM\Column(type="json")
     */
    private $roles = [];

    /**
     * @ORM\OneToMany(targetEntity=Exercice::class, mappedBy="author",cascade={"remove"})
     */
    private $exercicesCreated;

    /**
     * @ORM\OneToMany(targetEntity=Formation::class, mappedBy="author",cascade={"remove"})
     */
    private $formationsCreated;

    /**
     * @ORM\OneToMany(targetEntity=Resolution::class, mappedBy="user",cascade={"remove"})
     */
    private $resolutions;

    /**
     * @ORM\ManyToMany(targetEntity=Formation::class, mappedBy="inscrits",cascade={"remove"})
     */
    private $formations;

    public function __construct()
    {
        $this->roles[] = 'ROLE_ETUDIANT';
        $this->exercicesCreated = new ArrayCollection();
        $this->formationsCreated = new ArrayCollection();
        $this->resolutions = new ArrayCollection();
        $this->formations = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function setId(int $id): self
    {
        $this->id = $id;
        return $this;
    }

    public function getUsername(): ?string
    {
        return $this->username;
    }

    public function setUsername(string $username): self
    {
        $this->username = $username;

        return $this;
    }

    public function getPassword(): ?string
    {
        return $this->password;
    }

    public function setPassword(string $password): self
    {
        $this->password = $password;

        return $this;
    }

    public function getConfirmPassword(): ?string
    {
        return $this->confirm_password;
    }

    public function setConfirmPassword(string $confirm_password): self
    {
        $this->confirm_password = $confirm_password;

        return $this;
    }

    public function getSalt()
    {
        return null;
    }

    public function eraseCredentials()
    {
    } 

    public function serialize()
    {
        return serialize([
            $this->id,
            $this->username,
            $this->password
        ]);
    }

    public function unserialize($serialized)
    {
        list(
            $this->id,
            $this->username,
            $this->password
        ) = unserialize($serialized);
    }

    public function getEmail(): ?string
    {
        return $this->email;
    }

    public function setEmail(string $email): self
    {
        $this->email = $email;

        return $this;
    }

    /**
     * @return array(Role|string)[] the user roles
     */
    public function getRoles(): array
    {
        $tmp_roles = $this->roles;
        if (in_array('ROLE_ETUDIANT', $tmp_roles) === false) {
            $tmp_roles[] = 'ROLE_ETUDIANT';
        }
        return $tmp_roles;
    }

    public function setRoles(array $roles): self
    {
        $this->roles = $roles;

        return $this;
    }

    /**
     * @return Collection|Exercice[]
     */
    public function getExercicesCreated(): Collection
    {
        return $this->exercicesCreated;
    }

    public function addExercicesCreated(Exercice $exercicesCreated): self
    {
        if (!$this->exercicesCreated->contains($exercicesCreated)) {
            $this->exercicesCreated[] = $exercicesCreated;
            $exercicesCreated->setAuthor($this);
        }

        return $this;
    }

    public function removeExercicesCreated(Exercice $exercicesCreated): self
    {
        if ($this->exercicesCreated->contains($exercicesCreated)) {
            $this->exercicesCreated->removeElement($exercicesCreated);
            // set the owning side to null (unless already changed)
            if ($exercicesCreated->getAuthor() === $this) {
                $exercicesCreated->setAuthor(null);
            }
        }

        return $this;
    }
    /**
     * @return Collection|Formation[]
     */
    public function getFormationsCreated(): Collection
    {
        return $this->formationsCreated;
    }
    public function addFormationsCreated(Formation $formationsCreated): self
    {
        if (!$this->formationsCreated->contains($formationsCreated)) {
            $this->formationsCreated[] = $formationsCreated;
            $formationsCreated->setAuthor($this);
        }

        return $this;
    }

    public function removeFormationsCreated(Formation $formationsCreated): self
    {
        if ($this->formationsCreated->contains($formationsCreated)) {
            $this->exercicesCreated->removeElement($formationsCreated);
            // set the owning side to null (unless already changed)
            if ($formationsCreated->getAuthor() === $this) {
                $formationsCreated->setAuthor(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection|Resolution[]
     */
    public function getResolutions(): Collection
    {
        return $this->resolutions;
    }

    public function addResolution(Resolution $resolution): self
    {
        if (!$this->resolutions->contains($resolution)) {
            $this->resolutions[] = $resolution;
            $resolution->setUser($this);
        }

        return $this;
    }

    public function removeResolution(Resolution $resolution): self
    {
        if ($this->resolutions->contains($resolution)) {
            $this->resolutions->removeElement($resolution);
            // set the owning side to null (unless already changed)
            if ($resolution->getUser() === $this) {
                $resolution->setUser(null);
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
            $formation->addInscrit($this);
        }

        return $this;
    }

    public function removeFormation(Formation $formation): self
    {
        if ($this->formations->contains($formation)) {
            $this->formations->removeElement($formation);
            $formation->removeInscrit($this);
        }

        return $this;
    }

}
