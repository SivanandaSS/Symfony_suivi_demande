<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20250826090611 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE facture_prestation (id INT AUTO_INCREMENT NOT NULL, facture_id INT NOT NULL, prestation_id INT NOT NULL, quantity NUMERIC(10, 2) NOT NULL, soustotal NUMERIC(10, 2) NOT NULL, INDEX IDX_BBD760A97F2DEE08 (facture_id), INDEX IDX_BBD760A99E45C554 (prestation_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('ALTER TABLE facture_prestation ADD CONSTRAINT FK_BBD760A97F2DEE08 FOREIGN KEY (facture_id) REFERENCES facture (id)');
        $this->addSql('ALTER TABLE facture_prestation ADD CONSTRAINT FK_BBD760A99E45C554 FOREIGN KEY (prestation_id) REFERENCES prestation (id)');
        $this->addSql('ALTER TABLE devis DROP FOREIGN KEY FK_8B27C52B7F2DEE08');
        $this->addSql('DROP INDEX UNIQ_8B27C52B7F2DEE08 ON devis');
        $this->addSql('ALTER TABLE devis DROP facture_id, CHANGE date date_devis DATE NOT NULL');
        $this->addSql('ALTER TABLE facture ADD devis_id INT NOT NULL, ADD date_emission DATE NOT NULL, ADD paiement TINYINT(1) NOT NULL, ADD total NUMERIC(10, 2) NOT NULL');
        $this->addSql('ALTER TABLE facture ADD CONSTRAINT FK_FE86641041DEFADA FOREIGN KEY (devis_id) REFERENCES devis (id)');
        $this->addSql('CREATE UNIQUE INDEX UNIQ_FE86641041DEFADA ON facture (devis_id)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE facture_prestation DROP FOREIGN KEY FK_BBD760A97F2DEE08');
        $this->addSql('ALTER TABLE facture_prestation DROP FOREIGN KEY FK_BBD760A99E45C554');
        $this->addSql('DROP TABLE facture_prestation');
        $this->addSql('ALTER TABLE devis ADD facture_id INT DEFAULT NULL, CHANGE date_devis date DATE NOT NULL');
        $this->addSql('ALTER TABLE devis ADD CONSTRAINT FK_8B27C52B7F2DEE08 FOREIGN KEY (facture_id) REFERENCES facture (id)');
        $this->addSql('CREATE UNIQUE INDEX UNIQ_8B27C52B7F2DEE08 ON devis (facture_id)');
        $this->addSql('ALTER TABLE facture DROP FOREIGN KEY FK_FE86641041DEFADA');
        $this->addSql('DROP INDEX UNIQ_FE86641041DEFADA ON facture');
        $this->addSql('ALTER TABLE facture DROP devis_id, DROP date_emission, DROP paiement, DROP total');
    }
}
