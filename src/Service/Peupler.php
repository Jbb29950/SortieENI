<?php

namespace App\Service;

use App\Entity\Participant;
use App\Repository\ParticipantRepository;

class Peupler
{
    public function __construct(private ParticipantRepository $pr){}
    public function recupParticipantParId( $id) : Participant{
        $participant = $this->pr->findOneBy(['id' => $id]);
        return $participant;
    }
}