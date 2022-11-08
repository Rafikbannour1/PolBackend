<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;
use App\Repository\RateRepository;
use ApiPlatform\Core\Annotation\ApiResource;

/**
 * @ORM\Entity(repositoryClass=RateRepository::class)
 * @ApiResource(formats={"json"}) 
 */
class Rate
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\Column(type="integer")
     */
    private $rateValue;

    /**
     * @ORM\ManyToOne(targetEntity=groupe::class, inversedBy="rates")
     * @ORM\JoinColumn(nullable=false)
     */
    private $cours;

    /**
     * @ORM\ManyToOne(targetEntity=User::class, inversedBy="rates")
     * @ORM\JoinColumn(nullable=false)
     */
    private $user;

    /**
     * @ORM\Column(type="string", length=600)
     */
    private $message;

    public function getId(): ?int
    {
        return $this->id;
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

    public function getCours(): ?groupe
    {
        return $this->cours;
    }

    public function setCours(?groupe $cours): self
    {
        $this->cours = $cours;

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

    public function getMessage(): ?string
    {
        return $this->message;
    }

    public function setMessage(string $message): self
    {
        $this->message = $message;

        return $this;
    }

    public function toArray() 
    {
        return ['id'=>$this->id,'messages'=>$this->message,'writer'=>$this->user] ; 
    }
}
