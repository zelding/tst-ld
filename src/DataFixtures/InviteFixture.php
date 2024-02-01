<?php

namespace App\DataFixtures;

use App\Entity\Invite;
use App\Entity\User;
use DateTimeImmutable;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;
use Doctrine\Common\DataFixtures\DependentFixtureInterface;

class InviteFixture extends Fixture implements DependentFixtureInterface
{
    public function load(ObjectManager $manager): void
    {
        $inviter = $this->getReference(UserFixture::TEST_USER);

        $long = new DateTimeImmutable();
        $long = $long->modify('+1 year');

        for($i = 0; $i < UserFixture::FAKE_USERS / 2; $i++) {
            $name = sprintf('test_user_%0d', $i);

            /** @var User $inviter */
            $invitee = $this->getReference($name);

            $invite = new Invite($inviter, $invitee);
            $invite->setHash(hash('crc32', $i));
            $invite->setValidUntil($long);

            $manager->persist($invite);
        }

        for($i = UserFixture::FAKE_USERS / 2; $i < UserFixture::FAKE_USERS; $i++) {
            $name = sprintf('test_user_%0d', $i);

            /** @var User $inviter */
            $invitee = $this->getReference($name);

            $invite = new Invite($invitee, $inviter);
            $invite->setHash(hash('crc32', $i));
            $invite->setValidUntil($long);

            $manager->persist($invite);
        }

        $manager->flush();
    }
    public function getDependencies()
    {
        return [
            UserFixture::class
        ];
    }
}
