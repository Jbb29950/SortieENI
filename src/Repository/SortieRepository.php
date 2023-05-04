<?php

namespace App\Repository;

use App\Entity\Participant;
use App\Entity\Sortie;
use App\Filtre\FiltreAccueil;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\HttpFoundation\Request;

/**
 * @extends ServiceEntityRepository<Sortie>
 *
 * @method Sortie|null find($id, $lockMode = null, $lockVersion = null)
 * @method Sortie|null findOneBy(array $criteria, array $orderBy = null)
 * @method Sortie[]    findAll()
 * @method Sortie[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class SortieRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Sortie::class);
    }

    public function save(Sortie $entity, bool $flush = false): void
    {
        $this->getEntityManager()->persist($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

    public function remove(Sortie $entity, bool $flush = false): void
    {
        $this->getEntityManager()->remove($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

    public function trouverArchivable() : array{
        $date = new \DateTime('-1 month');

        $qb = $this->createQueryBuilder('s')
        ->andWhere('s.dateHeureDebut >= :date')
            ->setParameter('date', $date)
        ->andWhere('s.etat.libelle != Archivé');

        return $qb->getQuery()->getResult();
    }
    public function trouverFermable() : array{
        $date = new \DateTime();

        $qb = $this->createQueryBuilder('s')
            ->andWhere('s.dateHeureDebut > :date')
            ->setParameter('date', $date)
            ->andWhere('s.etat.libelle != Archivé');

        $query = $qb->getQuery();
        return $query->execute();
    }

    public function trouverAffichable(FiltreAccueil $filtre, ?Participant $participant) : array {

        $query = $this->createQueryBuilder('s');

        if (!empty($filtre->campus)){
            $query = $query
                ->andWhere('s.campus = :campus')
                ->setParameter('campus', $filtre->campus);
        }

        if (!empty($filtre->contient)){
            $query = $query
                ->andWhere('s.nom LIKE :contient')
                ->setParameter('contient', "%{$filtre->contient}%");
        }
        if (!empty($filtre->debutInterval)){
            $query = $query
                ->andWhere('s.dateHeureDebut > :debut')
                ->setParameter('debut', $filtre->debutInterval);
        }
        if (!empty($filtre->finInterval)){
            $query = $query
                ->andWhere('s.dateHeureDebut < :fin')
                ->setParameter('fin', $filtre->finInterval);
        }
        if(!is_null($participant)) {
            if (!$filtre->organisateur) {
                $query = $query
                    ->andWhere('s.organisateur != :actuel')
                    ->setParameter('actuel', $participant);
            }
            if (!$filtre->inscrit && $filtre->nonInscrit) {
                $query = $query
                    ->leftJoin('s.participants', 'participants');
            }

            if (!$filtre->nonInscrit) {
                $query = $query
                    ->leftJoin('s.participants', 'participants')
                    ->andWhere('participants.id LIKE :id')
                    ->setParameter('id', $participant->getId());
            }
        }
        if (!$filtre->passe){
            $query = $query
                ->andWhere('s.dateHeureDebut > :now')
                ->setParameter('now', new \DateTime());
        }

        $requete = $query->getQuery();

        return $requete->execute();
    }



//    /**
//     * @return Sortie[] Returns an array of Sortie objects
//     */
//    public function findByExampleField($value): array
//    {
//        return $this->createQueryBuilder('s')
//            ->andWhere('s.exampleField = :val')
//            ->setParameter('val', $value)
//            ->orderBy('s.id', 'ASC')
//            ->setMaxResults(10)
//            ->getQuery()
//            ->getResult()
//        ;
//    }

//    public function findOneBySomeField($value): ?Sortie
//    {
//        return $this->createQueryBuilder('s')
//            ->andWhere('s.exampleField = :val')
//            ->setParameter('val', $value)
//            ->getQuery()
//            ->getOneOrNullResult()
//        ;
//    }
}

