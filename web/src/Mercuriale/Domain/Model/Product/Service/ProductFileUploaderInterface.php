<?php

declare(strict_types=1);

namespace App\Mercuriale\Domain\Model\Product\Service;

use Symfony\Component\HttpFoundation\File\UploadedFile;

interface ProductFileUploaderInterface
{
    public function upload(UploadedFile $file): string;
}
