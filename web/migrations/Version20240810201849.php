<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20240810201849 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        $this->addSql('INSERT INTO supplier (name) VALUES (\'Govegi\')');
        $this->addSql('INSERT INTO supplier (name) VALUES (\'Dairyland\')');
    }

    public function down(Schema $schema): void
    {
        $this->addSql('DELETE FROM supplier WHERE name IN (\'Govegi\', \'Dairyland\')');
    }
}
