<?php

namespace App\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Security\Core\User\UserInterface;
Use Symfony\Component\Validator\Constraints as Assert;
Use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;

/**
 * User class represents registered user account
 * 
 * @ORM\Entity(repositoryClass="App\Repository\UserRepository")
 * @UniqueEntity(fields="loginName", message="Le nom d'utilisateur est déjà utilisé !")
 * @UniqueEntity(fields="email", message="L'email est déjà utilisé !")
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
     * @Assert\NotBlank()
     * @Assert\Length(min = 2, minMessage = "Votre prénom doit faire au moins 2 caractères !")
     * 
     */
    private $firstName;

    /**
     * @ORM\Column(type="string", length=255)
     * @Assert\NotBlank()
     * @Assert\Length(min = 2, minMessage = "Votre nom doit faire au moins 2 caractères !")
     */
    private $lastName;

    /**
     * email has an unicity constraint
     * 
     * @ORM\Column(type="string", length=255)
     * @Assert\Email(message="Veuillez renseigner un email valide !")
     */
    private $email;

    /**
     * Contains the crypted password
     * 
     * @ORM\Column(type="string", length=255)
     * @Assert\Length(min = 6, minMessage = "Votre mot de passe doit faire au moins 6 caractères !")
     */
    private $passwordHash;

    /**
     * Used as verification input
     * 
     * @Assert\EqualTo(propertyPath="passwordHash", message="Les mots de passe sont différents !")
     */
    public $passwordConfirm;

    /**
     * File name of the avatar image
     * 
     * @ORM\Column(type="string", length=255, nullable=true)
     * 
     */
    private $avatar;

    /**
     * @ORM\OneToMany(targetEntity="App\Entity\Comment", mappedBy="user", orphanRemoval=true)
     */
    private $comments;

    /**
     * @ORM\OneToMany(targetEntity="App\Entity\Trick", mappedBy="user")
     */
    private $tricks;

    /**
     * loginname is used to login and has an unicity constraints
     * 
     * @ORM\Column(type="string", length=255)
     * @Assert\Length(min = 2, minMessage = "Votre login doit faire au moins 2 caractères !")
     */
    private $loginName;

    /**
     * Date when token is created. Is used to create an limited delay action
     * 
     * @ORM\Column(type="datetime", nullable=true)
     */
    private $tokenRequestAt;

    /**
     * Token is created for password recuperation and account creation verification
     * 
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $token;

    /**
     * Define the account activation
     * 
     * @ORM\Column(type="boolean")
     */
    private $isActive;

    public function __construct()
    {
        $this->comments = new ArrayCollection();
        $this->tricks = new ArrayCollection();
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

    public function getEmail(): ?string
    {
        return $this->email;
    }

    public function setEmail(string $email): self
    {
        $this->email = $email;

        return $this;
    }

    public function getPasswordHash(): ?string
    {
        return $this->passwordHash;
    }

    public function setPasswordHash(string $passwordHash): self
    {
        $this->passwordHash = $passwordHash;

        return $this;
    }

    public function getAvatar(): ?string
    {
        return $this->avatar;
    }

    public function setAvatar(?string $avatar): self
    {
        $this->avatar = $avatar;

        return $this;
    }

    public function getRoles()
    {
        return ['ROLE_USER'];
    }

    public function getPassword()
    {
        return $this->passwordHash;
    }

    public function getUsername()
    {
        return $this->loginName;
    }

    public function eraseCredentials()
    {
        
    }

    public function getSalt()
    {
        
    }

    /**
     * @return Collection|Comment[]
     */
    public function getComments(): Collection
    {
        return $this->comments;
    }

    public function addComment(Comment $comment): self
    {
        if (!$this->comments->contains($comment)) {
            $this->comments[] = $comment;
            $comment->setUser($this);
        }

        return $this;
    }

    public function removeComment(Comment $comment): self
    {
        if ($this->comments->contains($comment)) {
            $this->comments->removeElement($comment);
            // set the owning side to null (unless already changed)
            if ($comment->getUser() === $this) {
                $comment->setUser(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection|Trick[]
     */
    public function getTricks(): Collection
    {
        return $this->tricks;
    }

    public function addTrick(Trick $trick): self
    {
        if (!$this->tricks->contains($trick)) {
            $this->tricks[] = $trick;
            $trick->setUser($this);
        }

        return $this;
    }

    public function removeTrick(Trick $trick): self
    {
        if ($this->tricks->contains($trick)) {
            $this->tricks->removeElement($trick);
            // set the owning side to null (unless already changed)
            if ($trick->getUser() === $this) {
                $trick->setUser(null);
            }
        }

        return $this;
    }

    /**
     * Return the firstname and the lastname of the user
     * 
     */
    public function getFullName()
    {
        return $this->firstName . ' ' . $this->lastName;
    }

    public function getLoginName()
    {
        return $this->loginName;
    }

    public function setLoginName(string $loginName): self
    {
        $this->loginName = $loginName;

        return $this;
    }

    public function getTokenRequestAt(): ?\DateTimeInterface
    {
        return $this->tokenRequestAt;
    }

    public function setTokenRequestAt(?\DateTimeInterface $tokenRequestAt): self
    {
        $this->tokenRequestAt = $tokenRequestAt;

        return $this;
    }

    public function getToken(): ?string
    {
        return $this->token;
    }

    public function setToken(?string $token): self
    {
        $this->token = $token;

        return $this;
    }


    public function getIsActive(): ?bool
    {
        return $this->isActive;
    }

    public function setIsActive(bool $isActive): self
    {
        $this->isActive = $isActive;

        return $this;
    }
}

