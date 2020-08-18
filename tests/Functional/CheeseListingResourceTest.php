<?php


namespace App\Tests\Functional;


use ApiPlatform\Core\Bridge\Symfony\Bundle\Test\ApiTestCase;
use App\Entity\User;
use Doctrine\ORM\EntityManagerInterface;

class CheeseListingResourceTest extends ApiTestCase
{
    public function testCreateCheeseListing()
    {
       $client = self::createClient();

        $client->request('POST', '/api/cheeses', [
            'headers' => ['Content-Type' => 'application/json'],
            // On doit envoyer un data vide sinon JSON invalide
            'json' => []
        ]);
        $this->assertResponseStatusCodeSame(401);

        $user = new User();
        $user->setEmail('phpunit@test.fr');
        $user->setUsername('Phpunit');
        $user->setPassword('$argon2id$v=19$m=65536,t=4,p=1$Cd0J6GxPMWBAhpE0F0i7sw$mcjMYoGV0ar+h62uua4lsSj3Gug+Lgr3ljzrjdBBlHQ');

        $em = self::$container->get(EntityManagerInterface::class);
        $em->persist($user);
        $em->flush();

        $client->request('POST', '/login', [
            'headers' => ['Content-Type' => 'application/json'],
            'json' => [
                'email' => 'phpunit@test.fr',
                'password' => 'foo'
            ]
        ]);

        $this->assertResponseStatusCodeSame(204);
    }
}