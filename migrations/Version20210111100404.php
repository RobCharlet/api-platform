<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20210111100404 extends AbstractMigration
{
    public function getDescription() : string
    {
        return 'Create CheeseListingNotification Entity';
    }

    public function up(Schema $schema) : void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE cheese_listing_notification (id INT AUTO_INCREMENT NOT NULL, cheese_listing_id INT NOT NULL, notification_text VARCHAR(255) NOT NULL, INDEX IDX_73DD77EEB167220F (cheese_listing_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('ALTER TABLE cheese_listing_notification ADD CONSTRAINT FK_73DD77EEB167220F FOREIGN KEY (cheese_listing_id) REFERENCES cheese_listing (id)');
    }

    public function down(Schema $schema) : void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('DROP TABLE cheese_listing_notification');
    }
}
