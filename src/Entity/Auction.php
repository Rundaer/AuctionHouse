<?php

namespace App\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Gedmo\Mapping\Annotation as Gedmo;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;
/**
 * @ORM\Table(name="auctions")
 * @ORM\Entity(repositoryClass="App\Repository\AuctionRepository")
 */
class Auction
{

    const STATUS_ACTIVE = 'active';
    const STATUS_FINISHED = 'finished';
    const STATUS_CANCELLED = 'cancelled';
    /**
     * @ORM\Id()
     * @ORM\GeneratedValue()
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\Column(type="string", length=255)
     * @Assert\NotBlank(
     *      message="Tytuł nie może być pusty!"
     * )
     * @Assert\Length(
     *      min=3,
     *      max=255,
     *      minMessage="Tytuł musi mieć więcej niż 3 znaki",
     *      maxMessage="Tytuł nie moze miec więcej jak 255 znaków"
     * )
     */
    private $title;

    /**
     * @ORM\Column(type="string", length=255)
     * @Assert\NotBlank(
     *      message="Opis nie może być pusty!"
     * )
     * @Assert\Length(
     *      min=10,
     *      minMessage="Opis musi mieć więcej niż 10 znaki"
     * )
     */
    private $description;

    /**
     * @ORM\Column(type="decimal", precision=10, scale=2)
     * @Assert\NotBlank(
     *      message="Cena nie może być pusta!"
     * )
     * @Assert\GreaterThan(
     *      value="0",
     *      message="Cena musi być większa od zera!"
     * )
     */
    private $price;

    /**
     * @ORM\Column(type="decimal", precision=10, scale=2)
     * @Assert\NotBlank(
     *      message="Cena wywoławcza nie może być pusta!"
     * )
     * @Assert\GreaterThan(
     *      value="0",
     *      message="Cena wywoławcza musi być większa od zera!"
     * )
     */
    private $startingPrice;

    /**
     * @Gedmo\Timestampable(on="create")
     * @ORM\Column(type="datetime")
     */
    private $createdAt;

    /**
     * @Gedmo\Timestampable(on="update")
     * @ORM\Column(type="datetime")
     */
    private $updatedAt;

    /**
     * @ORM\Column(type="datetime")
     * @Assert\NotBlank(
     *      message="Musisz podać datę zakończenia aukcji!"
     * )
     * @Assert\GreaterThan(
     *      value="+1 day",
     *      message="Aukcja nie może kończyć się za mniej niż 24 godziny"
     * )
     */
    private $expiresAt;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $status;

    /**
     * @ORM\OneToMany(targetEntity="App\Entity\Offer", mappedBy="auction")
     */
    private $offers;

    /**
     * @ORM\ManyToOne(targetEntity=User::class, inversedBy="auctions")
     * @ORM\JoinColumn(nullable=false)
     */
    private $owner;

    public function __construct()
    {
        $this->offers = new ArrayCollection();
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

    public function getDescription(): ?string
    {
        return $this->description;
    }

    public function setDescription(string $description): self
    {
        $this->description = $description;

        return $this;
    }

    public function getPrice(): ?string
    {
        return $this->price;
    }

    public function setPrice(string $price): self
    {
        $this->price = $price;

        return $this;
    }

    public function getStartingPrice(): ?string
    {
        return $this->startingPrice;
    }

    public function setStartingPrice(string $startingPrice): self
    {
        $this->startingPrice = $startingPrice;

        return $this;
    }

    public function getCreatedAt(): ?\DateTimeInterface
    {
        return $this->createdAt;
    }

    public function setCreatedAt(\DateTimeInterface $createdAt): self
    {
        $this->createdAt = $createdAt;

        return $this;
    }

    public function getUpdatedAt(): ?\DateTimeInterface
    {
        return $this->updatedAt;
    }

    public function setUpdatedAt(\DateTimeInterface $updatedAt): self
    {
        $this->updatedAt = $updatedAt;

        return $this;
    }

    public function getExpiresAt(): ?\DateTimeInterface
    {
        return $this->expiresAt;
    }

    public function setExpiresAt(\DateTimeInterface $expiresAt): self
    {
        $this->expiresAt = $expiresAt;

        return $this;
    }

    public function getStatus(): ?string
    {
        return $this->status;
    }

    public function setStatus(string $status): self
    {
        $this->status = $status;

        return $this;
    }

    /**
     * @return Collection|Offer[]
     */
    public function getOffers(): Collection
    {
        return $this->offers;
    }

    public function addOffer(Offer $offer): self
    {
        if (!$this->offers->contains($offer)) {
            $this->offers[] = $offer;
            $offer->setAuction($this);
        }

        return $this;
    }

    public function removeOffer(Offer $offer): self
    {
        if ($this->offers->contains($offer)) {
            $this->offers->removeElement($offer);
            // set the owning side to null (unless already changed)
            if ($offer->getAuction() === $this) {
                $offer->setAuction(null);
            }
        }

        return $this;
    }

    public function getOwner(): ?User
    {
        return $this->owner;
    }

    public function setOwner(?User $owner): self
    {
        $this->owner = $owner;

        return $this;
    }
}
