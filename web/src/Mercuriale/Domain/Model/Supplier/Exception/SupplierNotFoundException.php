<?php

declare(strict_types=1);

namespace App\Mercuriale\Domain\Model\Supplier\Exception;

final class SupplierNotFoundException extends \RuntimeException
{
    public function __construct(string $name, int $code = 0, ?\Throwable $previous = null)
    {
        parent::__construct(\sprintf('Cannot find supplier with name %s', (string) $name), $code, $previous);
    }
}
