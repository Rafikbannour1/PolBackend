<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;
use App\Repository\GroupeRepository;
use Doctrine\Common\Collections\Collection;
use ApiPlatform\Core\Annotation\ApiResource;
use Doctrine\Common\Collections\ArrayCollection;

/**
 * @ORM\Entity(repositoryClass=GroupeRepository::class)
 * @ApiResource(formats={"json"}) 
 */
class Groupe
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
    private $objectif;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $image;

    /**
     * @ORM\ManyToOne(targetEntity=User::class, inversedBy="groupes")
     * @ORM\JoinColumn(nullable=false)
     */
    private $user;

    /**
     * @ORM\OneToMany(targetEntity=DocumentGroupe::class, mappedBy="groupe", orphanRemoval=true)
     */
    private $documentGroupe;

    /**
     * @ORM\ManyToOne(targetEntity=Competence::class, inversedBy="groupes")
     * @ORM\JoinColumn(nullable=false)
     */
    private $competence;

    /**
     * @ORM\Column(type="float", length=255)
     */
    private $prix;

    /**
     * @ORM\ManyToMany(targetEntity=User::class, inversedBy="cours")
     */
    private $users;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $evaluation;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $etat;

    /**
     * @ORM\Column(type="datetime")
     */
    private $dateAjout;

    /**
     * @ORM\OneToMany(targetEntity=HistoriqueAchat::class, mappedBy="cours",orphanRemoval=true)
     */
    private $historiqueAchats;

    /**
     * @ORM\Column(type="integer")
     */
    private $rateValue;

    /**
     * @ORM\OneToMany(targetEntity=Rate::class, mappedBy="cours",orphanRemoval=true)
     */
    private $rates;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $langue; 

  

    public function __construct()
    {
       
        $this->documentGroupe = new ArrayCollection();
        $this->users = new ArrayCollection();
        $this->historiqueAchats = new ArrayCollection();
        $this->rates = new ArrayCollection();
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

    public function getObjectif(): ?string
    {
        return $this->objectif;
    }

    public function setObjectif(string $objectif): self
    {
        $this->objectif = $objectif;

        return $this;
    }

    public function getImage(): ?string
    {
        return $this->image;
    }

    public function setImage(string $image): self
    {
        $this->image = $image;

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
     * @return Collection<int, DocumentGroupe>
     */
    public function getDocumentGroupe(): Collection
    {
        return $this->documentGroupe;
    }

    public function addDocumentGroupe(DocumentGroupe $documentGroupe): self
    {
        if (!$this->documentGroupe->contains($documentGroupe)) {
            $this->documentGroupe[] = $documentGroupe;
            $documentGroupe->setGroupe($this);
        }

        return $this;
    }

    public function removeDocumentGroupe(DocumentGroupe $documentGroupe): self
    {
        if ($this->documentGroupe->removeElement($documentGroupe)) {
            // set the owning side to null (unless already changed)
            if ($documentGroupe->getGroupe() === $this) {
                $documentGroupe->setGroupe(null);
            }
        }

        return $this;
    }
  

    public function getCompetence(): ?Competence
    {
        return $this->competence;
    }

    public function setCompetence(?Competence $competence): self
    {
        $this->competence = $competence;

        return $this;
    }

    public function getPrix(): ?float
    {
        return $this->prix;
    }

    public function setPrix(float $prix): self
    {
        $this->prix = $prix;

        return $this;
    }
   

    /**
     * @return Collection<int, User>
     */
    public function getUsers(): Collection
    {
        return $this->users;
    }

    public function addUser(User $user): self
    {
        if (!$this->users->contains($user)) {
            $this->users[] = $user;
        }

        return $this;
    }

    public function removeUser(User $user): self
    {
        $this->users->removeElement($user);

        return $this;
    }
 
 

    public function getEvaluation(): ?string
    {
        return $this->evaluation;
    }

    public function setEvaluation(string $evaluation): self
    {
        $this->evaluation = $evaluation;

        return $this;
    }

    public function getEtat(): ?string
    {
        return $this->etat;
    }

    public function setEtat(string $etat): self
    {
        $this->etat = $etat;

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

    public function toArray()
    {
        return ['id'=>$this->id,'title'=>$this->title,'objectif'=>$this->objectif,'user'=>$this->user
        ,'documentGroupe'=>$this->documentGroupe ,'image'=>$this->image ,'competence'=>$this->competence ,
        'prix'=>$this->prix , 'rightUser'=>$this->users ,'dateAjout'=>$this->dateAjout , 'etat'=> $this->etat ,
        'evaluation'=>$this->evaluation,'rateValue'=>$this->rateValue ,'langue'=>$this->langue 
        ] ;  
    }

    /**
     * @return Collection<int, HistoriqueAchat>
     */
    public function getHistoriqueAchats(): Collection
    {
        return $this->historiqueAchats;
    }

    public function addHistoriqueAchat(HistoriqueAchat $historiqueAchat): self
    {
        if (!$this->historiqueAchats->contains($historiqueAchat)) {
            $this->historiqueAchats[] = $historiqueAchat;
            $historiqueAchat->setCours($this);
        }

        return $this;
    }

    public function removeHistoriqueAchat(HistoriqueAchat $historiqueAchat): self
    {
        if ($this->historiqueAchats->removeElement($historiqueAchat)) {
            // set the owning side to null (unless already changed)
            if ($historiqueAchat->getCours() === $this) {
                $historiqueAchat->setCours(null);
            }
        }

        return $this;
    }

    public function getRateValue(): ?int
    {
        return $this->rateValue;
    }

    public function setRateValue(int $rateValue): self
    {
        $this->rateValue = $rateValue;

        return $this;
    }

    /**
     * @return Collection<int, Rate>
     */
    public function getRates(): Collection
    {
        return $this->rates; 
    }

    public function addRate(Rate $rate): self
    {
        if (!$this->rates->contains($rate)) {
            $this->rates[] = $rate;
            $rate->setCours($this);
        }

        return $this;
    }

    public function removeRate(Rate $rate): self
    {
        if ($this->rates->removeElement($rate)) {
            // set the owning side to null (unless already changed)
            if ($rate->getCours() === $this) {
                $rate->setCours(null);
            }
        }

        return $this;
    }

    public function getLangue(): ?string
    {
        return $this->langue;
    }

    public function setLangue(string $langue): self
    {
        $this->langue = $langue;

        return $this;
    }
}
