<?php

namespace App\Tests\Functional;

use App\Entity\CheeseListing;
use App\ApiPlatform\Test\CustomApiTestCase;
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

        $authenticatedUser = $this->createUserAndLogIn($client, 'phpunit@test.fr', 'foo');
        $otherUser = $this->createUser('otheruser@test.com', 'foo');

        $cheesyData = [
            'title' => 'Mystery cheese... kinda green',
            'description' => 'What mysteries does it hold?',
            'price' => 5000
        ];

        $client->request('POST', '/api/cheeses', [
            // On doit envoyer un data vide sinon JSON invalide
            'json' => $cheesyData
        ]);
        $this->assertResponseStatusCodeSame(201);

        $client->request('POST', '/api/cheeses', [
            'json' => $cheesyData + ['owner' => 'api/users/'.$otherUser->getId()]
        ]);
        $this->assertResponseStatusCodeSame(400, 'not passing the correct owner');

        $client->request('POST', '/api/cheeses', [
            'json' => $cheesyData + ['owner' => 'api/users/'.$authenticatedUser->getId()]
        ]);
        $this->assertResponseStatusCodeSame(201);
    }

    public function testUpdateCheeseListing()
    {
        $client = self::createClient();
        $user1 = $this->createUser('updateUser1@test.com', 'foo');
        $user2 = $this->createUser('updateUser2@test.com', 'foo');
        $user3 = $this->createUser('updateUser3@test.com', 'foo');
        $user3->setRoles(['ROLE_ADMIN']);

        $cheeseListing = new CheeseListing('Block of chedar');
        $cheeseListing->setOwner($user1);
        $cheeseListing->setDescription('Mmmm');
        $cheeseListing->setPrice(1000);
        $cheeseListing->setIsPublished(true);

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

        $this->logIn($client, 'updateUser3@test.com', 'foo');
        $client->request('PUT', '/api/cheeses/'.$cheeseListing->getId(), [
            'json' => [
                'description' => 'titi'
            ]
        ]);
        $this->assertResponseStatusCodeSame(200);

        $this->logIn($client, 'updateUser1@test.com', 'foo');
        $client->request('PUT', '/api/cheeses/'.$cheeseListing->getId(), [
            'json' => [
                'description' => 'titi'
            ]
        ]);
        $this->assertResponseStatusCodeSame(200);
    }

    public function testGetCheeseListingCollection()
    {
        $client = self::createClient();
        $user = $this->createUser('getCollectionUser@test.com', 'foo');

        $cheeseListing1 = new CheeseListing('Cheese 1');
        $cheeseListing1->setOwner($user);
        $cheeseListing1->setDescription('Mmmm');
        $cheeseListing1->setPrice(1000);

        $cheeseListing2 = new CheeseListing('Cheese 2');
        $cheeseListing2->setOwner($user);
        $cheeseListing2->setDescription('Mmmm');
        $cheeseListing2->setPrice(1000);
        $cheeseListing2->setIsPublished(true);

        $cheeseListing3 = new CheeseListing('Cheese 3');
        $cheeseListing3->setOwner($user);
        $cheeseListing3->setDescription('Mmmm');
        $cheeseListing3->setPrice(1000);
        $cheeseListing3->setIsPublished(true);

        $em = $this->getEntityManager();
        $em->persist($cheeseListing1);
        $em->persist($cheeseListing2);
        $em->persist($cheeseListing3);
        $em->flush();

        $client->request('GET', '/api/cheeses');
        $this->assertJsonContains(['hydra:totalItems' => 2]);
    }

    public function testGetCheeseListingItem()
    {
        $client = self::createClient();
        $user = $this->createUserAndLogIn($client,'getItemUser@test.com', 'foo');

        $cheeseListing1 = new CheeseListing('Cheese 1');
        $cheeseListing1->setOwner($user);
        $cheeseListing1->setDescription('Mmmm');
        $cheeseListing1->setPrice(1000);
        $cheeseListing1->setIsPublished(false);

        $em = $this->getEntityManager();
        $em->persist($cheeseListing1);
        $em->flush();

        $client->request('GET', '/api/cheeses/'.$cheeseListing1->getId());
        $this->assertResponseStatusCodeSame(404);

        $client->request('GET', '/api/users/'.$user->getId());
        $data = $client->getResponse()->toArray();
        $this->assertEmpty($data['cheeseListing']);
    }
}