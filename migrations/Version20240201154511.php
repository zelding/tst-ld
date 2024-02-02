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
        return 'User table with a test user';
    }

    public function up(Schema $schema): void
    {
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

        $this->addSql(
            'INSERT INTO user (username, roles, password) 
            VALUES (\'test_user_01\', \'[]\', \'$2y$13$ZHqJ4JjhGvt.G8V8iaVHpOumZXcjM0kf7lmsDG73AFZ8iNDypYmcy\')'
        );
    }

    public function down(Schema $schema): void
    {
        $this->addSql('DROP TABLE user');
    }
}
