<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

final class Version20260212000523 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        $this->addSql('CREATE TABLE order_products (id BINARY(16) NOT NULL, external_id INT NOT NULL, name VARCHAR(255) NOT NULL, sku VARCHAR(50) DEFAULT NULL, ean VARCHAR(20) DEFAULT NULL, price_brutto NUMERIC(10, 2) NOT NULL, quantity INT NOT NULL, order_id BINARY(16) NOT NULL, UNIQUE INDEX UNIQ_5242B8EB9F75D7B0 (external_id), INDEX IDX_5242B8EB8D9F6D38 (order_id), PRIMARY KEY (id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci`');
        $this->addSql('CREATE TABLE orders (id BINARY(16) NOT NULL, external_id INT NOT NULL, marketplace VARCHAR(50) NOT NULL, customer_name VARCHAR(255) NOT NULL, total_amount NUMERIC(10, 2) NOT NULL, currency VARCHAR(3) NOT NULL, created_at DATETIME NOT NULL, email VARCHAR(150) DEFAULT NULL, phone VARCHAR(100) DEFAULT NULL, payment_method VARCHAR(100) DEFAULT NULL, paid TINYINT NOT NULL, delivery_method VARCHAR(100) DEFAULT NULL, delivery_price NUMERIC(10, 2) NOT NULL, user_comments VARCHAR(1000) DEFAULT NULL, admin_comments VARCHAR(1000) DEFAULT NULL, user_login VARCHAR(100) DEFAULT NULL, external_source_order_id VARCHAR(50) DEFAULT NULL, delivery_fullname VARCHAR(100) DEFAULT NULL, delivery_address VARCHAR(156) DEFAULT NULL, delivery_postcode VARCHAR(20) DEFAULT NULL, delivery_city VARCHAR(100) DEFAULT NULL, delivery_country_code VARCHAR(2) DEFAULT NULL, UNIQUE INDEX UNIQ_E52FFDEE9F75D7B0 (external_id), PRIMARY KEY (id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci`');
        $this->addSql('ALTER TABLE order_products ADD CONSTRAINT FK_5242B8EB8D9F6D38 FOREIGN KEY (order_id) REFERENCES orders (id) ON DELETE CASCADE');
    }

    public function down(Schema $schema): void
    {
        $this->addSql('ALTER TABLE order_products DROP FOREIGN KEY FK_5242B8EB8D9F6D38');
        $this->addSql('DROP TABLE order_products');
        $this->addSql('DROP TABLE orders');
    }
}
