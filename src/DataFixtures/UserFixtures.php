<?php

namespace App\DataFixtures;

use App\Entity\User;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

class UserFixtures extends Fixture
{
    public function __construct(
        private UserPasswordHasherInterface $passwordHasher
    ) {
    }

    public function load(ObjectManager $manager): void
    {
        // Admin
        $admin = new User();
        $admin->setEmail('admin@stubborn.com');
        $admin->setName('Admin Stubborn');
        $admin->setRoles(['ROLE_ADMIN']);
        $admin->setPassword($this->passwordHasher->hashPassword($admin, 'admin123'));
        $admin->setIsVerified(true);
        $manager->persist($admin);

        // Client de test
        $client = new User();
        $client->setEmail('client@test.com');
        $client->setName('John Doe');
        $client->setRoles(['ROLE_USER']);
        $client->setPassword($this->passwordHasher->hashPassword($client, 'client123'));
        $client->setDeliveryAddress('123 Test Street, London, UK');
        $client->setIsVerified(true);
        $manager->persist($client);

        // Client non vérifié
        $unverified = new User();
        $unverified->setEmail('unverified@test.com');
        $unverified->setName('Jane Smith');
        $unverified->setRoles(['ROLE_USER']);
        $unverified->setPassword($this->passwordHasher->hashPassword($unverified, 'test123'));
        $unverified->setDeliveryAddress('456 Test Avenue, London, UK');
        $unverified->setIsVerified(false);
        $unverified->setVerificationToken(bin2hex(random_bytes(32)));
        $manager->persist($unverified);

        $manager->flush();
    }
}