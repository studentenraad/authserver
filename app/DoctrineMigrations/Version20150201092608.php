<?php

namespace Application\Migrations;

use Doctrine\DBAL\Migrations\AbstractMigration;
use Doctrine\DBAL\Schema\Schema;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
class Version20150201092608 extends AbstractMigration
{
    public function up(Schema $schema)
    {
        $propertiesTable = $schema->createTable('properties');
        $propertiesTable->addColumn('id', 'integer');
        $propertiesTable->addColumn('name', 'string')->setLength(25);
        $propertiesTable->addColumn('user_editable', 'boolean');
        $propertiesTable->addColumn('required', 'boolean');
        $propertiesTable->setPrimaryKey(array('id'));
        $propertiesTable->addUniqueIndex(array('name'));
        
        $userPropertiesTable = $schema->createTable('user_properties');
        $userPropertiesTable->addColumn('user_id', 'integer');
        $userPropertiesTable->addColumn('property_id', 'integer');
        $userPropertiesTable->addColumn('data', 'text')->setNotNull(false);
        $userPropertiesTable->setPrimaryKey(array('user_id', 'property_id'));
        
        $userPropertiesTable->addForeignKeyConstraint($propertiesTable, array('property_id'), array('id'));
        $userPropertiesTable->addForeignKeyConstraint($schema->getTable('auth_users'), array('user_id'), array('id'));
    }

    public function down(Schema $schema)
    {
        $schema->dropTable('properties');
        $schema->dropTable('user_properties');
    }
}