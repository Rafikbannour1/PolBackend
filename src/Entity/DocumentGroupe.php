<?php

namespace App\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use ApiPlatform\Core\Annotation\ApiResource;
use App\Repository\DocumentGroupeRepository;

/**
 * @ORM\Entity(repositoryClass=DocumentGroupeRepository::class)
 * @ApiResource(formats={"json"}) 
 */
class DocumentGroupe 
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
     * @ORM\Column(type="string", length=255)
     */
    private $description;

    /**
     * @ORM\Column(type="date")
     */
    private $dateAjout;

    /**
     * @ORM\ManyToOne(targetEntity=Groupe::class, inversedBy="documentGroupe")
     * @ORM\JoinColumn(nullable=false)
     */
    private $groupe;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $numeroModule;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $objectif;

    /**
     * @ORM\OneToMany(targetEntity=FormationFiles::class, mappedBy="documentGroupe", orphanRemoval=true,cascade={"persist"})
     */
    private $formationFiles;

    /**
     * @ORM\OneToMany(targetEntity=Videos::class, mappedBy="documentGroupe", orphanRemoval=true,cascade={"persist"})
     */
    private $videos;

    public function __construct()
    {
        $this->formationFiles = new ArrayCollection();
        $this->videos = new ArrayCollection();
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

    public function setDescription(string $description): self
        {
            $this->description = $description;

            return $this;
        }

    public function getDateAjout(): ?\DateTimeInterface
        {
            return $this->dateAjout;
        }

    public function setDateAjout(\DateTimeInterface $dateAjout): self
        {
            $this->dateAjout = $dateAjout;

            return $this;
        }

    public function getGroupe(): ?Groupe
        {
            return $this->groupe;
        }

    public function setGroupe(?Groupe $groupe): self
        {
            $this->groupe = $groupe;

            return $this;
        }


   

    public function getNumeroModule(): ?string
    {
        return $this->numeroModule;
    }

    public function setNumeroModule(string $numeroModule): self
    {
        $this->numeroModule = $numeroModule;

        return $this;
    }

    public function getObjectif(): ?string
    {
        return $this->objectif;
    }

    public function setObjectif(string $objectif): self
    {
        $this->objectif = $objectif;

        return $this;
    }

  

    /**
     * @return Collection<int, FormationFiles>
     */
    public function getFormationFiles(): Collection
    {
        return $this->formationFiles;
    }

    public function addFormationFile(FormationFiles $formationFile): self
    {
        if (!$this->formationFiles->contains($formationFile)) {
            $this->formationFiles[] = $formationFile;
            $formationFile->setDocumentGroupe($this);
        }

        return $this;
    }

    public function removeFormationFile(FormationFiles $formationFile): self
    {
        if ($this->formationFiles->removeElement($formationFile)) {
            // set the owning side to null (unless already changed)
            if ($formationFile->getDocumentGroupe() === $this) {
                $formationFile->setDocumentGroupe(null); 
            }
        }

        return $this;
    }

   

    /**
     * @return Collection<int, Videos>
     */
    public function getVideos(): Collection
    {
        return $this->videos;
    }

    public function addVideo(Videos $video): self
    {
        if (!$this->videos->contains($video)) {
            $this->videos[] = $video;
            $video->setDocumentGroupe($this);
        }

        return $this;
    }

    public function removeVideo(Videos $video): self
    {
        if ($this->videos->removeElement($video)) {
            // set the owning side to null (unless already changed)
            if ($video->getDocumentGroupe() === $this) {
                $video->setDocumentGroupe(null);
            }
        }

        return $this;
    }

    public function toArray()
    {
        return [ 'id'=>$this->id,'title'=>$this->title,'description'=>$this->description,
        'dateAjout'=>$this->dateAjout ,'groupe'=>$this->groupe,
        'numeroModule'=>$this->numeroModule ,'objectif'=>$this->objectif] ;  
    }
}
