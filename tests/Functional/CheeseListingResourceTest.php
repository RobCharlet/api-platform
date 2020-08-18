<?php


namespace App\Tests\Functional;


use ApiPlatform\Core\Bridge\Symfony\Bundle\Test\ApiTestCase;

class CheeseListingResourceTest extends ApiTestCase
{
    public function testCreateCheeseListing()
    {
       $client = self::createClient();

        $client->request('POST', '/api/cheeses', [
            'headers' => ['Content-Type' => 'application/json'],
            // On doit envoyer un data vide sinon json invalide
            'json' => []
        ]);
        $this->assertResponseStatusCodeSame(401);
    }
}