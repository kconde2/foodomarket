<?php

declare(strict_types=1);

namespace App\Mercuriale\Domain\Model\Product\Entity;

use App\Mercuriale\Domain\Model\Product\Enum\ProductStatus;
use App\Mercuriale\Domain\Model\Product\ValueObject\ProductPrice;
use App\Mercuriale\Domain\Model\Supplier\Entity\Supplier;
use App\Mercuriale\Domain\Trait\TimestampableTrait;
use App\Repository\ProductRepository;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: ProductRepository::class)]
#[ORM\HasLifecycleCallbacks]
class Product
{
    use TimestampableTrait;

    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(type: 'string', length: 20)]
    private string $status;

    public function __construct(
        #[ORM\Column(length: 6)]
        private string $code,

        #[ORM\Column(length: 255)]
        private string $description,

        #[ORM\Embedded(class: ProductPrice::class, columnPrefix: false)]
        private ProductPrice $price,

        #[ORM\ManyToOne(inversedBy: 'products')]
        #[ORM\JoinColumn(nullable: false)]
        private Supplier $supplier,

        #[ORM\Column(type: 'text', nullable: true)]
        private ?string $imageUrl = null,
    ) {
        $this->status = ProductStatus::Imported->value;
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getCode(): ?string
    {
        return $this->code;
    }

    public function getDescription(): ?string
    {
        return $this->description;
    }

    public function price(): ProductPrice
    {
        return $this->price;
    }

    public function getSupplier(): ?Supplier
    {
        return $this->supplier;
    }

    public function setSupplier(?Supplier $supplier): static
    {
        $this->supplier = $supplier;

        return $this;
    }

    public function getStatus(): string
    {
        return ProductStatus::from($this->status)->value;
    }

    public function setStatus(ProductStatus $status): void
    {
        $this->status = $status->value;
    }

    public function getMarking(): ?string
    {
        return $this->status;
    }

    public function setMarking(?string $marking): self
    {
        $this->status = $marking;

        return $this;
    }

    public function getImageUrl(): ?string
    {
        return $this->imageUrl;
    }

    public function setImageUrl(?string $imageUrl): static
    {
        $this->imageUrl = $imageUrl;

        return $this;
    }

    public function update(
        ?string $code = null,
        ?string $description = null,
        ?ProductPrice $price = null,
        ?string $status = null,
        ?string $imageUrl = null,
    ): void {
        $this->code = $code ?? $this->code;
        $this->description = $description ?? $this->description;
        $this->price = $price ?? $this->price;
        $this->status = $status ?? $this->status;
        $this->imageUrl = $imageUrl ?? $this->imageUrl;
    }
}
