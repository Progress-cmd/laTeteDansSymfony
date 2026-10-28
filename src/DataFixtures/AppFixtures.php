<?php

namespace App\DataFixtures;

use App\Entity\Artistes;
use App\Entity\Benevole;
use App\Entity\Lieux;
use App\Entity\Oeuvre;
use App\Entity\Photo;
use App\Entity\Techniques;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;
use Faker\Factory;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

class AppFixtures extends Fixture
{
    public function __construct(
        private UserPasswordHasherInterface $hasher,
    ) {
    }

    public function load(ObjectManager $manager): void
    {
        $faker = Factory::create('fr_FR');

        // --- Bénévoles ---
        // Un bénévole admin avec un mot de passe connu, pour tester le login
        $admin = new Benevole();
        $admin->setNom('Admin')
            ->setPrenom('Test')
            ->setEmail('admin@artderue.fr')
            ->setRoles(['ROLE_ADMIN'])
            ->setPassword($this->hasher->hashPassword($admin, 'admin'));
        $manager->persist($admin);
        $benevoles = [$admin];

        // 10 bénévoles aléatoires (mot de passe : "password")
        for ($i = 0; $i < 10; ++$i) {
            $benevole = new Benevole();
            $benevole->setNom($faker->lastName())
                ->setPrenom($faker->firstName())
                ->setEmail($faker->unique()->safeEmail())
                ->setRoles(['ROLE_USER'])
                ->setPassword($this->hasher->hashPassword($benevole, 'password'));
            $manager->persist($benevole);
            $benevoles[] = $benevole;
        }

        // --- Techniques ---
        $techniques = [];
        foreach (['Bombe aérosol', 'Pochoir', 'Collage', 'Mosaïque', 'Fresque', 'Sticker'] as $nom) {
            $technique = new Techniques();
            $technique->setNom($nom);
            $manager->persist($technique);
            $techniques[] = $technique;
        }

        // --- Artistes ---
        $nationalites = ['Française', 'Belge', 'Espagnole', 'Italienne', 'Britannique', 'Américaine', 'Brésilienne'];
        $artistes = [];
        for ($i = 0; $i < 8; ++$i) {
            $artiste = new Artistes();
            $artiste->setPseudonyme(ucfirst($faker->unique()->word()).' '.strtoupper($faker->lexify('??')))
                ->setNomReel($faker->name())
                ->setBio($faker->paragraph(3))
                ->setNationalite($faker->randomElement($nationalites));
            $manager->persist($artiste);
            $artistes[] = $artiste;
        }

        // --- Lieux (autour de Paris) ---
        $lieux = [];
        for ($i = 0; $i < 8; ++$i) {
            $lieu = new Lieux();
            $lieu->setNom('Mur '.$faker->streetName())
                ->setAdresse($faker->streetAddress())
                ->setQuartier($faker->randomElement(['Belleville', 'Montmartre', 'Le Marais', 'Butte-aux-Cailles', 'Oberkampf', 'La Villette']))
                ->setLatitude((string) $faker->latitude(48.8, 48.9))
                ->setLongitude((string) $faker->longitude(2.25, 2.4));
            $manager->persist($lieu);
            $lieux[] = $lieu;
        }

        // --- Oeuvres ---
        $oeuvres = [];
        for ($i = 0; $i < 15; ++$i) {
            $oeuvre = new Oeuvre();
            $oeuvre->setTitre(ucfirst($faker->words(3, true)))
                ->setDateCreation($faker->dateTimeBetween('-10 years', '-1 month'))
                ->setDescription($faker->paragraph())
                ->setEstDetruite(false)
                ->setLieux($faker->randomElement($lieux));

            foreach ($faker->randomElements($artistes, $faker->numberBetween(1, 2)) as $artiste) {
                $oeuvre->addArtiste($artiste);
            }
            foreach ($faker->randomElements($techniques, $faker->numberBetween(1, 3)) as $technique) {
                $oeuvre->addTechnique($technique);
            }

            $manager->persist($oeuvre);
            $oeuvres[] = $oeuvre;
        }

        // Quelques oeuvres en recouvrent une autre (relation OneToOne :
        // chaque oeuvre ne peut être recouverte qu'une seule fois)
        for ($i = 1; $i < count($oeuvres); $i += 4) {
            $recouverte = $oeuvres[$i - 1];
            $recouverte->setEstDetruite(true);
            $oeuvres[$i]->setOeuvreRecouverte($recouverte);
        }

        // --- Photos ---
        $etats = ['Intacte', 'Légèrement dégradée', 'Très dégradée', 'Taguée', 'Effacée'];
        foreach ($oeuvres as $oeuvre) {
            for ($j = 0; $j < $faker->numberBetween(1, 3); ++$j) {
                $photo = new Photo();
                $photo->setDatePriseVue($faker->dateTimeBetween($oeuvre->getDateCreation(), 'now'))
                    ->setNomFichier($faker->uuid().'.jpg')
                    ->setEtatOeuvre($faker->randomElement($etats))
                    ->setBenevole($faker->randomElement($benevoles))
                    ->setOeuvre($oeuvre);
                $manager->persist($photo);
            }
        }

        $manager->flush();
    }
}
