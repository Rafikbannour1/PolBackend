<?php

namespace App\Entity;

use App\Repository\FormationFilesRepository;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass=FormationFilesRepository::class)
 */
class FormationFiles
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
    private $name;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $description;

    /**
     * @ORM\ManyToOne(targetEntity=DocumentGroupe::class, inversedBy="formationFiles")
     * @ORM\JoinColumn(nullable=false)
     */
    private $documentGroupe;

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

    public function getDocumentGroupe(): ?DocumentGroupe
    {
        return $this->documentGroupe;
    }

    public function setDocumentGroupe(?DocumentGroupe $documentGroupe): self
    {
        $this->documentGroupe = $documentGroupe;

        return $this;
    }
}
