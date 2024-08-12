<?php

declare(strict_types=1);

namespace App\Mercuriale\Domain\Service;

interface MailerServiceInterface
{
    public function send(string $to, string $subject, string $template, array $context = []): void;
}
