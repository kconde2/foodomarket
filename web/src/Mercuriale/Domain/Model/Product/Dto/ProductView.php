<?php

declare(strict_types=1);

namespace App\Mercuriale\Domain\Model\Product\Dto;

use Symfony\Component\Validator\Constraints as Assert;

final class ProductView
{
    public function __construct(
        #[Assert\NotBlank(message: 'Product description cannot be empty.')]
        #[Assert\Length(
            max: 255,
            maxMessage: 'Product description cannot exceed {{ limit }} characters.'
        )]
        public readonly string $description,

        #[Assert\NotBlank(message: 'Product code cannot be empty.')]
        #[Assert\Length(
            max: 6,
            maxMessage: 'Product code cannot exceed {{ limit }} characters.'
        )]
        public readonly string $code,

        #[Assert\NotBlank(message: 'Product price cannot be empty.')]
        #[Assert\Positive(message: 'Product price must be a positive number.')]
        #[Assert\LessThan(
            value: 1000,
            message: 'Product price must be less than {{ compared_value }}.'
        )]
        public readonly string $price,
    ) {
    }
}
