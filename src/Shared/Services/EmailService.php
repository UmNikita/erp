<?php

namespace App\Shared\Services;

use Symfony\Bridge\Twig\Mime\TemplatedEmail;
use Symfony\Component\Mailer\MailerInterface;
use Symfony\Component\Mime\Address;

class EmailService
{
    public function __construct(
        private MailerInterface $mailer,
        private string $fromAddress,
        private string $fromName,
    ) {}

    public function sendTemplate(string $title, string $emailTo, string $template, array $context): void {
        $email = (new TemplatedEmail())
        ->from(
            new Address(
                $this->fromAddress,
                $this->fromName
            )
        )
        ->to($emailTo)
        ->subject($title)
        ->htmlTemplate($template)
        ->context($context);

        $this->mailer->send($email);
    }
}