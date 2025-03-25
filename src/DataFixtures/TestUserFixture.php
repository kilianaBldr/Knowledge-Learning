<?php

namespace App\DataFixtures;

use App\Entity\User;
use App\Entity\Lessons;
use App\Repository\CursusRepository;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

class TestUserFixture extends Fixture
{
    private $passwordHasher;
    private $cursusRepository;

    public function __construct(UserPasswordHasherInterface $passwordHasher, CursusRepository $cursusRepository)
    {
        $this->passwordHasher = $passwordHasher;
        $this->cursusRepository = $cursusRepository;
    }

    public function load(ObjectManager $manager): void
    {
        // Créer l'utilisateur de test
        $user = new User();
        $user->setEmail('test2@elearning.com');
        $user->setName('Testeur');
        $user->setRoles(['ROLE_USER']);
        $user->setIsVerified(true);

        // Hasher le mot de passe
        $hashedPassword = $this->passwordHasher->hashPassword($user, 'password123');
        $user->setPassword($hashedPassword);

        // Récupérer un cursus (par exemple ID 1)
        $cursus = $this->cursusRepository->find(1);
        if ($cursus) {
            foreach ($cursus->getLessons() as $lesson) {
                $user->addPurchasedLesson($lesson); // Le user a acheté toutes les leçons de ce cursus
            }
        }

        $manager->persist($user);
        $manager->flush();
    }
}