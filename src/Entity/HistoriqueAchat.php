<?php

namespace App\Entity;

use App\Entity\User;
use App\Entity\Groupe;
use Doctrine\ORM\Mapping as ORM;
use Doctrine\Common\Collections\Collection;
use ApiPlatform\Core\Annotation\ApiResource;
use App\Repository\HistoriqueAchatRepository;
use Doctrine\Common\Collections\ArrayCollection;

/**
 * @ORM\Entity(repositoryClass=HistoriqueAchatRepository::class)
 * @ApiResource(formats={"json"} )
 */
class HistoriqueAchat
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\Column(type="datetime")
     */
    private $dateAchat;

    /**
     * @ORM\ManyToOne(targetEntity=User::class, inversedBy="historiqueAchats")
     * @ORM\JoinColumn(nullable=false)
     */
    private $user;

    /**
     * @ORM\ManyToOne(targetEntity=Groupe::class, inversedBy="historiqueAchats")
     * @ORM\JoinColumn(nullable=false)
     */
    private $cours;

  

  

    public function __construct()
    {
        $this->user = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getDateAchat(): ?\DateTimeInterface
    {
        return $this->dateAchat;
    }

    public function setDateAchat(\DateTimeInterface $dateAchat): self
    {
        $this->dateAchat = $dateAchat;

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

    public function getCours(): ?Groupe
    {
        return $this->cours;
    }

    public function setCours(?Groupe $cours): self
    {
        $this->cours = $cours;

        return $this;
    }

    public function addCours(Groupe $cours): self
    {
        if (!$this->cours->contains($cours)) {
            $this->cours[] = $cours;
            $cours->setHistoriqueAchat($this);
        }

        return $this;
    }

    public function addUser(User $user): self
    {
        if (!$this->user->contains($user)) {
            $this->user[] = $user;
            $user->setHistoriqueAchat($this);
        }

        return $this;
    }

   public function toArray() 
   {
       return ['id'=>$this->id, 'user'=>$this->user , 'cour'=> $this->cours ,  'dateAchat' => $this->dateAchat ] ; 
       
   }
}
