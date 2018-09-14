<?php declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20180911150303 extends AbstractMigration
{
    public function up(Schema $schema) : void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('ALTER TABLE users ADD is_active TINYINT(1) NOT NULL, ADD username VARCHAR(255) NOT NULL');
        $this->addSql('ALTER TABLE articles DROP FOREIGN KEY FK_BFDD3168A76ED395');
        $this->addSql('ALTER TABLE articles CHANGE content content VARCHAR(255) NOT NULL');
        $this->addSql('ALTER TABLE articles ADD CONSTRAINT FK_BFDD3168A76ED395 FOREIGN KEY (user_id) REFERENCES users (id)');
        $this->addSql('ALTER TABLE articles_categories DROP FOREIGN KEY FK_DE004A0E1EBAF6CC');
        $this->addSql('ALTER TABLE articles_categories DROP FOREIGN KEY FK_DE004A0EA21214B7');
        $this->addSql('ALTER TABLE articles_categories ADD CONSTRAINT FK_DE004A0E1EBAF6CC FOREIGN KEY (articles_id) REFERENCES articles (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE articles_categories ADD CONSTRAINT FK_DE004A0EA21214B7 FOREIGN KEY (categories_id) REFERENCES categories (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE comments DROP FOREIGN KEY FK_5F9E962A1EBAF6CC');
        $this->addSql('ALTER TABLE comments DROP FOREIGN KEY FK_5F9E962A67B3B43D');
        $this->addSql('ALTER TABLE comments ADD CONSTRAINT FK_5F9E962A1EBAF6CC FOREIGN KEY (articles_id) REFERENCES articles (id)');
        $this->addSql('ALTER TABLE comments ADD CONSTRAINT FK_5F9E962A67B3B43D FOREIGN KEY (users_id) REFERENCES users (id)');
    }

    public function down(Schema $schema) : void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('ALTER TABLE articles DROP FOREIGN KEY FK_BFDD3168A76ED395');
        $this->addSql('ALTER TABLE articles CHANGE content content TEXT NOT NULL COLLATE utf8mb4_unicode_ci');
        $this->addSql('ALTER TABLE articles ADD CONSTRAINT FK_BFDD3168A76ED395 FOREIGN KEY (user_id) REFERENCES users (id) ON UPDATE CASCADE ON DELETE CASCADE');
        $this->addSql('ALTER TABLE articles_categories DROP FOREIGN KEY FK_DE004A0E1EBAF6CC');
        $this->addSql('ALTER TABLE articles_categories DROP FOREIGN KEY FK_DE004A0EA21214B7');
        $this->addSql('ALTER TABLE articles_categories ADD CONSTRAINT FK_DE004A0E1EBAF6CC FOREIGN KEY (articles_id) REFERENCES articles (id) ON UPDATE CASCADE ON DELETE CASCADE');
        $this->addSql('ALTER TABLE articles_categories ADD CONSTRAINT FK_DE004A0EA21214B7 FOREIGN KEY (categories_id) REFERENCES categories (id) ON UPDATE CASCADE ON DELETE CASCADE');
        $this->addSql('ALTER TABLE comments DROP FOREIGN KEY FK_5F9E962A67B3B43D');
        $this->addSql('ALTER TABLE comments DROP FOREIGN KEY FK_5F9E962A1EBAF6CC');
        $this->addSql('ALTER TABLE comments ADD CONSTRAINT FK_5F9E962A67B3B43D FOREIGN KEY (users_id) REFERENCES users (id) ON UPDATE CASCADE ON DELETE CASCADE');
        $this->addSql('ALTER TABLE comments ADD CONSTRAINT FK_5F9E962A1EBAF6CC FOREIGN KEY (articles_id) REFERENCES articles (id) ON UPDATE CASCADE ON DELETE CASCADE');
        $this->addSql('ALTER TABLE users DROP is_active, DROP username');
    }
}
