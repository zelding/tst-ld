<?php

namespace App\Tests\Unit;

use App\Entity\User;
use App\Service\InviteService;
use PHPUnit\Framework\TestCase;

class InviteServiceTest extends TestCase
{
    public function testInvites()
    {
        static::markTestIncomplete("in progress");

        $user1 = new User('user1');
        $user2 = new User('user2');

        $inviteSvc = new InviteService(
            null, null, null
        );

        $inviteSvc->invite($invite1);
    }
}
