<?php

namespace App\Tests\Functional;

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

        $this-> logIn($client, 'createUser@test.fr', 'brie');
    }
}