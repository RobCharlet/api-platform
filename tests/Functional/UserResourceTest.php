<?php

namespace App\Tests\Functional;

use App\Factory\UserFactory;
use App\Test\CustomApiTestCase;
use Hautelook\AliceBundle\PhpUnit\ReloadDatabaseTrait;

class UserResourceTest extends CustomApiTestCase
{
    use ReloadDatabaseTrait;

    public function testCreateUser()
    {
        $client = self::createClient();

        $client->request('POST', '/api/users', [
            'json' => [
                'email' => 'createUser@test.fr',
                'username' => 'createUser',
                'password' => 'brie',
            ]
        ]);
        $this->assertResponseStatusCodeSame(201);

        $user = UserFactory::repository()->findOneBy(['email' => 'createUser@test.fr']);
        $this->assertNotNull($user);
        $this->assertJsonContains([
            '@id' => '/api/users/'.$user->getUuid()->toString()
        ]);

        $this-> logIn($client, 'createUser@test.fr', 'brie');
    }

    public function testUpdateUser()
    {
        $client = self::createClient();

        $user = UserFactory::new()->create();
        $this->logIn($client, $user);

        $client->request('PUT', 'api/users/'.$user->getUuid(), [
            'json' => [
                'username' => 'newusername',
                'roles' => ['ROLE_ADMIN'] // will be ignored
            ]
        ]);
        $this->assertResponseIsSuccessful();
        $this->assertJsonContains([
            'username' => 'newusername',
        ]);

        $user->refresh();
        $this->assertEquals(['ROLE_USER'], $user->getRoles());
    }

    public function testGetUser()
    {
        $client = self::createClient();
        $user = UserFactory::new()->create([
            'phoneNumber' => '555.123.4567',
            'username' => 'cheesehead'
        ]);
        $authenticatedUser = UserFactory::new()->create();
        $this->logIn($client, $authenticatedUser);

        $client->request('GET', '/api/users/'.$user->getUuid());
        $this->assertResponseStatusCodeSame(200);

        $this->assertJsonContains([
            'username' => $user->getUsername(),
            'isMvp' => true
        ]);

        $data = $client->getResponse()->toArray();
        $this->assertArrayNotHasKey('phoneNumber', $data);
        $this->assertJsonContains([
            'isMe' => false
        ]);

        // refresh the user & elevate
        $user->refresh();
        $user->setRoles(['ROLE_ADMIN']);
        $user->save();

        // Must relogin to handle admin status
        $this->logIn($client, $user);

        $client->request('GET', '/api/users/'.$user->getUuid());
        $this->assertJsonContains([
            'phoneNumber' => '555.123.4567',
            'isMe' => true
        ]);
    }
}