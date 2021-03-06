<?php

namespace App\Entity;

use Cocur\Slugify\Slugify;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;

/**
 * Trick class represents a trick
 * 
 * @ORM\Entity(repositoryClass="App\Repository\TrickRepository")
 * @UniqueEntity(fields="name", message="Le nom du trick est déjà utilisé !")
 * @UniqueEntity(fields="slug", message="Le slug existe déjà")
 * 
 * @ORM\HasLifecycleCallbacks
 * 
 */
class Trick
{
    /**
     * @ORM\Id()
     * @ORM\GeneratedValue()
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * Trick name has an unicity constraint
     * 
     * @ORM\Column(type="string", length=255)
     * @Assert\Length(min = 2, minMessage = "Le nom du trick doit faire au moins 2 caractères !")
     * @Assert\Length(max = 40, maxMessage = "Le nom du trick ne doit pas faire plus de 40 caractères !")
     * 
     * 
     */
    private $name;

    /**
     * @ORM\Column(type="text")
     * @Assert\Length(min = 10, minMessage = "La description du trick doit faire au moins 10 caractères !")
     */
    private $description;

    /**
     * @ORM\Column(type="datetime")
     * 
     */
    private $createdAt;

    /**
     * Slug has an unicity constraint
     * 
     * @ORM\Column(type="string", length=255)
     */
    private $slug;

    /**
     * Images are optionals
     * 
     * @ORM\OneToMany(targetEntity="App\Entity\Image", mappedBy="trick", orphanRemoval=true)  
     * 
     */
    private $images;

    /**
     * ImageMain is the reference image for the trick. 
     * 
     * @ORM\Column(type="string", length=255)
     */
    private $imageMain;

    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\Category", inversedBy="tricks")
     * @ORM\JoinColumn(nullable=true)
     * 
     */
    private $category;

    /**
     * @ORM\Column(type="datetime", nullable=true)
     */
    private $modifiedAt;

    /**
     * Videos are optionals
     * 
     * @ORM\OneToMany(targetEntity="App\Entity\Video", mappedBy="trick", orphanRemoval=true)
     */
    private $videos;

    /**
     * @ORM\OneToMany(targetEntity="App\Entity\Comment", mappedBy="trick", orphanRemoval=true)
     * @ORM\OrderBy({"createdAt" = "DESC"})
     */
    private $comments;

    /**
     * Each tricks are linked to an user but no functionnality use this link for yet
     * 
     * 
     * @ORM\ManyToOne(targetEntity="App\Entity\User", inversedBy="tricks")
     */
    private $user;


    public function __construct()
    {
        $this->images = new ArrayCollection();
        $this->videos = new ArrayCollection();
        $this->comments = new ArrayCollection();
    }

    /**
     * Slug generation with use of the trick name
     * 
     * @ORM\PrePersist
     * @ORM\PreUpdate
     */
    public function initializeSlug()
    {
        if (empty($this->slug)) {
            $slugify = new Slugify();
            $this->slug = $slugify->slugify($this->name);
        }
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getName(): ?string
    {
        return $this->name;
    }

    public function setName(string $name): self
    {
        $this->name = $name;

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

    public function getCreatedAt(): ?\DateTimeInterface
    {
        return $this->createdAt;
    }

    public function setCreatedAt(\DateTimeInterface $createdAt): self
    {
        $this->createdAt = $createdAt;

        return $this;
    }

    public function getSlug(): ?string
    {
        return $this->slug;
    }

    public function setSlug(string $slug): self
    {
        $this->slug = $slug;

        return $this;
    }

    /**
     * @return Collection|Image[]
     */
    public function getImages(): Collection
    {
        return $this->images;
    }

    public function addImage(Image $image): self
    {
        if (!$this->images->contains($image)) {
            $this->images[] = $image;
            $image->setTrick($this);
        }

        return $this;
    }

    public function removeImage(Image $image): self
    {
        if ($this->images->contains($image)) {
            $this->images->removeElement($image);
            // set the owning side to null (unless already changed)
            if ($image->getTrick() === $this) {
                $image->setTrick(null);
            }
        }

        return $this;
    }

    public function getImageMain(): ?string
    {
        return $this->imageMain;
    }

    public function setImageMain(?string $imageMain): self
    {
        $this->imageMain = $imageMain;

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

    public function getModifiedAt(): ?\DateTimeInterface
    {
        return $this->modifiedAt;
    }

    public function setModifiedAt(?\DateTimeInterface $modifiedAt): self
    {
        $this->modifiedAt = $modifiedAt;

        return $this;
    }

    /**
     * @return Collection|Video[]
     */
    public function getVideos(): Collection
    {
        return $this->videos;
    }

    public function addVideo(Video $video): self
    {
        if (!$this->videos->contains($video)) {
            $this->videos[] = $video;
            $video->setTrick($this);
        }

        return $this;
    }

    public function removeVideo(Video $video): self
    {
        if ($this->videos->contains($video)) {
            $this->videos->removeElement($video);
            // set the owning side to null (unless already changed)
            if ($video->getTrick() === $this) {
                $video->setTrick(null);
            }
        }

        return $this;
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
            $comment->setTrick($this);
        }

        return $this;
    }

    public function removeComment(Comment $comment): self
    {
        if ($this->comments->contains($comment)) {
            $this->comments->removeElement($comment);
            // set the owning side to null (unless already changed)
            if ($comment->getTrick() === $this) {
                $comment->setTrick(null);
            }
        }

        return $this;
    }

    public function getUser(): ?User
    {
        return $this->user;
    }

    public function setUser(?User $user): self
    {
        $this->user = $user;

        return $this;
    }

    /**
     * Add date to the createdAt attribut when a trick is created
     * 
     * @ORM\PrePersist
     */
    public function creationDate()
    {
        if (empty($this->createdAt)) {
            $this->createdAt = new \DateTime();
        }
    }

    /**
     * Modify date to the modifiedAt attribut for each trick update
     * 
     * @ORM\PreUpdate
     */
    public function modificationDate()
    {
        $this->modifiedAt = new \DateTime();
    }

    /**
    * Delete the imageMain file
    *
    * @ORM\PostRemove
    */
    public function imageMainFileDelete()
    {
        unlink('uploads/images/' . $this->imageMain);
    }
}

