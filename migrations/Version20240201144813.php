<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20240201144813 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql(
            "CREATE TABLE token (
                id INT AUTO_INCREMENT NOT NULL, 
                user_id INT DEFAULT NULL, hash VARCHAR(255) NOT NULL, 
                created_at DATETIME NOT NULL COMMENT '(DC2Type:datetime_immutable)', 
                valid_until DATETIME NOT NULL COMMENT '(DC2Type:datetime_immutable)', 
                INDEX IDX_5F37A13BA76ED395 (user_id), INDEX validaity_idx (valid_until), PRIMARY KEY(id)) 
                DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB"
        );
        $this->addSql(
            "CREATE TABLE user (
                id INT AUTO_INCREMENT NOT NULL, 
                username VARCHAR(180) NOT NULL, 
                roles JSON NOT NULL, 
                password VARCHAR(255) NOT NULL, 
                UNIQUE INDEX UNIQ_8D93D649F85E0677 (username), 
                INDEX username_idx (username), 
                PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB"
        );
        $this->addSql('ALTER TABLE token ADD CONSTRAINT FK_5F37A13BA76ED395 FOREIGN KEY (user_id) REFERENCES user (id)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE token DROP FOREIGN KEY FK_5F37A13BA76ED395');
        $this->addSql('DROP TABLE token');
        $this->addSql('DROP TABLE user');
    }
}
