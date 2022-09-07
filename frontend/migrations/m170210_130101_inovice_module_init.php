<?php

use yii\db\Schema;

class m170210_130101_inovice_module_init extends \yii\db\Migration
{
    public function up()
    {
        $tableOptions = null;
        if ($this->db->driverName === 'mysql') {
            $tableOptions = 'CHARACTER SET utf8 COLLATE utf8_general_ci ENGINE=InnoDB';
        }
        
        $this->createTable('invoice', [
            'id' => $this->primaryKey(),
            'external_id' => $this->integer(11),
            'paymentmethod' => $this->string(255),
            'paymentdate' => $this->date(),
            'paymentstate' => $this->string(255),
            'disposaldate_format' => $this->string(255),
            'disposaldate_empty' => $this->integer(11)->defaultValue(0),
            'disposaldate' => $this->date(),
            'date' => $this->date(),
            'period' => $this->integer(11),
            'total' => $this->decimal(11,2),
            'total_composed' => $this->decimal(11,2),
            'alreadypaid' => $this->decimal(11,2),
            'alreadypaid_initial' => $this->decimal(11,2),
            'remaining' => $this->decimal(11,2),
            'numer' => $this->integer(11),
            'day' => $this->integer(11),
            'month' => $this->integer(11),
            'year' => $this->integer(11),
            'fullnumber' => $this->string(255),
            'semitemplatenumber' => $this->integer(11),
            'type' => $this->string(255),
            'correction_type' => $this->string(255),
            'corrections' => $this->integer(11),
            'currency' => $this->string(255),
            'currency_exchange' => $this->decimal(8,4),
            'currency_label' => $this->string(255),
            'currency_date' => $this->date(),
            'price_currency_exchange' => $this->decimal(8,4),
            'good_price_group_currency_exchange' => $this->decimal(8,4),
            'template' => $this->integer(11),
            'auto_send' => $this->integer(11),
            'description' => $this->text(),
            'header' => $this->text(),
            'footer' => $this->text(),
            'user_name' => $this->string(255),
            'schema' => $this->string(255),
            'schema_bill' => $this->integer(11),
            'schema_canceled' => $this->integer(11),
            'register_description' => $this->text(),
            'netto' => $this->decimal(11,2),
            'tax' => $this->decimal(11,2),
            'signed' => $this->integer(11),
            'hash' => $this->string(255),
            'warehouse_type' => $this->string(255),
            'notes' => $this->integer(11),
            'documents' => $this->integer(11),
            'tags' => $this->string(255),
            'price_type' => $this->string(255),
            'create_time' => $this->datetime(),
            'update_time' => $this->datetime(),
            ], $tableOptions);
                $this->createTable('invoice_content', [
            'id' => $this->primaryKey(),
            'invoice_id' => $this->integer(11)->notNull(),
            'external_id' => $this->integer(11),
            'cassification' => $this->string(255),
            'unit' => $this->string(255),
            'count' => $this->decimal(11,4),
            'price' => $this->decimal(11,2),
            'discount' => $this->integer(11)->defaultValue(0),
            'discount_percent' => $this->integer(11)->defaultValue(0),
            'netto' => $this->decimal(11,2),
            'brutto' => $this->decimal(11,2),
            'vat' => $this->decimal(6,3),
            'lumpcode' => $this->string(255),
            'create_time' => $this->datetime(),
            'update_time' => $this->datetime(),
            'FOREIGN KEY ([[invoice_id]]) REFERENCES invoice ([[id]]) ON DELETE CASCADE ON UPDATE CASCADE',
            ], $tableOptions);
                
    }

    public function down()
    {
        $this->dropTable('invoice_content');
        $this->dropTable('invoice');
    }
}
