<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;
use App\Repository\DomaineRepository;
use Doctrine\Common\Collections\Collection;
use ApiPlatform\Core\Annotation\ApiResource;
use Doctrine\Common\Collections\ArrayCollection;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;
use Symfony\Component\Validator\Constraints as Assert;
/**
 * @ApiResource(formats={"json"}) 
 * @ORM\Entity
 */
class Domaine
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
     * @ORM\OneToMany(targetEntity=Type::class, mappedBy="domaine",orphanRemoval=true)
     */
    private $types;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $image;

  



    public function __construct()
    {
        $this->types = new ArrayCollection();
        $this->groupes = new ArrayCollection();
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


    /**
     * @return Collection<int, Type>
     */
    public function getTypes(): Collection
    {
        return $this->types;
    }

    public function addType(Type $type): self
    {
        if (!$this->types->contains($type)) {
            $this->types[] = $type;
            $type->setDomaine($this);
        }

        return $this;
    }

    public function removeType(Type $type): self
    {
        if ($this->types->removeElement($type)) {
            // set the owning side to null (unless already changed)
            if ($type->getDomaine() === $this) {
                $type->setDomaine(null);
            }
        }

        return $this;
    } 

    

  

    
   
    public function toArray()
    {
        return ['id'=> $this->id , 'title'=>$this->title ,'types'=>$this->getTypes(),
        'image'=>$this->image ] ;      
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

   

   
   
}
