<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20250821081537 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE devis ADD numero VARCHAR(255) NOT NULL, ADD statut VARCHAR(255) NOT NULL');
        $this->addSql('ALTER TABLE devis_prestation DROP FOREIGN KEY FK_E169C4459E45C554');
        $this->addSql('ALTER TABLE devis_prestation DROP FOREIGN KEY FK_E169C44541DEFADA');
        $this->addSql('ALTER TABLE devis_prestation ADD id INT AUTO_INCREMENT NOT NULL, ADD quantity NUMERIC(10, 2) NOT NULL, ADD soustotal NUMERIC(10, 2) NOT NULL, CHANGE devis_id devis_id INT DEFAULT NULL, CHANGE prestation_id prestation_id INT DEFAULT NULL, DROP PRIMARY KEY, ADD PRIMARY KEY (id)');
        $this->addSql('ALTER TABLE devis_prestation ADD CONSTRAINT FK_E169C4459E45C554 FOREIGN KEY (prestation_id) REFERENCES prestation (id)');
        $this->addSql('ALTER TABLE devis_prestation ADD CONSTRAINT FK_E169C44541DEFADA FOREIGN KEY (devis_id) REFERENCES devis (id)');
        $this->addSql('ALTER TABLE facture ADD numero VARCHAR(255) NOT NULL');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE devis DROP numero, DROP statut');
        $this->addSql('ALTER TABLE devis_prestation MODIFY id INT NOT NULL');
        $this->addSql('ALTER TABLE devis_prestation DROP FOREIGN KEY FK_E169C44541DEFADA');
        $this->addSql('ALTER TABLE devis_prestation DROP FOREIGN KEY FK_E169C4459E45C554');
        $this->addSql('DROP INDEX `PRIMARY` ON devis_prestation');
        $this->addSql('ALTER TABLE devis_prestation DROP id, DROP quantity, DROP soustotal, CHANGE devis_id devis_id INT NOT NULL, CHANGE prestation_id prestation_id INT NOT NULL');
        $this->addSql('ALTER TABLE devis_prestation ADD CONSTRAINT FK_E169C44541DEFADA FOREIGN KEY (devis_id) REFERENCES devis (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE devis_prestation ADD CONSTRAINT FK_E169C4459E45C554 FOREIGN KEY (prestation_id) REFERENCES prestation (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE devis_prestation ADD PRIMARY KEY (devis_id, prestation_id)');
        $this->addSql('ALTER TABLE facture DROP numero');
    }
}
