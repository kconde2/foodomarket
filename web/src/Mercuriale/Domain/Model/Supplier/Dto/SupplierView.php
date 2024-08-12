<?php

declare(strict_types=1);

namespace App\Mercuriale\Domain\Model\Product\Dto;

use Symfony\Component\Validator\Constraints as Assert;

final class SupplierView
{
    public function __construct(
        #[Assert\NotBlank(message: 'The supplier name cannot be empty.')]
        #[Assert\Length(
            min: 3,
            minMessage: 'The supplier name must be at least {{ limit }} characters long.',
            max: 255,
            maxMessage: 'The supplier name cannot exceed {{ limit }} characters.'
        )]
        public readonly string $name,
    ) {
    }
}
