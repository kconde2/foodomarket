<?php

declare(strict_types=1);

namespace App\Mercuriale\Domain\Model\Product\ValueObject;

use Doctrine\ORM\Mapping as ORM;
use Webmozart\Assert\Assert;

#[ORM\Embeddable]
final class ProductPrice implements \Stringable
{
    #[ORM\Column(name: 'price', type: 'decimal', precision: 7, scale: 2)]
    public readonly string $value;

    public function __construct(string $value)
    {
        Assert::greaterThan($value, 0, 'Price must be positive.');
        Assert::lessThan($value, 1000, 'Price must be less than 1000.');

        $this->value = $value;
    }

    public function __toString(): string
    {
        return $this->value;
    }
}
