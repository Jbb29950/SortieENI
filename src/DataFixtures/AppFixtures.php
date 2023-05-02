<?php

namespace App\DataFixtures;


use App\Entity\Campus;
use App\Entity\Etat;
use App\Entity\Lieu;
use App\Entity\Participant;
use App\Entity\Sortie;
use App\Entity\Ville;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;
use Symfony\Component\PasswordHasher\Hasher\PasswordHasherFactory;


class AppFixtures extends Fixture
{

    public function load(ObjectManager $manager): void
    {

        //Chargement du PasswordHasher
        $factory = new PasswordHasherFactory([
            'auto'=>['algorithm'=>'auto']
        ]);
        $hasher = $factory->getPasswordHasher('auto');
        
        //ALTER TABLE tablename AUTO_INCREMENT = 1
        $conn = $manager->getConnection();

        $sql = 'ALTER TABLE ville AUTO_INCREMENT = 1';
        $conn->prepare($sql)->executeQuery();

        $sql = 'ALTER TABLE lieu AUTO_INCREMENT = 1';
        $conn->prepare($sql)->executeQuery();

        $sql = 'ALTER TABLE campus AUTO_INCREMENT = 1';
        $conn->prepare($sql)->executeQuery();

        $sql = 'ALTER TABLE etat AUTO_INCREMENT = 1';
        $conn->prepare($sql)->executeQuery();

        $sql = 'ALTER TABLE sortie AUTO_INCREMENT = 1';
        $conn->prepare($sql)->executeQuery();

        $sql = 'ALTER TABLE participant AUTO_INCREMENT = 1';
        $conn->prepare($sql)->executeQuery();

        $sql = 'ALTER TABLE participant_sortie AUTO_INCREMENT = 1';
        $conn->prepare($sql)->executeQuery();


        //Création des états
        $enCreation = new Etat();
        $enCreation->setLibelle('En création');
        $manager->persist($enCreation);

        $ouvert = new Etat();
        $ouvert->setLibelle('Ouvert');
        $manager->persist($ouvert);

        $enCours = new Etat();
        $enCours->setLibelle('En cours');
        $manager->persist($enCours);

        $ferme = new Etat();
        $ferme->setLibelle('Fermé');
        $manager->persist($ferme);

        $archiv = new Etat();
        $archiv->setLibelle('Archivé');
        $manager->persist($archiv);

        $annul = new Etat();
        $annul->setLibelle('Annulé');
        $manager->persist($annul);

        //création de deux campus
        $campus1 = new Campus();
        $campus1->setNom('SAINT HERBLAIN');
        $manager->persist($campus1);

        $campus2 = new Campus();
        $campus2->setNom('HERBLAY');
        $manager->persist($campus2);

        //création de deux villes
        $stherb = new Ville();
        $stherb->setNom('SAINT HERBLAIN');
        $stherb->setCodePostal(44800);
        $manager->persist($stherb);

        $cherbourg = new Ville();
        $cherbourg->setNom('CHERBOURG');
        $cherbourg->setCodePostal(50100);
        $manager->persist($cherbourg);

        //création de deux villes
        $barherb = new Lieu();
        $barherb->setNom('La gross pinte');
        $barherb->setVille($stherb);
        $barherb->setRue('Hydratante');
        $barherb->setLatitude(24);
        $barherb->setLongitude(42);
        $manager->persist($barherb);

        $barcher = new Lieu();
        $barcher->setNom('La moins gross pinte');
        $barcher->setVille($cherbourg);
        $barcher->setRue('Froide');
        $barcher->setLatitude(62);
        $barcher->setLongitude(26);
        $manager->persist($barcher);

        $parccher = new Lieu();
        $parccher->setNom('Le petit parc');
        $parccher->setVille($cherbourg);
        $parccher->setRue('reposante');
        $parccher->setLatitude(62);
        $parccher->setLongitude(26);
        $manager->persist($parccher);

        $parcherb = new Lieu();
        $parcherb->setNom('Le gros parc');
        $parcherb->setVille($stherb);
        $parcherb->setRue('fatigante');
        $parcherb->setLatitude(72);
        $parcherb->setLongitude(83);
        $manager->persist($parccher);

        //Création de quatre utilisateurs
        $pomme = new Participant();
        $pomme->setNom('Pomme');
        $pomme->setPrenom('Pistache');
        $pomme->setPassword($hasher->hash('pommepistache'));
        $pomme->setActif(true);
        $pomme->setCampus($campus1);
        $pomme->setTelephone('0606060606');
        $pomme->setEmail('pomme@pistache.fr');
        $pomme->setPseudo('PommePistache');
        $pomme->setAdministrateur(true);
        $pomme->setRoles(['ROLE_ADMIN']);
        $manager->persist($pomme);

        $choco = new Participant();
        $choco->setNom('Chocolat');
        $choco->setPrenom('Banane');
        $choco->setPassword($hasher->hash('chocolatbanane'));
        $choco->setActif(true);
        $choco->setCampus($campus1);
        $choco->setTelephone('0707070707');
        $choco->setEmail('chocolat@banane.fr');
        $choco->setPseudo('ChocolatBanane');
        $choco->setAdministrateur(false);
        $choco->setRoles(['ROLE_USER']);
        $manager->persist($choco);

        $citron = new Participant();
        $citron->setNom('Citron');
        $citron->setPrenom('Cassis');
        $citron->setPassword($hasher->hash('citroncassis'));
        $citron->setActif(true);
        $citron->setCampus($campus2);
        $citron->setTelephone('0808080808');
        $citron->setEmail('citron@cassis.fr');
        $citron->setPseudo('CitronCassis');
        $citron->setAdministrateur(false);
        $citron->setRoles(['ROLE_USER']);
        $manager->persist($citron);

        $fraise = new Participant();
        $fraise->setNom('Fraise');
        $fraise->setPrenom('Vanille');
        $fraise->setPassword($hasher->hash('fraisevanille'));
        $fraise->setActif(true);
        $fraise->setCampus($campus2);
        $fraise->setTelephone('0909090909');
        $fraise->setEmail('fraise@vanille.fr');
        $fraise->setPseudo('FraiseVanille');
        $fraise->setAdministrateur(false);
        $fraise->setRoles(['ROLE_USER']);
        $manager->persist($fraise);
        //Creation de deux sorties

        $datet = new \DateTime();
        $datet->setDate(2023, 6, 15);
        $datet->setTime(2, 30);

        $duree = new \DateTime();
        $duree->setTime(2, 30);

        $balade = new Sortie();
        $balade->setNom("Balade au parc");
        $balade->setOrganisateur($citron);
        $balade->setDuree($duree);
        $balade->setDateHeureDebut($datet);
        $balade->setLieu($parccher);
        $balade->setEtat($enCreation);
        $datet->setDate(2023, 5, 15);
        $balade->setDateLimiteInscription($datet);
        $balade->setNbInscriptionsMax(10);
        $balade->setInfosSortie('Il va faire chaud préparez de l\'eau');
        $manager->persist($balade);

        $datet = new \DateTime();
        $datet->setDate(2023, 7, 15);
        $datet->setTime(2, 30);
        $picole = new Sortie();
        $picole->setNom("Balade au bar");
        $picole->setOrganisateur($pomme);
        $picole->setDuree($duree);
        $picole->setDateHeureDebut($datet);
        $picole->setLieu($barherb);
        $picole->setEtat($ouvert);
        $datet->setDate(2023, 6, 15);
        $picole->setDateLimiteInscription($datet);
        $picole->setNbInscriptionsMax(200);
        $picole->setInfosSortie('Il va faire chaud mais il n\'y aura pas d\'eau');
        $manager->persist($picole);

        $manager->flush();
    }
}
