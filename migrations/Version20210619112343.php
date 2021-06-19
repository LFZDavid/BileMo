<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20210619112343 extends AbstractMigration
{
    public function getDescription(): string
    {
        return 'Fill database for demo';
    }

    public function up(Schema $schema): void
    {
        /** Supplier */
        $this->addSql('
            INSERT INTO `supplier` (`id`,`name`, `email`, `pwd`, `roles`) VALUES
            (1, "SupplierDemo", "supplier@demo.com", "$argon2id$v=19$m=65536,t=4,p=1$dsJNk8RiQbfA5MAqWmfsRA$EB3ZB+P8aF88uikEfRuEzcF7H3Xtxetaq4nUYrvBIpw", "[]");
        ');

        /** Customer */
        $this->addSql('
            INSERT INTO `customer` (`id`, `supplier_id`, `name`) VALUES
            (401, 1, "Mark McMorris"),
            (402, 1, "Tyler Chorlton"),
            (403, 1, "Alek Ostreng"),
            (404, 1, "Marcus Ostreng"),
            (405, 1, "McMorris Mattson"),
            (406, 1, "Niklas Bergrem"),
            (407, 1, "Yuki Kadono"),
            (408, 1, "Mark Mark"),
            (409, 1, "Mark Chorlton"),
            (410, 1, "Tyler Ostreng"),
            (411, 1, "Marcus Kleveland"),
            (412, 1, "Niklas Mattson"),
            (413, 1, "Yuki Niklas"),
            (414, 1, "Chorlton Kadono"),
            (415, 1, "Marcus Torgeir"),
            (416, 1, "Ostreng Kadono"),
            (417, 1, "Yuki Ostreng"),
            (418, 1, "Ostreng Chorlton"),
            (419, 1, "Tyler Niklas"),
            (420, 1, "Niklas Kadono"),
            (421, 1, "McMorris Chorlton");
        ');
    }

    public function down(Schema $schema): void
    {
        $this->addSql('DELETE FROM `customer` WHERE `supplier_id` = 1');
        $this->addSql('DELETE FROM `supplier` WHERE `id` = 1');
    }
}
