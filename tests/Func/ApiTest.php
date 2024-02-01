<?php

namespace App\Tests\Func;
use App\Service\InviteService;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;

class ApiTest extends KernelTestCase
{
    public function testSomething(): void
    {
        self::bootKernel([
            'environment' => 'test'
        ]);

        $container = static::getContainer();

        /** @var InviteService $inviteService */
        $inviteService = $container->get(InviteService::class);

        $inviteService->invite($u1, $u2);

        $this->assertEquals($u2, $u1);
    }
}
