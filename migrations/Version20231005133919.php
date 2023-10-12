<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20231005133919 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE usr ALTER id DROP DEFAULT');
        $this->addSql('ALTER INDEX usr_email_key RENAME TO UNIQ_1762498CE7927C74');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE SCHEMA public');
        $this->addSql('CREATE SEQUENCE user_id_seq INCREMENT BY 1 MINVALUE 1 START 1');
        $this->addSql('CREATE TABLE "user" (id INT NOT NULL, email VARCHAR(180) NOT NULL, roles JSON NOT NULL, password VARCHAR(255) NOT NULL, PRIMARY KEY(id))');
        $this->addSql('CREATE UNIQUE INDEX uniq_8d93d649e7927c74 ON "user" (email)');
        $this->addSql('CREATE SEQUENCE usr_id_seq');
        $this->addSql('SELECT setval(\'usr_id_seq\', (SELECT MAX(id) FROM "usr"))');
        $this->addSql('ALTER TABLE "usr" ALTER id SET DEFAULT nextval(\'usr_id_seq\')');
        $this->addSql('ALTER INDEX uniq_1762498ce7927c74 RENAME TO usr_email_key');
    }
}
