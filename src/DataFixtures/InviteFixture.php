<?php

namespace App\DataFixtures;

use App\Entity\Invite;
use App\Entity\User;
use App\Model\InviteStatus;
use DateTimeImmutable;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;
use Doctrine\Common\DataFixtures\DependentFixtureInterface;

class InviteFixture extends Fixture implements DependentFixtureInterface
{
    public const string BAD_INVITE = "bad-invite-record";

    public static string $badHash = "SAME-THING-ALWAYS";

    public function load(ObjectManager $manager): void
    {
        $inviter = $this->getReference(UserFixture::TEST_USER, User::class);

        $long = new DateTimeImmutable();
        $long = $long->modify('+1 year');

        for($i = 0; $i < UserFixture::FAKE_USERS / 2; $i++) {
            $invite = $this->generateInvite($i, $inviter, $long);

            $manager->persist($invite);
        }

        for($i = UserFixture::FAKE_USERS / 2; $i < UserFixture::FAKE_USERS; $i++) {
            $invite = $this->generateInvite($i, $inviter, $long);

            $manager->persist($invite);
        }

        $inviter = $this->getReference('test_user_3', User::class);
        $invitee = $this->getReference('test_user_4', User::class);

        $invite = new Invite($inviter, $invitee);
        $invite->setHash(static::$badHash);
        $invite->setStatus($i % 5 ? InviteStatus::SENT : InviteStatus::ACCEPTED);
        $invite->setValidUntil($long);

        $manager->persist($invite);

        /** @see \App\Tests\Func\ApiTest::testDBFull */
        $this->addReference(self::BAD_INVITE, $invite);

        $manager->flush();
    }
    public function getDependencies(): array
    {
        return [
            UserFixture::class
        ];
    }

    protected function generateInvite(int $i, User $inviter, DateTimeImmutable $long): Invite
    {
        $name = sprintf('test_user_%0d', $i);

        /** @var User $inviter */
        $invitee = $this->getReference($name, User::class);

        $invite = new Invite($inviter, $invitee);
        $invite->setHash(hash('crc32', $i));
        $invite->setStatus($i % 5 ? InviteStatus::SENT : InviteStatus::ACCEPTED);
        $invite->setValidUntil($long);

        return $invite;
    }
}
