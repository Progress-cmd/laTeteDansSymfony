<?php

namespace App\Entity;

use App\Repository\OeuvreRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: OeuvreRepository::class)]
class Oeuvre
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 255)]
    private ?string $titre = null;

    #[ORM\Column(type: Types::DATE_MUTABLE)]
    private ?\DateTime $date_creation = null;

    #[ORM\Column(type: Types::TEXT, nullable: true)]
    private ?string $description = null;

    #[ORM\Column(nullable: true)]
    private ?bool $est_detruite = null;

    #[ORM\OneToOne(targetEntity: self::class, cascade: ['persist', 'remove'])]
    private ?self $oeuvre_recouverte = null;

    /**
     * @var Collection<int, Photo>
     */
    #[ORM\OneToMany(targetEntity: Photo::class, mappedBy: 'oeuvre')]
    private Collection $photos;

    #[ORM\ManyToOne(inversedBy: 'oeuvres')]
    private ?Lieux $lieux = null;

    /**
     * @var Collection<int, Artistes>
     */
    #[ORM\ManyToMany(targetEntity: Artistes::class, inversedBy: 'oeuvres')]
    private Collection $artistes;

    /**
     * @var Collection<int, Techniques>
     */
    #[ORM\ManyToMany(targetEntity: Techniques::class, inversedBy: 'oeuvres')]
    private Collection $techniques;

    public function __construct()
    {
        $this->photos = new ArrayCollection();
        $this->artistes = new ArrayCollection();
        $this->techniques = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getTitre(): ?string
    {
        return $this->titre;
    }

    public function setTitre(string $titre): static
    {
        $this->titre = $titre;

        return $this;
    }

    public function getDateCreation(): ?\DateTime
    {
        return $this->date_creation;
    }

    public function setDateCreation(\DateTime $date_creation): static
    {
        $this->date_creation = $date_creation;

        return $this;
    }

    public function getDescription(): ?string
    {
        return $this->description;
    }

    public function setDescription(?string $description): static
    {
        $this->description = $description;

        return $this;
    }

    public function isEstDetruite(): ?bool
    {
        return $this->est_detruite;
    }

    public function setEstDetruite(?bool $est_detruite): static
    {
        $this->est_detruite = $est_detruite;

        return $this;
    }

    public function getOeuvreRecouverte(): ?self
    {
        return $this->oeuvre_recouverte;
    }

    public function setOeuvreRecouverte(?self $oeuvre_recouverte): static
    {
        $this->oeuvre_recouverte = $oeuvre_recouverte;

        return $this;
    }

    /**
     * @return Collection<int, Photo>
     */
    public function getPhotos(): Collection
    {
        return $this->photos;
    }

    public function addPhoto(Photo $photo): static
    {
        if (!$this->photos->contains($photo)) {
            $this->photos->add($photo);
            $photo->setOeuvre($this);
        }

        return $this;
    }

    public function removePhoto(Photo $photo): static
    {
        if ($this->photos->removeElement($photo)) {
            // set the owning side to null (unless already changed)
            if ($photo->getOeuvre() === $this) {
                $photo->setOeuvre(null);
            }
        }

        return $this;
    }

    public function getLieux(): ?Lieux
    {
        return $this->lieux;
    }

    public function setLieux(?Lieux $lieux): static
    {
        $this->lieux = $lieux;

        return $this;
    }

    /**
     * @return Collection<int, Artistes>
     */
    public function getArtistes(): Collection
    {
        return $this->artistes;
    }

    public function addArtiste(Artistes $artiste): static
    {
        if (!$this->artistes->contains($artiste)) {
            $this->artistes->add($artiste);
        }

        return $this;
    }

    public function removeArtiste(Artistes $artiste): static
    {
        $this->artistes->removeElement($artiste);

        return $this;
    }

    /**
     * @return Collection<int, Techniques>
     */
    public function getTechniques(): Collection
    {
        return $this->techniques;
    }

    public function addTechnique(Techniques $technique): static
    {
        if (!$this->techniques->contains($technique)) {
            $this->techniques->add($technique);
        }

        return $this;
    }

    public function removeTechnique(Techniques $technique): static
    {
        $this->techniques->removeElement($technique);

        return $this;
    }
}
