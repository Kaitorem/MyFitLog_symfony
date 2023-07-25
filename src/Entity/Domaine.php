<?php

namespace App\Entity;

use App\Repository\DomaineRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: DomaineRepository::class)]
class Domaine
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 100)]
    private ?string $libelle = null;

    #[ORM\OneToMany(mappedBy: 'domaine', targetEntity: Objectif::class)]
    private Collection $objectifs;

    #[ORM\OneToMany(mappedBy: 'domaine', targetEntity: Libelle::class)]
    private Collection $libelles;

    #[ORM\OneToMany(mappedBy: 'domaine', targetEntity: Historique::class)]
    private Collection $historiques;

    public function __construct()
    {
        $this->objectifs = new ArrayCollection();
        $this->libelles = new ArrayCollection();
        $this->historiques = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getLibelle(): ?string
    {
        return $this->libelle;
    }

    public function setLibelle(string $libelle): self
    {
        $this->libelle = $libelle;

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
            $objectif->setDomaine($this);
        }

        return $this;
    }

    public function removeObjectif(Objectif $objectif): self
    {
        if ($this->objectifs->removeElement($objectif)) {
            // set the owning side to null (unless already changed)
            if ($objectif->getDomaine() === $this) {
                $objectif->setDomaine(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection<int, Libelle>
     */
    public function getLibelles(): Collection
    {
        return $this->libelles;
    }

    public function addLibelle(Libelle $libelle): self
    {
        if (!$this->libelles->contains($libelle)) {
            $this->libelles->add($libelle);
            $libelle->setDomaine($this);
        }

        return $this;
    }

    public function removeLibelle(Libelle $libelle): self
    {
        if ($this->libelles->removeElement($libelle)) {
            // set the owning side to null (unless already changed)
            if ($libelle->getDomaine() === $this) {
                $libelle->setDomaine(null);
            }
        }

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
            $historique->setDomaine($this);
        }

        return $this;
    }

    public function removeHistorique(Historique $historique): self
    {
        if ($this->historiques->removeElement($historique)) {
            // set the owning side to null (unless already changed)
            if ($historique->getDomaine() === $this) {
                $historique->setDomaine(null);
            }
        }

        return $this;
    }

    public function __toString()
    {
        return $this->libelle;
    }
}
