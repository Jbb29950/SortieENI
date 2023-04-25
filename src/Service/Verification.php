<?php

namespace App\Service;

use App\Entity\Participant;
use App\Repository\ParticipantRepository;

class Verification
{
    private function __construct(private ParticipantRepository $pr){}

    public function verifValidParticipant(Participant $participant):array
    {
        $err =array() ;
        if(strlen($participant->getTelephone())<9 && strlen($participant->getTelephone())>12){
            $err[] = 'Numéro de téléphone incorrecte';
        }
        if($this->pr->findOneBy(['email' => $participant->getEmail()])){
            $err[] = 'Un compte avec cet email existe déjà';
        }
        if($this->pr->findOneBy(['pseudo' => $participant->getPseudo()])){
            $err[] = 'Un compte avec ce pseudo existe déjà';
        }
        return $err;
    }

    public function verifValidSortie():array{
        $err =array() ;
        return $err;
    }


}