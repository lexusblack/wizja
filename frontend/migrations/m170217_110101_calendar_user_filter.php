<?php

use yii\db\Schema;

class m170217_110101_calendar_user_filter extends \yii\db\Migration
{
    public function up()
    {
        $tableOptions = null;
        if ($this->db->driverName === 'mysql') {
            $tableOptions = 'CHARACTER SET utf8 COLLATE utf8_general_ci ENGINE=InnoDB';
        }
        
        $this->createTable('calendar_user_filter', [
            'id' => $this->primaryKey(),
            'user_id' => $this->integer(11)->notNull(),
            'type' => $this->string(255),
            'manager_id' => $this->integer(11),
            'coordinator' => $this->integer(11),
            'department' => $this->integer(11),
            'contact' => $this->integer(11),
            'customer' => $this->integer(11),
            'projectStatus' => $this->string(255),
            'rentStatus' => $this->integer(11),
            'FOREIGN KEY ([[user_id]]) REFERENCES user ([[id]]) ON DELETE CASCADE ON UPDATE CASCADE',
            'FOREIGN KEY ([[department]]) REFERENCES department ([[id]]) ON DELETE CASCADE ON UPDATE CASCADE',
            'FOREIGN KEY ([[contact]]) REFERENCES contact ([[id]]) ON DELETE CASCADE ON UPDATE CASCADE',
            'FOREIGN KEY ([[customer]]) REFERENCES customer ([[id]]) ON DELETE CASCADE ON UPDATE CASCADE',
            ], $tableOptions);
                
    }

    public function down()
    {
        $this->execute('SET foreign_key_checks = 0');
        $this->dropTable('calendar_user_filter');
        $this->execute('SET foreign_key_checks = 1');
    }
}
