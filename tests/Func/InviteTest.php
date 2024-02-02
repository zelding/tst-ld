<?php

namespace App\Tests\Func;

use App\DataFixtures\UserFixture;
use App\Entity\User;
use App\Repository\UserRepository;
use Liip\TestFixturesBundle\Services\DatabaseToolCollection;
use Liip\TestFixturesBundle\Services\DatabaseTools\AbstractDatabaseTool;
use Symfony\Bundle\FrameworkBundle\KernelBrowser;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use Symfony\Component\HttpFoundation\Response;

class InviteTest extends WebTestCase
{
    public function testHomepage(): void
    {
        $client = static::createClient();

        $client->request('GET', '/', [], [], ['accept' => 'application-json']);

        $this->assertResponseIsSuccessful();
        $this->assertResponseHeaderSame('content-type', 'application/json');
    }

    public function testAuthUser()
    {
        $client = static::createClient();

        $user = $this->loginTestUser($client);

        $loginResponse = $client->getResponse();

        $token = $this->validateLoginResponse($loginResponse);
    }

    public function testProfile()
    {
        $client = static::createClient();
        $client->disableReboot();

        $user = $this->loginTestUser($client);

        $loginResponse = $client->getResponse();

        $token = $this->validateLoginResponse($loginResponse);

        $client->jsonRequest('GET', '/me', [], [
            'Accept'        => 'application-json',
            'Authorization' => sprintf("Bearer %s", $token)
        ]);

        //dump($client->getInternalRequest());

        $this->assertResponseIsSuccessful();
        $this->assertResponseHeaderSame('content-type', 'application/json');

        $profileResponse = $client->getResponse();
    }

    private function loginTestUser(KernelBrowser $client) : User
    {
        /** @var AbstractDatabaseTool $databaseTool */
        $databaseTool = static::getContainer()->get(DatabaseToolCollection::class)->get();
        $databaseTool->loadFixtures([UserFixture::class]);

        /** @var UserRepository $userRepository */
        $userRepository = static::getContainer()->get(UserRepository::class);

        /** @var User $user */
        $user = $userRepository->findOneByUsername(UserFixture::TEST_USER);

        $client->jsonRequest(
            'POST',
            '/login',
            ['username' => $user->getUsername(), 'password' => 'asdasd'],
            ['Accept' => 'application-json'],
        );

        $this->assertResponseIsSuccessful();
        $this->assertResponseHeaderSame('content-type', 'application/json');

        return $user;
    }

    private function validateLoginResponse(Response $loginResponse): string
    {
        $loginData = json_decode($loginResponse->getContent(), true);

        static::assertEquals(JSON_ERROR_NONE, json_last_error());

        static::assertArrayHasKey('message', $loginData);
        static::assertArrayHasKey('user', $loginData);
        static::assertArrayHasKey('token', $loginData);
        static::assertArrayHasKey('validity', $loginData);

        $token = $loginData['token'];

        static::assertIsString($token);

        return $token;
    }
}
