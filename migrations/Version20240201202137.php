<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20240201202137 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE invite (id INT AUTO_INCREMENT NOT NULL, user_from INT DEFAULT NULL, user_to INT DEFAULT NULL, hash VARCHAR(255) NOT NULL, status INT NOT NULL, created_at DATETIME NOT NULL COMMENT \'(DC2Type:datetime_immutable)\', valid_until DATETIME NOT NULL COMMENT \'(DC2Type:datetime_immutable)\', INDEX IDX_C7E210D7C39BEDB9 (user_from), INDEX IDX_C7E210D7CFD06601 (user_to), INDEX validaity_idx (valid_until), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('ALTER TABLE invite ADD CONSTRAINT FK_C7E210D7C39BEDB9 FOREIGN KEY (user_from) REFERENCES user (id)');
        $this->addSql('ALTER TABLE invite ADD CONSTRAINT FK_C7E210D7CFD06601 FOREIGN KEY (user_to) REFERENCES user (id)');
        $this->addSql('ALTER TABLE token DROP FOREIGN KEY FK_5F37A13BCFD06601');
        $this->addSql('ALTER TABLE token DROP FOREIGN KEY FK_5F37A13BC39BEDB9');
        $this->addSql('DROP TABLE token');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE token (id INT AUTO_INCREMENT NOT NULL, user_from INT DEFAULT NULL, user_to INT DEFAULT NULL, hash VARCHAR(255) CHARACTER SET utf8mb4 NOT NULL COLLATE `utf8mb4_unicode_ci`, status INT NOT NULL, created_at DATETIME NOT NULL COMMENT \'(DC2Type:datetime_immutable)\', valid_until DATETIME NOT NULL COMMENT \'(DC2Type:datetime_immutable)\', INDEX IDX_5F37A13BCFD06601 (user_to), INDEX IDX_5F37A13BC39BEDB9 (user_from), INDEX validaity_idx (valid_until), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB COMMENT = \'\' ');
        $this->addSql('ALTER TABLE token ADD CONSTRAINT FK_5F37A13BCFD06601 FOREIGN KEY (user_to) REFERENCES user (id)');
        $this->addSql('ALTER TABLE token ADD CONSTRAINT FK_5F37A13BC39BEDB9 FOREIGN KEY (user_from) REFERENCES user (id)');
        $this->addSql('ALTER TABLE invite DROP FOREIGN KEY FK_C7E210D7C39BEDB9');
        $this->addSql('ALTER TABLE invite DROP FOREIGN KEY FK_C7E210D7CFD06601');
        $this->addSql('DROP TABLE invite');
    }
}
