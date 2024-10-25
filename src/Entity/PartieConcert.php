<?php

namespace App\Entity;

use ApiPlatform\Metadata\ApiResource;
use ApiPlatform\Metadata\Delete;
use ApiPlatform\Metadata\Get;
use ApiPlatform\Metadata\GetCollection;
use ApiPlatform\Metadata\Patch;
use ApiPlatform\Metadata\Post;
use App\Repository\PartieConcertRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

#[ORM\Entity(repositoryClass: PartieConcertRepository::class)]
#[ApiResource(
    operations: [
        new GetCollection(),
        new Get(),
        new Post(
            security: "is_granted('PARTIE_CONCERT_EDIT', object) and object == user",
            validationContext: ['groups' => ['partie_concert:create']]
        ),
        new Patch(
            security: "is_granted('PARTIE_CONCERT_EDIT', object) and object == user",
            validationContext: ['groups' => ['partie_concert:update']]
        ),
        new Delete(
            security: "is_granted('PARTIE_CONCERT_DELETE', object) and object == user"
        )
    ]
)]
class PartieConcert
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 255)]
    #[Assert\NotBlank(groups: ['partie_concert:create'])]
    #[Assert\NotNull(groups: ['partie_concert:create'])]
    private ?string $nom = null;

    #[ORM\Column]
    #[Assert\NotNull(groups: ['partie_concert:create'])]
    #[Assert\Type(type: 'bool', groups: ['partie_concert:create'])]
    private ?bool $artistePrincipal = null;

    #[ORM\Column(type: Types::DATETIME_MUTABLE)]
    #[Assert\NotBlank(groups: ['partie_concert:create'])]
    #[Assert\NotNull(groups: ['partie_concert:create'])]
    #[Assert\DateTime(groups: ['partie_concert:create'])]
    private ?\DateTimeInterface $dateDeDebut = null;

    #[ORM\Column(type: Types::DATETIME_MUTABLE)]
    #[Assert\NotBlank(groups: ['partie_concert:create'])]
    #[Assert\NotNull(groups: ['partie_concert:create'])]
    #[Assert\DateTime(groups: ['partie_concert:create'])]
    private ?\DateTimeInterface $dateDeFin = null;

    /**
     * @var Collection<int, Scene>
     */
    #[ORM\OneToMany(targetEntity: Scene::class, mappedBy: 'partieConcerts')]
    private Collection $scenes;

    #[ORM\ManyToOne(inversedBy: 'partieConcerts')]
    #[ORM\JoinColumn(nullable: false)]
    private ?User $artiste = null;

    public function __construct()
    {
        $this->scenes = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getNom(): ?string
    {
        return $this->nom;
    }

    public function setNom(string $nom): static
    {
        $this->nom = $nom;

        return $this;
    }

    public function isArtistePrincipal(): ?bool
    {
        return $this->artistePrincipal;
    }

    public function setArtistePrincipal(bool $artistePrincipal): static
    {
        $this->artistePrincipal = $artistePrincipal;

        return $this;
    }

    public function getDateDeDebut(): ?\DateTimeInterface
    {
        return $this->dateDeDebut;
    }

    public function setDateDeDebut(\DateTimeInterface $dateDeDebut): static
    {
        $this->dateDeDebut = $dateDeDebut;

        return $this;
    }

    public function getDateDeFin(): ?\DateTimeInterface
    {
        return $this->dateDeFin;
    }

    public function setDateDeFin(\DateTimeInterface $dateDeFin): static
    {
        $this->dateDeFin = $dateDeFin;

        return $this;
    }

    /**
     * @return Collection<int, Scene>
     */
    public function getScenes(): Collection
    {
        return $this->scenes;
    }

    public function addScene(Scene $scene): static
    {
        if (!$this->scenes->contains($scene)) {
            $this->scenes->add($scene);
            $scene->setPartieConcerts($this);
        }

        return $this;
    }

    public function removeScene(Scene $scene): static
    {
        if ($this->scenes->removeElement($scene)) {
            // set the owning side to null (unless already changed)
            if ($scene->getPartieConcerts() === $this) {
                $scene->setPartieConcerts(null);
            }
        }

        return $this;
    }

    public function getArtiste(): ?User
    {
        return $this->artiste;
    }

    public function setArtiste(?User $artiste): static
    {
        $this->artiste = $artiste;

        return $this;
    }
}
