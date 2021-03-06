<?php

namespace App\DataProvider;

use ApiPlatform\Core\DataProvider\CollectionDataProviderInterface;
use ApiPlatform\Core\DataProvider\ContextAwareCollectionDataProviderInterface;
use ApiPlatform\Core\DataProvider\DenormalizedIdentifiersAwareItemDataProviderInterface;
use ApiPlatform\Core\DataProvider\ItemDataProviderInterface;
use ApiPlatform\Core\DataProvider\RestrictedDataProviderInterface;
use App\Entity\User;
use Symfony\Component\Security\Core\Security;

class UserDataProvider implements
    ContextAwareCollectionDataProviderInterface,
    RestrictedDataProviderInterface,
    DenormalizedIdentifiersAwareItemDataProviderInterface
{
    private $itemDataProvider;
    private $collectionDataProvider;
    private $security;

    public function __construct(
        ItemDataProviderInterface $itemDataProvider,
        CollectionDataProviderInterface $collectionDataProvider,
        Security $security
    )
    {
        $this->itemDataProvider = $itemDataProvider;
        $this->collectionDataProvider = $collectionDataProvider;
        $this->security = $security;
    }

    public function getItem(string $resourceClass, $id, string $operationName = null, array $context = [])
    {
        /** @var User|null $user */
        $user =  $this->itemDataProvider->getItem($resourceClass, $id, $operationName, $context);
        /** @var User $currentUser */
        $currentUser = $this->security->getUser();

        if(!$user) {
            return null;
        }
        // Now handled in a listener
        //$user->setIsMe($currentUser === $user);

        return $user;
    }

    public function getCollection(string $resourceClass, string $operationName = null, array $context = [])
    {
        /** @var User[] $users */
        $users = $this->collectionDataProvider->getCollection($resourceClass, $operationName, $context);

        /** @var User $currentUser */
        $currentUser = $this->security->getUser();

        foreach ($users as $user) {
            // Now handled in a listener
            //$user->setIsMe($currentUser === $user);
        }
        
        return $users;
    }

    public function supports(string $resourceClass, string $operationName = null, array $context = []): bool
    {
       return $resourceClass === User::class;
    }
}