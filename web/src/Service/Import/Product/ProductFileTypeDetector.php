<?php

declare(strict_types=1);

namespace App\Service\Import\Product;

class ProductFileTypeDetector
{
    /**
     * Detects the file type based on its extension.
     *
     * @param string $filePath the path to the file
     *
     * @return string the detected file type
     *
     * @throws \InvalidArgumentException if the file type is unsupported or the file does not exist
     */
    public function detect(string $filePath): string
    {
        if (!file_exists($filePath)) {
            throw new \InvalidArgumentException("File does not exist: $filePath");
        }

        $extension = strtolower(pathinfo($filePath, \PATHINFO_EXTENSION));

        return match ($extension) {
            'csv' => 'csv',
            'json' => 'json',
            'xml' => 'xml',
            default => throw new \InvalidArgumentException("Unsupported file type: $extension"),
        };
    }
}
