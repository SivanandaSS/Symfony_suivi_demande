<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20250819122403 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE devis_prestation (devis_id INT NOT NULL, prestation_id INT NOT NULL, INDEX IDX_E169C44541DEFADA (devis_id), INDEX IDX_E169C4459E45C554 (prestation_id), PRIMARY KEY(devis_id, prestation_id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('ALTER TABLE devis_prestation ADD CONSTRAINT FK_E169C44541DEFADA FOREIGN KEY (devis_id) REFERENCES devis (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE devis_prestation ADD CONSTRAINT FK_E169C4459E45C554 FOREIGN KEY (prestation_id) REFERENCES prestation (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE demande ADD category_id INT NOT NULL, ADD devis_id INT DEFAULT NULL');
        $this->addSql('ALTER TABLE demande ADD CONSTRAINT FK_2694D7A512469DE2 FOREIGN KEY (category_id) REFERENCES category (id)');
        $this->addSql('ALTER TABLE demande ADD CONSTRAINT FK_2694D7A541DEFADA FOREIGN KEY (devis_id) REFERENCES devis (id)');
        $this->addSql('CREATE INDEX IDX_2694D7A512469DE2 ON demande (category_id)');
        $this->addSql('CREATE UNIQUE INDEX UNIQ_2694D7A541DEFADA ON demande (devis_id)');
        $this->addSql('ALTER TABLE devis ADD facture_id INT DEFAULT NULL');
        $this->addSql('ALTER TABLE devis ADD CONSTRAINT FK_8B27C52B7F2DEE08 FOREIGN KEY (facture_id) REFERENCES facture (id)');
        $this->addSql('CREATE UNIQUE INDEX UNIQ_8B27C52B7F2DEE08 ON devis (facture_id)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE devis_prestation DROP FOREIGN KEY FK_E169C44541DEFADA');
        $this->addSql('ALTER TABLE devis_prestation DROP FOREIGN KEY FK_E169C4459E45C554');
        $this->addSql('DROP TABLE devis_prestation');
        $this->addSql('ALTER TABLE demande DROP FOREIGN KEY FK_2694D7A512469DE2');
        $this->addSql('ALTER TABLE demande DROP FOREIGN KEY FK_2694D7A541DEFADA');
        $this->addSql('DROP INDEX IDX_2694D7A512469DE2 ON demande');
        $this->addSql('DROP INDEX UNIQ_2694D7A541DEFADA ON demande');
        $this->addSql('ALTER TABLE demande DROP category_id, DROP devis_id');
        $this->addSql('ALTER TABLE devis DROP FOREIGN KEY FK_8B27C52B7F2DEE08');
        $this->addSql('DROP INDEX UNIQ_8B27C52B7F2DEE08 ON devis');
        $this->addSql('ALTER TABLE devis DROP facture_id');
    }
}
