<?php

declare(strict_types=1);

namespace App\Service\Import\Product;

use App\Mercuriale\Domain\Model\Product\Service\ProductFileUploaderInterface;
use Symfony\Component\DependencyInjection\ParameterBag\ParameterBagInterface;
use Symfony\Component\HttpFoundation\File\Exception\FileException;
use Symfony\Component\HttpFoundation\File\UploadedFile;

final class ProductFileUploader implements ProductFileUploaderInterface
{
    private string $uploadsDirectory;

    public function __construct(ParameterBagInterface $params)
    {
        $this->uploadsDirectory = $params->get('product_upload_directory');
    }

    public function upload(UploadedFile $file): string
    {
        $originalFilename = pathinfo($file->getClientOriginalName(), \PATHINFO_FILENAME);
        $newFilename = uniqid().'-'.str_replace(' ', '_', $originalFilename).'.'.$file->guessExtension();

        try {
            $file->move($this->uploadsDirectory, $newFilename);
        } catch (FileException $e) {
            throw new \RuntimeException('An error occurred while uploading the file.');
        }

        return $this->uploadsDirectory.'/'.$newFilename;
    }
}
