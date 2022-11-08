<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;
use App\Repository\TypeRepository;
use Doctrine\Common\Collections\Collection;
use ApiPlatform\Core\Annotation\ApiResource;
use Doctrine\Common\Collections\ArrayCollection;
use Symfony\Component\Serializer\Annotation\Groups;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
/**
 * @ApiResource(formats={"json"}) 
 * @ORM\Entity(repositoryClass=TypeRepository::class)
 */
class Type
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
     * @ORM\ManyToOne(targetEntity=Domaine::class, inversedBy="types")
     * @ORM\JoinColumn(nullable=false)
     
     */
    private $domaine;

    /**
     * @ORM\OneToMany(targetEntity=Competence::class, mappedBy="type" ,orphanRemoval=true)
     * @Groups("read") 
     */
    private $competences;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $image;

    public function __construct()
    {
        $this->competences = new ArrayCollection();
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

    public function getDomaine(): ?Domaine
    {
        return    $this->domaine;  
    }

    public function setDomaine(?Domaine $domaine): self
    {
        $this->domaine = $domaine;

        return $this;
    } 



    /**
     * @return Collection<int, Competence>
     */
    public function getCompetences(): Collection
    {
        return $this->competences;
         

    }

    public function addCompetence(Competence $competence): self
    {
        if (!$this->competences->contains($competence)) {
            $this->competences[] = $competence;
            $competence->setType($this);
        }

        return $this;
    }

    public function removeCompetence(Competence $competence): self
    {
        if ($this->competences->removeElement($competence)) {
            // set the owning side to null (unless already changed)
            if ($competence->getType() === $this) {
                $competence->setType(null);
            }
        }

        return $this;
    }  
    public function toArray()
    {
        return ['id'=> $this->id , 
        'title'=>$this->title , 'domaine'=>$this->domaine , 'competences'=>$this->competences ,'image'=>$this->image ] ;   
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
