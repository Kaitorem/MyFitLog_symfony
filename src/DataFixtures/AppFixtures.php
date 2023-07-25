<?php

namespace App\DataFixtures;

use App\Entity\User;
use App\Entity\Historique;
use App\Entity\Libelle;
use App\Entity\Domaine;
use App\Repository\LibelleRepository;
use App\Repository\DomaineRepository;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;
use Faker\Factory;
use Faker\Generator;

class AppFixtures extends Fixture
{

    private Generator $faker;
    private LibelleRepository $libelleRepository;
    private DomaineRepository $domaineRepository;

    public function __construct(LibelleRepository $libelleRepository, DomaineRepository $domaineRepository)
    {
        $this->faker = Factory::create('fr_FR');
        $this->libelleRepository = $libelleRepository;
        $this->domaineRepository = $domaineRepository;
    }

    public function load(ObjectManager $manager): void
    {
        $users = [];
        for ($i=0; $i < 5; $i++) {
            $user =  new User();
            $user->setFirstName($this->faker->firstName);
            $user->setLastName($this->faker->lastName);
            $user->setEmail($this->faker->safeEmail);
            $user->setPassword(
                password_hash("123456", PASSWORD_BCRYPT)
            );
            array_push($users, $user);
            $manager->persist($user);
        }

        for ($i=0; $i < 2; $i++) { 
            $categorie = new Domaine();
            $categorie->setLibelle('categorie' . ' ' . $i);
            $manager->persist($categorie);
        }

        for ($i=0; $i < 12; $i++) { 
            $lib = new Libelle();
            $unites_de_mesure = array('mètre', 'kilogramme', 'hectare', 'heure', 'kcal', 'pas', 'kilometre');
            $unite_de_mesure = $this->faker->randomElement($unites_de_mesure);
            $lib->setLabel($this->faker->unique()->word);
            $lib->setUnit($unite_de_mesure);
            if ($this->domaineRepository->findBy(['id' => rand(0, count($this->domaineRepository->findAll()))])) {
                $lib->setDomaine($this->domaineRepository->findBy(['id' => rand(0, count($this->domaineRepository->findAll()))])[0]);
            }else {
                continue;
            }
            $manager->persist($lib);
        }


        for ($i=0; $i < 100; $i++) { 
            $history = new Historique();
            $libelle = $this->libelleRepository->findBy(['id' => rand(0, count($this->libelleRepository->findAll()))]);
            if (!$libelle) {
                continue;
            }
            $domaine = $this->domaineRepository->findBy(['id' => $libelle[0]->getDomaine()->getId()]);
            if (!$domaine) {
                continue;
            }
            $userId = rand(0, 20);
            $history->setUser($users[$userId]);
            $history->setValeur(rand(0,1000));
            $history->setLibelle($libelle[0]);
            $history->setDomaine($domaine[0]);
            $history->setCreatedAt($this->faker->dateTime());
            $manager->persist($history);
        }

        $manager->flush();
    }
}
