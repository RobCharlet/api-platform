# Mise en place
docker-compose up -d
symfony composer install
yarn install
symfony serve -d
symfony console doctrine:database:create
symfony console doctrine:schema:create
yarn watch

# Environnement de test
./bin/console doctrine:database:create --env=test
./bin/console doctrine:schema:create --env=test

Si on veut que symfony injecte directement la connection à la BDD:
symfony console doctrine:database:create --env=test
symfony console doctrine:schema:create --env=test

# Lancement des tests
php ./bin/phpunit
php ./bin/phpunit --filter=fonctionTest
php ./bin/phpunit tests/Functional/UserResourceTest.php

Si on veut que symfony injecte directement la connection à la BDD:
symfony run bin/phpunit
symfony run bin/phpunit --filter=fonctionTest
symfony run bin/phpunit tests/Functional/UserResourceTest.php

# Mise à jour base de test après migration
./bin/console doctrine:schema:update --force --env=test