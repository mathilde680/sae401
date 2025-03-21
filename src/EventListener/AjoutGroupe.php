<?php

namespace App\EventListener;

use App\Event\GroupeEvent;
use App\Service\MailerService;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;

class AjoutGroupe implements EventSubscriberInterface
{
    public function __construct(
        protected MailerService $mailerService
    )
    {
    }

    public static function getSubscribedEvents()
    {
        return [
            GroupeEvent::ADDED => 'onGroupeAdded',
        ];
    }

    public function onGroupeAdded(GroupeEvent $event)
    {
        $groupe = $event->getGroupe();

        $this->mailerService->sendTemplatedMail(
            'admin@mmiple.fr',
            'Nouveau groupe ajouté',
            'groupe_added.html.twig',
            ['groupe' => $groupe]
        );
    }
}