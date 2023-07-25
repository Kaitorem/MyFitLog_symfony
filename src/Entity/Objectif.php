<?php

namespace App\Entity;

use App\Repository\ObjectifRepository;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: ObjectifRepository::class)]
class Objectif
{
    public function __construct()
    {
        $this->CreatedAt = new \DateTimeImmutable();
    }

    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\ManyToOne(inversedBy: 'objectifs')]
    #[ORM\JoinColumn(nullable: false)]
    private ?Domaine $domaine = null;

    #[ORM\ManyToOne(inversedBy: 'objectifs')]
    #[ORM\JoinColumn(nullable: false)]
    private ?Libelle $Libelle = null;

    #[ORM\Column(length: 100)]
    private ?string $valeur = null;

    #[ORM\Column]
    private ?\DateTimeImmutable $CreatedAt = null;

    #[ORM\ManyToOne(inversedBy: 'objectifs')]
    #[ORM\JoinColumn(nullable: false)]
    private ?User $user = null;

    #[ORM\Column(nullable:true)]
    private ?\DateTimeImmutable $endAt = null;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getLibelle(): ?Libelle
    {
        return $this->Libelle;
    }

    public function setLibelle(?Libelle $Libelle): self
    {
        $this->Libelle = $Libelle;

        return $this;
    }

    public function getDomaine(): ?Domaine
    {
        return $this->domaine;
    }

    public function setDomaine(?Domaine $domaine): self
    {
        $this->domaine = $domaine;

        return $this;
    }

    public function getValeur(): ?string
    {
        return $this->valeur;
    }

    public function setValeur(string $valeur): self
    {
        $this->valeur = $valeur;

        return $this;
    }

    public function getCreatedAt(): ?\DateTimeImmutable
    {
        return $this->CreatedAt;
    }

    public function setCreatedAt(\DateTimeImmutable $CreatedAt): self
    {
        $this->CreatedAt = $CreatedAt;

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
     * Set the value of id
     *
     * @return  self
     */
    public function setId($id)
    {
        $this->id = $id;

        return $this;
    }

    public function getEndAt(): ?\DateTimeImmutable
    {
        return $this->endAt;
    }

    public function setEndAt(\DateTimeImmutable $endAt): self
    {
        $this->endAt = $endAt;

        return $this;
    }
}
