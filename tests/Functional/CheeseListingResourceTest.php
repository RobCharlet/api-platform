<?php

namespace App\Tests\Functional;

use App\Entity\User;
use App\Test\CustomApiTestCase;
use Doctrine\ORM\EntityManagerInterface;
use Hautelook\AliceBundle\PhpUnit\ReloadDatabaseTrait;

class CheeseListingResourceTest extends CustomApiTestCase
{
    use ReloadDatabaseTrait;

    public function testCreateCheeseListing()
    {
        $client = self::createClient();

        $client->request('POST', '/api/cheeses', [
            // On doit envoyer un data vide sinon JSON invalide
            'json' => []
        ]);
        $this->assertResponseStatusCodeSame(401);

        $this->createUserAndLogIn($client, 'phpunit@test.fr', 'foo');

        $client->request('POST', '/api/cheeses', [
            // On doit envoyer un data vide sinon JSON invalide
            'json' => []
        ]);
        // Loggé mais JSON vide -> 400
        $this->assertResponseStatusCodeSame(400);
    }
}