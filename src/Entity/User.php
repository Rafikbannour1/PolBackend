<?php

namespace App\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use App\Repository\UserRepository;
use ApiPlatform\Core\Annotation\ApiResource;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Security\Core\User\PasswordAuthenticatedUserInterface;

/**
 * @ORM\Entity(repositoryClass=UserRepository::class)
 * @ORM\Table(name="`user`")
 * @ApiResource(formats={"json"}) 
 * @UniqueEntity(fields={"email"}, message="There is already an account with this email")
 */
class User implements UserInterface, PasswordAuthenticatedUserInterface
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\Column(type="string", length=180, unique=true)
     */
    private $email;

    /**
     * @ORM\Column(type="json")
     */
    private $roles = [];

    /**
     * @var string The hashed password
     * @ORM\Column(type="string")
     */
    private $password;

    private $plainPassword ;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $image;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $adresse;

    /**
     * @ORM\Column(type="text")
     */
    private $bio;

    /**
     * @ORM\Column(type="date")
     */
    private $date_ajout;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $name;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $metier;
    
    /**
     * @ORM\OneToMany(targetEntity=Document::class, mappedBy="auteur",orphanRemoval=true)
     */
    private $documents;

    /**
     * @ORM\OneToMany(targetEntity=Likes::class, mappedBy="user", orphanRemoval=true)
     */
    private $likes;

    /**
     * @ORM\OneToMany(targetEntity=Groupe::class, mappedBy="user",orphanRemoval=true)
     */
    private $groupes;

    /**
     * @ORM\Column(type="integer")
     */
    private $isVerified = 0 ; 





    /**
     * @ORM\ManyToMany(targetEntity=Groupe::class, mappedBy="users",orphanRemoval=true)
     */
    private $cours;

    /**
     * @ORM\OneToMany(targetEntity=HistoriqueAchat::class, mappedBy="user",orphanRemoval=true)
     */
    private $historiqueAchats;

    /**
     * @ORM\OneToMany(targetEntity=Rate::class, mappedBy="user")
     */
    private $rates;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $codeSecurite;

    /**
     * @ORM\Column(type="integer")
     */
    private $telephoneNumber;

    

  

  
 

    public function __construct()
    {
        $this->documents = new ArrayCollection();
        $this->likes = new ArrayCollection();
        $this->groupes = new ArrayCollection();
        $this->cours = new ArrayCollection();
        $this->historiqueAchats = new ArrayCollection();
        $this->rates = new ArrayCollection();
        
    } 

    public function getId(): ?int
    {
        return $this->id;
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

    
    public function getPlainPassword()
    {
        return $this->plainPassword;
    }

    public function setPlainPassword( $plainPassword):void
    {
        $this->plainPassword = $plainPassword;

        
    }

    /**
     * A visual identifier that represents this user.
     *
     * @see UserInterface
     */
    public function getUserIdentifier(): string
    {
        return (string) $this->email;
    }

    /**
     * @deprecated since Symfony 5.3, use getUserIdentifier instead
     */
    public function getUsername(): string
    {
        return (string) $this->email;
    }

    /**
     * @see UserInterface
     */
    public function getRoles(): array
    {
        $roles = $this->roles;
        // guarantee every user at least has ROLE_USER
        //$roles[] = 'ROLE_USER';

        return array_unique($roles);
    }

    public function setRoles(array $roles): self
    {
        $this->roles = $roles;

        return $this;
    }

    /**
     * @see PasswordAuthenticatedUserInterface
     */
    public function getPassword(): string
    {
        return $this->password;
    }

    public function setPassword(string $password): self
    {
        $this->password = $password;

        return $this;
    }

    /**
     * Returning a salt is only needed, if you are not using a modern
     * hashing algorithm (e.g. bcrypt or sodium) in your security.yaml.
     *
     * @see UserInterface
     */
    public function getSalt(): ?string
    {
        return null;
    }

    /**
     * @see UserInterface
     */
    public function eraseCredentials()
    {
        // If you store any temporary, sensitive data on the user, clear it here
         $this->plainPassword = null;
    }

    /**
     * @return Collection<int, Document>
     */
    public function getDocuments(): Collection
    {
        return $this->documents;
    }

    public function addDocument(Document $document): self
    {
        if (!$this->documents->contains($document)) {
            $this->documents[] = $document;
            $document->setAuteur($this);
        }

        return $this;
    }

    public function removeDocument(Document $document): self
    {
        if ($this->documents->removeElement($document)) {
            // set the owning side to null (unless already changed)
            if ($document->getAuteur() === $this) {
                $document->setAuteur(null);
            }
        }

        return $this;
    }

    public function getImage()
    {
        return $this->image;
    }

    public function setImage( $image)
    {

        $this->image = $image;

        return $this;
    }

     
    public function getAdresse(): ?string
    {
        return $this->adresse;
    }

    public function setAdresse(string $adresse): self
    {
        $this->adresse = $adresse;

        return $this;
    }

    public function getBio(): ?string
    {
        return $this->bio;
    }

    public function setBio(string $bio): self
    {
        $this->bio = $bio;

        return $this;
    }

    public function getDateAjout(): ?\DateTimeInterface
    {
        return $this->date_ajout;
    }

    public function setDateAjout(\DateTimeInterface $date_ajout): self
    {
        $this->date_ajout = $date_ajout;

        return $this;
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

    public function getMetier(): ?string
    {
        return $this->metier;
    }

    public function setMetier(string $metier): self
    {
        $this->metier = $metier;

        return $this;
    }
  
    

    /**
     * @return Collection<int, Likes>
     */
    public function getLikes(): Collection
    {
        return $this->likes;
    }

    public function addLike(Likes $like): self
    {
        if (!$this->likes->contains($like)) {
            $this->likes[] = $like;
            $like->setUser($this);
        }

        return $this;
    }

    public function removeLike(Likes $like): self
    {
        if ($this->likes->removeElement($like)) {
            // set the owning side to null (unless already changed)
            if ($like->getUser() === $this) {
                $like->setUser(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection<int, Groupe>
     */
    public function getGroupes(): Collection
    {
        return $this->groupes;
    }

    public function addGroupe(Groupe $groupe): self
    {
        if (!$this->groupes->contains($groupe)) {
            $this->groupes[] = $groupe;
            $groupe->setUser($this);
        }

        return $this;
    }

    public function removeGroupe(Groupe $groupe): self
    {
        if ($this->groupes->removeElement($groupe)) {
            // set the owning side to null (unless already changed)
            if ($groupe->getUser() === $this) {
                $groupe->setUser(null);
            }
        }

        return $this;
    }

    public function isVerified(): int
    {
        return $this->isVerified;
    }

    public function setIsVerified(int $isVerified): self
    {
        $this->isVerified = $isVerified;

        return $this;
    }

    /**
     * @return Collection<int, Groupe>
     */
    public function getCours(): Collection
    {
        return $this->cours;
    }

    public function addCour(Groupe $cour): self
    {
        if (!$this->cours->contains($cour)) {
            $this->cours[] = $cour;
            $cour->addUser($this);
        }

        return $this;
    }

    public function removeCour(Groupe $cour): self
    {
        if ($this->cours->removeElement($cour)) {
            $cour->removeUser($this);
        }

        return $this;
    }

   
    public function toArray()
    {
        return ['id'=>$this->id,'name'=> $this->name , 'email'=>$this->email , 'adresse'=>$this->adresse,
        'metier'=>$this->metier ,'dateAjout'=>$this->date_ajout, 'bio'=>$this->bio 
         ,'image'=>$this->image ,'roles'=>$this->roles,'documents'=>$this->getDocuments(),'likes'=>$this->likes,
         'cours'=>$this->cours,'password'=>$this->password,'isVerified'=>$this->isVerified,'numerotelephone'=>$this->telephoneNumber
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
            $historiqueAchat->setUser($this);
        }

        return $this;
    }

    public function removeHistoriqueAchat(HistoriqueAchat $historiqueAchat): self
    {
        if ($this->historiqueAchats->removeElement($historiqueAchat)) {
            // set the owning side to null (unless already changed)
            if ($historiqueAchat->getUser() === $this) {
                $historiqueAchat->setUser(null);
            }
        }

        return $this;
    }

    public function setHistoriqueAchat(HistoriqueAchat $historiqueAchat): self
    {
        $this->historiqueAchat = $historiqueAchat;

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
            $rate->setUser($this);
        }

        return $this;
    }

    public function removeRate(Rate $rate): self
    {
        if ($this->rates->removeElement($rate)) {
            // set the owning side to null (unless already changed)
            if ($rate->getUser() === $this) {
                $rate->setUser(null);
            }
        }

        return $this;
    }

    public function getCodeSecurite(): ?string
    {
        return $this->codeSecurite;
    }

    public function setCodeSecurite(string $codeSecurite): self
    {
        $this->codeSecurite = $codeSecurite;

        return $this;
    }

    public function getTelephoneNumber(): ?int
    {
        return $this->telephoneNumber;
    }

    public function setTelephoneNumber(int $telephoneNumber): self
    {
        $this->telephoneNumber = $telephoneNumber;

        return $this;
    }
 

    
    
}
