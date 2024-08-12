<?php

declare(strict_types=1);

namespace App\EventListener;

use App\Event\ProductImportedEvent;
use App\Mercuriale\Domain\Service\MailerServiceInterface;
use Symfony\Component\EventDispatcher\Attribute\AsEventListener;
use Symfony\Contracts\Translation\TranslatorInterface;

#[AsEventListener]
final class ProductImportedEventListener
{
    public function __construct(
        private MailerServiceInterface $mailerService,
        private TranslatorInterface $translator
    ) {
    }

    public function __invoke(ProductImportedEvent $event): void
    {
        $subject = $this->translator->trans('mail.product.imported.subject');
        $template = 'emails/product/product-imported.html.twig';
        $context = [
            'importStatus' => $event->importStatus->value,
            'supplierName' => $event->supplierName,
            'errors' => $event->productImportView->errors,
        ];

        $this->mailerService->send('admin@foodomarket.com', $subject, $template, $context);
    }
}
