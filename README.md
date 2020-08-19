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

# Lancement des tests
./bin/phpunit
./bin/phpunit --filter=fonctionTest