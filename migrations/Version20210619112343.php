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

        /** Product */
        $this->addSql('
            INSERT INTO `product` (`id`, `name`, `brand`, `stock`, `price`) VALUES
            (401, "Apple-0", "Apple", 234, 0),
            (402, "Apple-1", "Apple", 234, 12.34),
            (403, "Apple-2", "Apple", 234, 24.68),
            (404, "Apple-3", "Apple", 234, 37.02),
            (405, "Apple-4", "Apple", 234, 49.36),
            (406, "Apple-5", "Apple", 234, 61.7),
            (407, "Apple-6", "Apple", 234, 74.04),
            (408, "Apple-7", "Apple", 234, 86.38),
            (409, "Samsung-0", "Samsung", 234, 0),
            (410, "Samsung-1", "Samsung", 234, 12.34),
            (411, "Samsung-2", "Samsung", 234, 24.68),
            (412, "Samsung-3", "Samsung", 234, 37.02),
            (413, "Samsung-4", "Samsung", 234, 49.36),
            (414, "Samsung-5", "Samsung", 234, 61.7),
            (415, "Samsung-6", "Samsung", 234, 74.04),
            (416, "Samsung-7", "Samsung", 234, 86.38),
            (417, "Huawei-0", "Huawei", 234, 0),
            (418, "Huawei-1", "Huawei", 234, 12.34),
            (419, "Huawei-2", "Huawei", 234, 24.68),
            (420, "Huawei-3", "Huawei", 234, 37.02),
            (421, "Huawei-4", "Huawei", 234, 49.36),
            (422, "Huawei-5", "Huawei", 234, 61.7),
            (423, "Huawei-6", "Huawei", 234, 74.04),
            (424, "Huawei-7", "Huawei", 234, 86.38),
            (425, "Sony-0", "Sony", 234, 0),
            (426, "Sony-1", "Sony", 234, 12.34),
            (427, "Sony-2", "Sony", 234, 24.68),
            (428, "Sony-3", "Sony", 234, 37.02),
            (429, "Sony-4", "Sony", 234, 49.36),
            (430, "Sony-5", "Sony", 234, 61.7),
            (431, "Sony-6", "Sony", 234, 74.04),
            (432, "Sony-7", "Sony", 234, 86.38),
            (433, "Honor-0", "Honor", 234, 0),
            (434, "Honor-1", "Honor", 234, 12.34),
            (435, "Honor-2", "Honor", 234, 24.68),
            (436, "Honor-3", "Honor", 234, 37.02),
            (437, "Honor-4", "Honor", 234, 49.36),
            (438, "Honor-5", "Honor", 234, 61.7),
            (439, "Honor-6", "Honor", 234, 74.04),
            (440, "Honor-7", "Honor", 234, 86.38),
            (441, "LG-0", "LG", 234, 0),
            (442, "LG-1", "LG", 234, 12.34),
            (443, "LG-2", "LG", 234, 24.68),
            (444, "LG-3", "LG", 234, 37.02),
            (445, "LG-4", "LG", 234, 49.36),
            (446, "LG-5", "LG", 234, 61.7),
            (447, "LG-6", "LG", 234, 74.04),
            (448, "LG-7", "LG", 234, 86.38);
        ');
    }

    public function down(Schema $schema): void
    {
        $this->addSql('DELETE FROM `customer` WHERE `supplier_id` = 1');
        $this->addSql('DELETE FROM `supplier` WHERE `id` = 1');
        $this->addSql('DELETE FROM `product` WHERE `id` > 400 AND `id` < 449');
    }
}
