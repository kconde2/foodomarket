<?php

declare(strict_types=1);

namespace App\Service\Mailer;

use App\Mercuriale\Domain\Service\MailerServiceInterface;
use Psr\Log\LoggerInterface;
use Symfony\Bridge\Twig\Mime\TemplatedEmail;
use Symfony\Component\DependencyInjection\ParameterBag\ParameterBagInterface;
use Symfony\Component\Mailer\Exception\TransportExceptionInterface;
use Symfony\Component\Mailer\MailerInterface;
use Symfony\Component\Mime\Address;

/**
 * class MailerService.
 */
class MailerService implements MailerServiceInterface
{
    /**
     * @return void
     */
    public function __construct(
        private MailerInterface $mailer,
        private ParameterBagInterface $params,
        private LoggerInterface $logger
    ) {
    }

    public function send(string $to, string $subject, string $template, array $context = []): void
    {
        $email = (new TemplatedEmail())
            ->from($this->params->get('app.email_from'))
            ->subject($subject)
            ->to(new Address($to))
            ->htmlTemplate($template)
            ->context($context);

        try {
            $this->mailer->send($email);
        } catch (TransportExceptionInterface $exception) {
            // some error prevented the email sending; display an
            // error message or try to resend the message
            $this->logger->warning('An error occured when sending mail', [
                'subject' => $subject,
                'to' => $to,
                'exception' => $exception,
            ]);
        }
    }
}
