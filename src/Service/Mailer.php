<?php

namespace App\Service;

use App\Entity\Participant;
use App\Entity\Sortie;
use Symfony\Component\Mailer\MailerInterface;
use Symfony\Component\Mime\Email;

class Mailer
{
    Private const ENVOYEUR = 'pigeon@pigeon.com';

    private function __construct(
        private MailerInterface $mailer

    ){}

    public function nouveauParticipant(Sortie $sortie, Participant $participant) :void{
        //Envoie à l'organisateur une notification de nouveau participant
        //todo créer fonction
        $message = new Email();
        $message->from(self::ENVOYEUR)
            ->to($sortie->getOrganisateur()->getEmail())
            ->subject('Nouveau participant')
            ->html();

        $this->mailer->send($message);
    }

    public function nouvelleSortie(Sortie $sortie):void{
        //Envoie un mail à l'organisateur d'une sortie nouvellement crée
        //todo créer fonction

    }

    public function annulationSortie(Sortie $sortie):void{
        //Envoie un mail d'annulation à tout les participants de la sortie
        //todo créer fonction

    }

    public function annulationParticipation(Sortie $sortie, Participant $participant):void{
        //Envoie une notification de désistement du participant à l'organisateur
        //todo créer fonction

    }


}