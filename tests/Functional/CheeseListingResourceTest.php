<?php

namespace App\Tests\Functional;

use App\Entity\CheeseListing;
use App\Factory\CheeseListingFactory;
use App\Factory\UserFactory;
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

        $authenticatedUser = UserFactory::new()->create();
        $otherUser = UserFactory::new()->create();
        $this->logIn($client, $authenticatedUser);

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
        $user1 = UserFactory::new()->create();
        $user2 = UserFactory::new()->create();

        $cheeseListing = CheeseListingFactory::new()->published()->create([
            'owner' => $user1
        ]);

        $this->logIn($client, $user2);
        $client->request('PUT', '/api/cheeses/'.$cheeseListing->getId(), [
            'json' => [
                'title' => 'updated',
                'owner' => '/api/users/'.$user2->getId()
            ]
        ]);
        $this->assertResponseStatusCodeSame(403, 'only author can updated');

        $this->logIn($client, $user1);
        $client->request('PUT', '/api/cheeses/'.$cheeseListing->getId(), [
            'json' => [
                'title' => 'updated',
            ]
        ]);
        $this->assertResponseStatusCodeSame(200);
    }

    public function testPublishCheeseListing()
    {
        $client = self::createClient();
        $user = UserFactory::new()->create();

        $cheeseListing = CheeseListingFactory::new()->create([
            'owner' => $user
        ]);

        $this->logIn($client, $user);
        $client->request('PUT', '/api/cheeses/'.$cheeseListing->getId(), [
            'json' => [
                'isPublished' => true,
            ]
        ]);
        $this->assertResponseStatusCodeSame(200);
        // Update entity with latest data
        $cheeseListing->refresh();
        $this->assertTrue($cheeseListing->getIsPublished());
    }

    public function testGetCheeseListingCollection()
    {
        $client = self::createClient();
        $user = UserFactory::new()->create();

        $factory = CheeseListingFactory::new([
            'owner' =>$user
        ]);

        // CL1 Unpublished
        $factory->create();

        //CL2 Published
        $cheeseListing2 = $factory->published()->create([
            'title' => 'cheese2',
            'description' => 'cheese',
            'price' => 1000,
        ]);

        //CL3 Published
        $factory->published()->create();

        $client->request('GET', '/api/cheeses');
        $this->assertJsonContains(['hydra:totalItems' => 2]);
        $this->assertJsonContains(['hydra:member' => [
            0 => [
                '@id' => '/api/cheeses/' . $cheeseListing2->getId(),
                '@type' => 'cheese',
                'title' => 'cheese2',
                'description' => 'cheese',
                'price' => 1000,
                'owner' => '/api/users/' . $user->getId(),
                'shortDescription' => 'cheese',
                'createdAtAgo' => '1 second ago',
            ]
        ]]);
    }

    public function testGetCheeseListingItem()
    {
        $client = self::createClient();
        $user = UserFactory::new()->create();
        $this->logIn($client, $user);
        $otherUser = UserFactory::new()->create();

        $cheeseListing1 = CheeseListingFactory::new()->create([
            'owner' => $otherUser
        ]);

        $client->request('GET', '/api/cheeses/'.$cheeseListing1->getId());
        $this->assertResponseStatusCodeSame(404);

        $response = $client->request('GET', '/api/users/'.$user->getId());
        $data = $response->toArray();
        $this->assertEmpty($data['cheeseListing']);
    }
}