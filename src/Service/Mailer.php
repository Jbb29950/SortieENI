<?php

namespace App\Service;

use App\Entity\Participant;
use App\Entity\Sortie;
use phpDocumentor\Reflection\Types\Context;
use Symfony\Bridge\Twig\Mime\TemplatedEmail;
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

        //if ($participant->getPseudo()){
        //    $inscrit = $participant->getPseudo();
        //}else{
        //    $inscrit = $participant->getNom();
        //}
        $message = new TemplatedEmail();
        $message->from(self::ENVOYEUR)
            ->to($sortie->getOrganisateur()->getEmail())
            ->subject('Nouveau participant')
            ->htmlTemplate('EmailTemplate/nouveauParticipant.html.twig')
            ->context([
                'participant' => $participant,
                'sortie' => $sortie
            ]);

        $this->mailer->send($message);
    }

    public function nouvelleSortie(Sortie $sortie):void{
        //Envoie un mail à l'organisateur d'une sortie nouvellement crée
        //todo créer fonction
        $message = new TemplatedEmail();
        $message->from(self::ENVOYEUR)
            ->to($sortie->getOrganisateur()->getEmail())
            ->subject('Nouvelle Sortie')
            ->htmlTemplate('EmailTemplate/nouvelleSortie.html.twig')
            ->context([
                'sortie' => $sortie
            ]);

        $this->mailer->send($message);
    }

    public function annulationSortie(Sortie $sortie):void{
        //Envoie un mail d'annulation à tout les participants de la sortie
        //todo créer fonction

        foreach ($sortie->getParticipants() as $participant ) {
            $message = new TemplatedEmail();
            $message->from(self::ENVOYEUR)
                ->to($participant->getEmail())
                ->subject('Annulation de sortie')
                ->htmlTemplate('EmailTemplate/annulationSortie.html.twig')
                ->context([
                    'sortie' => $sortie
                ]);
        }
        $this->mailer->send($message);

    }

    public function annulationParticipation(Sortie $sortie, Participant $participant):void{
        //Envoie une notification de désistement du participant à l'organisateur
        //todo créer fonction
        $message = new TemplatedEmail();
        $message->from(self::ENVOYEUR)
            ->to($sortie->getOrganisateur()->getEmail())
            ->subject('Annulation Participant')
            ->htmlTemplate('EmailTemplate/annulationParticipant.html.twig')
            ->context([
                'participant' => $participant,
                'sortie' => $sortie
            ]);

        $this->mailer->send($message);

    }


}