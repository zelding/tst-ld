<?php

namespace App\DataFixtures;

use App\Entity\User;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

class UserFixture extends Fixture
{
    public const int FAKE_USERS = 20;

    public const string TEST_USER = 'test-user';

    public function __construct(private readonly UserPasswordHasherInterface $hasher) {}

    public function load(ObjectManager $manager): void
    {
        $user = new User(self::TEST_USER);
        $user->setPassword($this->hasher->hashPassword($user, 'asdasd'));
        $manager->persist($user);

        $this->addReference(self::TEST_USER, $user);

        for ($i = 0; $i < self::FAKE_USERS; $i++) {
            $name = sprintf('test_user_%0d', $i);

            $user = new User($name);
            $user->setPassword($this->hasher->hashPassword($user, $i));

            $manager->persist($user);

            $this->addReference($name, $user);
        }

        $manager->flush();
    }
}
