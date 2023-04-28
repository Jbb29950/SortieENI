<?php

namespace App\Command;

use App\Entity\Sortie;
use App\Repository\EtatRepository;
use App\Repository\SortieRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

#[AsCommand(
    name: 'ActualisationSortie',
    description: 'Archive et ferme les sortie visées',
)]
class ActualisationSortieCommand extends Command
{
    public function __construct(
        private SortieRepository $sortieRepository,
        private EtatRepository $etatRepository
    )
    {
        parent::__construct();
    }

    protected function configure(): void
    {
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        //Archive les sorties
        $sortie = new Sortie();
        $archivables = $this->sortieRepository->trouverArchivable();
        $archive = $this->etatRepository->findOneBy(['id'=>'5']);
        foreach ($archivables as $sortie){
            $sortie->setEtat($archive);
        }
        $fermable = $this->sortieRepository->trouverFermable();$output->write('6');
        $ferme = $this->etatRepository->findOneBy(['id'=>'4']);
        foreach ($fermable as $sortie){
            $sortie->setEtat($ferme);
        }



        return Command::SUCCESS;
    }
}
