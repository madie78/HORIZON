<?php

namespace App\DataFixtures;

use App\Entity\User;
use DateTimeImmutable;
use Doctrine\Persistence\ObjectManager;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

class SuperAdminFixtures extends Fixture
{
    public function __construct( private UserPasswordHasherInterface $hasher)
    {
    }
    public function load(ObjectManager $manager): void
    {
        $superAdmin=$this->createSuperAdminFixtures();
        $manager->persist($superAdmin);
        $manager->flush();

    }
    private function createSuperAdminFixtures():User
    {
    $superAdmin= new User();

    $passwordHashed = $this->hasher->hashPassword($superAdmin, "Aazerty1234*");

    $superAdmin->setFirstName("Marie");
    $superAdmin->setLastName("Madie");
    $superAdmin->setEmail("horizon@gmail.com");
    $superAdmin->setRoles(["ROLE_SUPER_ADMIN","ROLE_ADMIN","ROLE_USER",]);
    $superAdmin->setPassword($passwordHashed);
    $superAdmin->setIsVerified(true);
    $superAdmin->setCreatedAt(new DateTimeImmutable());
    $superAdmin->setVerifiedAt(new DateTimeImmutable());
    $superAdmin->setUpdatedAt(new DateTimeImmutable());

    return $superAdmin;

    }
}