<?php
namespace App\Filtre;



use App\Entity\Campus;

class FiltreAccueil{

    /**
     * @var Campus
     */
    public $campus;

    /**
     * @var string
     */
    public $contient = '';

    /**
     * @var \DateTime
     */
    public $debutInterval;

    /**
     * @var \DateTime
     */
    public $finInterval;

    /**
     * @var bool
     */
    public $organisateur = true;

    /**
     * @var bool
     */
    public $inscrit = true;

    /**
     * @var bool
     */
    public $nonInscrit = true;

    /**
     * @var bool
     */
    public $passe = false;
}