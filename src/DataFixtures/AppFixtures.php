<?php
namespace App\DataFixtures;

use App\Entity\Location;
use App\Entity\Summit;
use App\Entity\User;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

class AppFixtures extends Fixture
{
    public function __construct(
        private UserPasswordHasherInterface $hasher
    ) {}

    public function load(ObjectManager $manager): void
    {
        // Admin user
        $admin = new User();
        $admin->setEmail('admin@ism.de');
        $admin->setRoles(['ROLE_ADMIN']);
        $admin->setPassword($this->hasher->hashPassword($admin, 'admin123'));
        $manager->persist($admin);

        // Location
        $location = new Location();
        $location->setCity('Hamburg');
        $location->setCampusName('ISM Campus Hamburg');
        $location->setAddress('Brooktokai 22, 20457 Hamburg');
        $location->setCapacity(80);
        $location->setEvenDate(new \DateTime('2026-06-15 10:00:00'));
        $manager->persist($location);

        // Summit
        $summit = new Summit();
        $summit->setTitle('Data Science Institute Launch Summit 2026');
        $summit->setIsActive(true);
        $summit->setLocation($location);
        $manager->persist($summit);

        $manager->flush();
    }
}