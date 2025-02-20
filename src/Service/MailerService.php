<?php

namespace App\Service;

use Symfony\Bridge\Twig\Mime\TemplatedEmail;
use Symfony\Component\Mailer\MailerInterface;
use Symfony\Component\Mime\Email;

class MailerService
{
    public function __construct(
        private MailerInterface $mailer,
    ){}

    public function sendMail(string $expediteur, string $sujet, string $message): void
    {
        $email = (new Email())
            ->from('contact@mmiple.fr')
            ->replyTo($expediteur)
            ->to('administrateur@mmiple.fr')
            ->subject($sujet)
            ->text($message);

        $this->mailer->send($email);
    }

    public function sendTemplatedMail(string $destinataire, string $sujet, string $template, array $data = []): void
    {
        $email = (new TemplatedEmail())
            ->from('contact@mmiple.fr')
            ->to($destinataire)
            ->subject($sujet)
            ->htmlTemplate('mailer/'.$template)
            ->context($data);

        $this->mailer->send($email);
    }
}