<?php

namespace App\Tests\Functional;

use App\Entity\CheeseListing;
use App\Test\CustomApiTestCase;
use Hautelook\AliceBundle\PhpUnit\ReloadDatabaseTrait;

class CheeseListingResourceTest extends CustomApiTestCase
{
    use ReloadDatabaseTrait;

    public function testCreateCheeseListing()
    {
        // Boot symfony container, who give access to service
        // Must be the first line of the tests
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

    public function testUpdateCheeseListing()
    {
        $client = self::createClient();
        $user1 = $this->createUser('updateUser1@test.com', 'foo');
        $user2 = $this->createUser('updateUser2@test.com', 'foo');

        $cheeseListing = new CheeseListing('Block of chedar');
        $cheeseListing->setOwner($user1);
        $cheeseListing->setDescription('Mmmm');
        $cheeseListing->setPrice(1000);

        $em = $this->getEntityManager();
        $em->persist($cheeseListing);
        $em->flush();

        $this->logIn($client, 'updateUser2@test.com', 'foo');
        $client->request('PUT', '/api/cheeses/'.$cheeseListing->getId(), [
            'json' => [
                'description' => 'titi',
                'owner' => '/api/users/'.$user2->getId()
            ]
        ]);
        $this->assertResponseStatusCodeSame(403);

        $this->logIn($client, 'updateUser1@test.com', 'foo');
        $client->request('PUT', '/api/cheeses/'.$cheeseListing->getId(), [
            'json' => [
                'description' => 'titi'
            ]
        ]);
        $this->assertResponseStatusCodeSame(200);
    }
}