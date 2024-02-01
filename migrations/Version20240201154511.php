<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20240201154511 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql(
            sql: "CREATE TABLE token (
                    id INT AUTO_INCREMENT NOT NULL, 
                    user_from INT DEFAULT NULL, 
                    user_to INT DEFAULT NULL, 
                    hash VARCHAR(255) NOT NULL, 
                    status INT NOT NULL, 
                    created_at DATETIME NOT NULL COMMENT '(DC2Type:datetime_immutable)', 
                    valid_until DATETIME NOT NULL COMMENT '(DC2Type:datetime_immutable)', 
                    INDEX IDX_5F37A13BC39BEDB9 (user_from), INDEX IDX_5F37A13BCFD06601 (user_to), 
                    INDEX validaity_idx (valid_until), 
                    PRIMARY KEY(id)
                ) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB"
        );

        $this->addSql(
            sql: 'CREATE TABLE user (
                id INT AUTO_INCREMENT NOT NULL, 
                username VARCHAR(180) NOT NULL, 
                roles JSON NOT NULL, 
                password VARCHAR(255) NOT NULL, 
                UNIQUE INDEX UNIQ_8D93D649F85E0677 (username), 
                INDEX username_idx (username), 
                PRIMARY KEY(id)
            ) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB'
        );

        $this->addSql('ALTER TABLE token ADD CONSTRAINT FK_5F37A13BC39BEDB9 FOREIGN KEY (user_from) REFERENCES user (id)');
        $this->addSql('ALTER TABLE token ADD CONSTRAINT FK_5F37A13BCFD06601 FOREIGN KEY (user_to) REFERENCES user (id)');


        $this->addSql('INSERT INTO user (username, roles, password) VALUES (`test_user_01`, `[]`, `$2y$13$ZHqJ4JjhGvt.G8V8iaVHpOumZXcjM0kf7lmsDG73AFZ8iNDypYmcy`)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE token DROP FOREIGN KEY FK_5F37A13BC39BEDB9');
        $this->addSql('ALTER TABLE token DROP FOREIGN KEY FK_5F37A13BCFD06601');
        $this->addSql('DROP TABLE token');
        $this->addSql('DROP TABLE user');
    }
}
