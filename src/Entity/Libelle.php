<?php

namespace App\Entity;

use App\Repository\LibelleRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: LibelleRepository::class)]
class Libelle
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 100)]
    private ?string $label = null;

    #[ORM\Column(length: 50)]
    private ?string $unit = null;

    #[ORM\OneToMany(mappedBy: 'Libelle', targetEntity: Objectif::class)]
    private Collection $objectifs;

    #[ORM\ManyToOne(inversedBy: 'libelles')]
    #[ORM\JoinColumn(nullable: false)]
    private ?Domaine $domaine = null;

    #[ORM\OneToMany(mappedBy: 'libelle', targetEntity: Historique::class)]
    private Collection $historiques;

    public function __construct()
    {
        $this->objectifs = new ArrayCollection();
        $this->historiques = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getLabel(): ?string
    {
        return $this->label;
    }

    public function setLabel(string $label): self
    {
        $this->label = $label;

        return $this;
    }

    public function getUnit(): ?string
    {
        return $this->unit;
    }

    public function setUnit(string $unit): self
    {
        $this->unit = $unit;

        return $this;
    }

    /**
     * @return Collection<int, Objectif>
     */
    public function getObjectifs(): Collection
    {
        return $this->objectifs;
    }

    public function addObjectif(Objectif $objectif): self
    {
        if (!$this->objectifs->contains($objectif)) {
            $this->objectifs->add($objectif);
            $objectif->setLibelle($this);
        }

        return $this;
    }

    public function removeObjectif(Objectif $objectif): self
    {
        if ($this->objectifs->removeElement($objectif)) {
            // set the owning side to null (unless already changed)
            if ($objectif->getLibelle() === $this) {
                $objectif->setLibelle(null);
            }
        }

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

    /**
     * @return Collection<int, Historique>
     */
    public function getHistoriques(): Collection
    {
        return $this->historiques;
    }

    public function addHistorique(Historique $historique): self
    {
        if (!$this->historiques->contains($historique)) {
            $this->historiques->add($historique);
            $historique->setLibelle($this);
        }

        return $this;
    }

    public function removeHistorique(Historique $historique): self
    {
        if ($this->historiques->removeElement($historique)) {
            // set the owning side to null (unless already changed)
            if ($historique->getLibelle() === $this) {
                $historique->setLibelle(null);
            }
        }

        return $this;
    }

    public function __toString()
    {
        return $this->label;
    }
}
