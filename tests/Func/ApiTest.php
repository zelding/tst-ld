<?php

namespace App\Tests\Func;
use App\DataFixtures\InviteFixture;
use App\DataFixtures\UserFixture;
use App\Exception\AppException;
use App\Model\InviteStatus;
use App\Repository\InviteRepository;
use App\Repository\UserRepository;
use App\Service\InviteService;
use Doctrine\ORM\EntityManagerInterface;
use Liip\TestFixturesBundle\Services\DatabaseToolCollection;
use Liip\TestFixturesBundle\Services\DatabaseTools\AbstractDatabaseTool;
use RuntimeException;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;

class ApiTest extends KernelTestCase
{
    protected AbstractDatabaseTool $databaseTool;
    protected EntityManagerInterface $entityManager;

    public function setUp(): void
    {
        parent::setUp();

        self::bootKernel([
            'environment' => 'test'
        ]);

        $container = static::getContainer();

        $this->databaseTool  = $container->get(DatabaseToolCollection::class)->get();
        $this->entityManager = $container->get(EntityManagerInterface::class);

        $this->entityManager->getConnection()->setAutoCommit(false);
        $this->entityManager->beginTransaction();
    }

    public function tearDown(): void
    {
        if ($this->entityManager->getConnection()->isTransactionActive()) {
            $this->entityManager->rollback();
        }

        parent::tearDown();
    }

    public function testProperInvite(): void
    {
        $this->databaseTool->loadFixtures([
            UserFixture::class
        ]);

        /** @var InviteService $inviteService */
        $inviteService = static::getContainer()->get(InviteService::class);

        /** @var UserRepository $userRepo */
        $userRepo = static::getContainer()->get(UserRepository::class);

        $u1 = $userRepo->findOneByUsername('test_user_1');
        $u2 = $userRepo->findOneByUsername('test_user_2');

        $invitation = $inviteService->invite($u1, $u2);

        $this->assertEquals(InviteStatus::SENT, $invitation->getStatus());

        try {
            $invitation = $inviteService->invite($u1, $u2);
        }
        catch(AppException $exception) {
            static::assertEquals(406, $exception->getCode());
        }
    }

    public function testDBFull()
    {
        $this->databaseTool->loadFixtures([
            UserFixture::class,
            InviteFixture::class
        ]);

        static::expectException(RuntimeException::class);

        /** @var UserRepository $userRepo */
        $userRepo = static::getContainer()->get(UserRepository::class);

        $fakeInviteService = new class(
            $userRepo,
            static::getContainer()->get(InviteRepository::class),
            static::getContainer()->get(EntityManagerInterface::class)
        ) extends InviteService {
            protected static function newHash(): string
            {
                return "SAME-THING-ALWAYS";
            }
        };

        $u1 = $userRepo->findOneByUsername('test_user_1');
        $u2 = $userRepo->findOneByUsername('test_user_2');

        $fakeInviteService->invite($u1, $u2);
    }
}
