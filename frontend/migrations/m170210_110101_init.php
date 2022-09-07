<?php

use yii\db\Schema;

class m170210_110101_init extends \yii\db\Migration
{
    public function up()
    {
        $tableOptions = null;
        if ($this->db->driverName === 'mysql') {
            $tableOptions = 'CHARACTER SET utf8 COLLATE utf8_general_ci ENGINE=InnoDB';
        }

        $this->createTable('addon_rate', [
            'id' => $this->primaryKey(),
            'amount' => $this->decimal(11,2)->notNull(),
            'level' => $this->integer(11)->notNull()->defaultValue(1),
            'create_time' => $this->datetime(),
            'update_time' => $this->datetime(),
            'period' => $this->integer(11)->defaultValue(0),
        ], $tableOptions);
        $this->createTable('customer', [
            'id' => $this->primaryKey(),
            'company' => $this->string(255),
            'name' => $this->string(255),
            'address' => $this->string(255),
            'city' => $this->string(255),
            'zip' => $this->string(255),
            'phone' => $this->string(255),
            'email' => $this->string(255),
            'info' => $this->text(),
            'create_time' => $this->datetime(),
            'update_time' => $this->datetime(),
            'type' => $this->integer(11)->defaultValue(1),
            'status' => $this->integer(11)->defaultValue(1),
            'logo' => $this->string(255),
            'nip' => $this->string(255),
            'bank_account' => $this->string(255),
        ], $tableOptions);
        $this->createTable('location', [
            'id' => $this->primaryKey(),
            'name' => $this->string(255),
            'address' => $this->string(255),
            'city' => $this->string(255),
            'zip' => $this->string(255),
            'info' => $this->text(),
            'latitude' => $this->decimal(10,8),
            'longitude' => $this->decimal(10,8),
            'create_time' => $this->datetime(),
            'update_time' => $this->datetime(),
            'type' => $this->integer(11)->defaultValue(1),
            'status' => $this->integer(11)->defaultValue(1),
            'travel_time' => $this->string(255),
            'manager_phone' => $this->string(255),
            'electrician_phone' => $this->string(255),
            'distance' => $this->decimal(11,2),
            'photo' => $this->string(255),
            'country' => $this->string(255),
            'rent_price' => $this->decimal(11,2),
        ], $tableOptions);
        $this->createTable('contact', [
            'id' => $this->primaryKey(),
            'first_name' => $this->string(255),
            'last_name' => $this->string(255)->notNull(),
            'phone' => $this->string(255),
            'email' => $this->string(255),
            'position' => $this->string(255),
            'info' => $this->text(),
            'customer_id' => $this->integer(11)->notNull(),
            'photo' => $this->string(255),
            'create_time' => $this->datetime(),
            'update_time' => $this->datetime(),
            'type' => $this->integer(11)->defaultValue(1),
            'status' => $this->integer(11)->defaultValue(1),
            'FOREIGN KEY ([[customer_id]]) REFERENCES customer ([[id]]) ON DELETE CASCADE ON UPDATE CASCADE',
        ], $tableOptions);
        $this->createTable('user', [
            'id' => $this->primaryKey(),
            'username' => $this->string(255)->notNull(),
            'auth_key' => $this->string(32)->notNull(),
            'password_hash' => $this->string(255)->notNull(),
            'password_reset_token' => $this->string(255),
            'email' => $this->string(255)->notNull(),
            'role' => $this->smallInteger(6)->notNull()->defaultValue(10),
            'status' => $this->smallInteger(6)->notNull()->defaultValue(10),
            'create_time' => $this->datetime(),
            'update_time' => $this->datetime(),
            'first_name' => $this->string(255)->notNull(),
            'last_name' => $this->string(255)->notNull(),
            'last_visit' => $this->datetime(),
            'photo' => $this->string(255),
            'birth_date' => $this->date(),
            'pesel' => $this->char(11),
            'id_card' => $this->string(255),
            'phone' => $this->string(255),
            'type' => $this->integer(11)->defaultValue(1),
            'rate_type' => $this->integer(11),
            'rate_amount' => $this->decimal(10,2),
            'overtime_amount' => $this->decimal(10,2),
            'base_hours' => $this->integer(11),
        ], $tableOptions);
        $this->createTable('event', [
            'id' => $this->primaryKey(),
            'name' => $this->string(255)->notNull(),
            'location_id' => $this->integer(11),
            'customer_id' => $this->integer(11),
            'contact_id' => $this->integer(11),
            'manager_id' => $this->integer(11),
            'info' => $this->text(),
            'description' => $this->text(),
            'code' => $this->string(255),
            'event_start' => $this->datetime(),
            'event_end' => $this->datetime(),
            'status' => $this->integer(11)->defaultValue(1),
            'type' => $this->integer(11)->defaultValue(1),
            'create_time' => $this->datetime(),
            'update_time' => $this->datetime(),
            'packing_start' => $this->datetime(),
            'packing_end' => $this->datetime(),
            'montage_start' => $this->datetime(),
            'montage_end' => $this->datetime(),
            'readiness_start' => $this->datetime(),
            'readiness_end' => $this->datetime(),
            'practice_start' => $this->datetime(),
            'practice_end' => $this->datetime(),
            'disassembly_start' => $this->datetime(),
            'disassembly_end' => $this->datetime(),
            'packing_type' => $this->integer(11)->defaultValue(0),
            'montage_type' => $this->integer(11)->defaultValue(0),
            'readiness_type' => $this->integer(11)->defaultValue(0),
            'practice_type' => $this->integer(11)->defaultValue(0),
            'disassembly_type' => $this->integer(11)->defaultValue(0),
            'level' => $this->integer(11)->defaultValue(1),
            'route_start' => $this->string(255),
            'route_end' => $this->string(255),
            'provision' => $this->decimal(10,3)->defaultValue('0.050'),
            'project_done' => $this->integer(11)->defaultValue(0),
            'invoice_issued' => $this->integer(11)->defaultValue(0),
            'invoice_sent' => $this->integer(11)->defaultValue(0),
            'transfer_booked' => $this->integer(255)->defaultValue(0),
            'invoice_number' => $this->string(255),
            'creator_id' => $this->integer(11),
            'provision_type' => $this->integer(11)->defaultValue(1),
            'FOREIGN KEY ([[customer_id]]) REFERENCES customer ([[id]]) ON DELETE CASCADE ON UPDATE CASCADE',
            'FOREIGN KEY ([[location_id]]) REFERENCES location ([[id]]) ON DELETE CASCADE ON UPDATE CASCADE',
            'FOREIGN KEY ([[contact_id]]) REFERENCES contact ([[id]]) ON DELETE CASCADE ON UPDATE CASCADE',
            'FOREIGN KEY ([[creator_id]]) REFERENCES user ([[id]]) ON DELETE CASCADE ON UPDATE CASCADE',
        ], $tableOptions);
        $this->createTable('attachment', [
            'id' => $this->primaryKey(),
            'filename' => $this->string(255),
            'extension' => $this->string(255),
            'type' => $this->integer(11)->defaultValue(1),
            'status' => $this->integer(11)->defaultValue(1),
            'content' => $this->text(),
            'create_time' => $this->datetime(),
            'update_time' => $this->datetime(),
            'info' => $this->text(),
            'event_id' => $this->integer(11)->notNull(),
            'mime_type' => $this->string(255),
            'base_name' => $this->string(255),
            'FOREIGN KEY ([[event_id]]) REFERENCES event ([[id]]) ON DELETE CASCADE ON UPDATE CASCADE',
        ], $tableOptions);
        $this->createTable('auth_rule', [
            'name' => $this->string(64)->notNull(),
            'data' => $this->text(),
            'created_at' => $this->integer(11),
            'updated_at' => $this->integer(11),
            'PRIMARY KEY ([[name]])',
        ], $tableOptions);
        $this->createTable('auth_item', [
            'name' => $this->string(64)->notNull(),
            'type' => $this->integer(11)->notNull(),
            'description' => $this->text(),
            'rule_name' => $this->string(64),
            'data' => $this->text(),
            'created_at' => $this->integer(11),
            'updated_at' => $this->integer(11),
            'PRIMARY KEY ([[name]])',
            'FOREIGN KEY ([[rule_name]]) REFERENCES auth_rule ([[name]]) ON DELETE CASCADE ON UPDATE CASCADE',
        ], $tableOptions);
        $this->createTable('auth_assignment', [
            'item_name' => $this->string(64)->notNull(),
            'user_id' => $this->string(64)->notNull(),
            'created_at' => $this->integer(11),
            'PRIMARY KEY ([[item_name]], [[user_id]])',
            'FOREIGN KEY ([[item_name]]) REFERENCES auth_item ([[name]]) ON DELETE CASCADE ON UPDATE CASCADE',
        ], $tableOptions);
        $this->createTable('auth_item_child', [
            'parent' => $this->string(64)->notNull(),
            'child' => $this->string(64)->notNull(),
            'PRIMARY KEY ([[parent]], [[child]])',
            'FOREIGN KEY ([[child]]) REFERENCES auth_item ([[name]]) ON DELETE CASCADE ON UPDATE CASCADE',
        ], $tableOptions);
        $this->createTable('customer_discount', [
            'id' => $this->primaryKey(),
            'created_at' => $this->datetime()->notNull(),
            'updated_at' => $this->datetime()->notNull(),
            'discount' => $this->integer(11)->notNull(),
        ], $tableOptions);
        $this->createTable('gear_category', [
            'id' => $this->primaryKey(),
            'root' => $this->integer(11),
            'lft' => $this->integer(11)->notNull(),
            'rgt' => $this->integer(11)->notNull(),
            'lvl' => $this->smallInteger(5)->notNull(),
            'name' => $this->string(60)->notNull(),
            'icon' => $this->string(255),
            'icon_type' => $this->smallInteger(1)->notNull()->defaultValue(1),
            'active' => $this->smallInteger(1)->notNull()->defaultValue(1),
            'selected' => $this->smallInteger(1)->notNull()->defaultValue(0),
            'disabled' => $this->smallInteger(1)->notNull()->defaultValue(0),
            'readonly' => $this->smallInteger(1)->notNull()->defaultValue(0),
            'visible' => $this->smallInteger(1)->notNull()->defaultValue(1),
            'collapsed' => $this->smallInteger(1)->notNull()->defaultValue(0),
            'movable_u' => $this->smallInteger(1)->notNull()->defaultValue(1),
            'movable_d' => $this->smallInteger(1)->notNull()->defaultValue(1),
            'movable_l' => $this->smallInteger(1)->notNull()->defaultValue(1),
            'movable_r' => $this->smallInteger(1)->notNull()->defaultValue(1),
            'removable' => $this->smallInteger(1)->notNull()->defaultValue(1),
            'removable_all' => $this->smallInteger(1)->notNull()->defaultValue(0),
        ], $tableOptions);
        $this->createTable('customer_discount_category', [
            'customer_discount_id' => $this->integer(11)->notNull(),
            'category_id' => $this->integer(11)->notNull(),
            'PRIMARY KEY ([[customer_discount_id]], [[category_id]])',
            'FOREIGN KEY ([[customer_discount_id]]) REFERENCES customer_discount ([[id]]) ON DELETE CASCADE ON UPDATE CASCADE',
            'FOREIGN KEY ([[category_id]]) REFERENCES gear_category ([[id]]) ON DELETE CASCADE ON UPDATE CASCADE',
        ], $tableOptions);
        $this->createTable('customer_discount_customer', [
            'customer_discount_id' => $this->integer(11)->notNull(),
            'customer_id' => $this->integer(11)->notNull(),
            'PRIMARY KEY ([[customer_discount_id]], [[customer_id]])',
            'FOREIGN KEY ([[customer_id]]) REFERENCES customer ([[id]]) ON DELETE CASCADE ON UPDATE CASCADE',
            'FOREIGN KEY ([[customer_discount_id]]) REFERENCES customer_discount ([[id]]) ON DELETE CASCADE ON UPDATE CASCADE',
        ], $tableOptions);
        $this->createTable('department', [
            'id' => $this->primaryKey(),
            'name' => $this->string(255)->notNull(),
            'status' => $this->integer(11),
            'type' => $this->integer(11),
            'create_time' => $this->datetime(),
            'update_time' => $this->datetime(),
            'color' => $this->string(255),
        ], $tableOptions);
        $this->createTable('event_department', [
            'event_id' => $this->integer(11)->notNull(),
            'department_id' => $this->integer(11)->notNull(),
            'PRIMARY KEY ([[event_id]], [[department_id]])',
            'FOREIGN KEY ([[event_id]]) REFERENCES event ([[id]]) ON DELETE CASCADE ON UPDATE CASCADE',
            'FOREIGN KEY ([[department_id]]) REFERENCES department ([[id]]) ON DELETE CASCADE ON UPDATE CASCADE',
        ], $tableOptions);
        $this->createTable('event_expense', [
            'id' => $this->primaryKey(),
            'event_id' => $this->integer(11)->notNull(),
            'name' => $this->string(255)->notNull(),
            'department_id' => $this->integer(11),
            'amount' => $this->decimal(11,2)->defaultValue('0.00'),
            'amount_customer' => $this->decimal(11,2)->defaultValue('0.00'),
            'profit' => $this->decimal(11,2)->defaultValue('0.00'),
            'invoice_nr' => $this->string(255),
            'customer_id' => $this->integer(11),
            'status' => $this->integer(11)->defaultValue(1),
            'type' => $this->integer(11)->defaultValue(1),
            'create_time' => $this->datetime(),
            'update_time' => $this->datetime(),
            'group_id' => $this->integer(11),
            'group_amount' => $this->decimal(11,2)->defaultValue('0.00'),
            'group_amount_customer' => $this->decimal(11,2),
            'start_time' => $this->datetime(),
            'end_time' => $this->datetime(),
            'FOREIGN KEY ([[event_id]]) REFERENCES event ([[id]]) ON DELETE CASCADE ON UPDATE CASCADE',
            'FOREIGN KEY ([[department_id]]) REFERENCES department ([[id]]) ON DELETE CASCADE ON UPDATE CASCADE',
            'FOREIGN KEY ([[customer_id]]) REFERENCES customer ([[id]]) ON DELETE CASCADE ON UPDATE CASCADE',
        ], $tableOptions);
        $this->createTable('gear_group', [
            'id' => $this->primaryKey(),
            'name' => $this->string(255)->notNull(),
            'create_time' => $this->datetime(),
            'update_time' => $this->datetime(),
            'type' => $this->integer(11)->defaultValue(1),
            'status' => $this->integer(11)->defaultValue(1),
            'width' => $this->integer(11),
            'height' => $this->integer(11),
            'depth' => $this->integer(11),
            'volume' => $this->integer(11),
            'warehouse' => $this->string(255),
            'location' => $this->string(255),
            'weight' => $this->integer(11),
        ], $tableOptions);
        $this->createTable('gear', [
            'id' => $this->primaryKey(),
            'name' => $this->string(255)->notNull(),
            'quantity' => $this->integer(11),
            'available' => $this->integer(11),
            'brightness' => $this->decimal(11,2),
            'power_consumption' => $this->decimal(11,2),
            'status' => $this->integer(11)->defaultValue(1),
            'type' => $this->integer(11)->defaultValue(1),
            'category_id' => $this->integer(11)->notNull(),
            'width' => $this->decimal(11,2),
            'height' => $this->decimal(11,2),
            'volume' => $this->decimal(11,2),
            'depth' => $this->decimal(11,2),
            'weight' => $this->decimal(11,2),
            'weight_case' => $this->decimal(11,2),
            'info' => $this->text(),
            'photo' => $this->string(255),
            'group_id' => $this->integer(11),
            'create_time' => $this->datetime(),
            'update_time' => $this->datetime(),
            'price' => $this->decimal(11,2),
            'no_items' => $this->integer(11)->defaultValue(0),
            'sort_order' => $this->integer(11)->defaultValue(999),
            'FOREIGN KEY ([[category_id]]) REFERENCES gear_category ([[id]]) ON DELETE CASCADE ON UPDATE CASCADE',
            'FOREIGN KEY ([[group_id]]) REFERENCES gear_group ([[id]]) ON DELETE CASCADE ON UPDATE CASCADE',
        ], $tableOptions);
        $this->createTable('gear_item', [
            'id' => $this->primaryKey(),
            'name' => $this->string(255)->notNull(),
            'photo' => $this->string(255),
            'weight' => $this->decimal(11,2),
            'width' => $this->decimal(11,2),
            'height' => $this->decimal(11,2),
            'depth' => $this->decimal(11,2),
            'weight_case' => $this->decimal(11,2),
            'height_case' => $this->decimal(11,2),
            'depth_case' => $this->decimal(11,2),
            'width_case' => $this->decimal(11,2),
            'volume' => $this->decimal(11,2),
            'warehouse' => $this->string(255),
            'location' => $this->string(255),
            'code' => $this->string(255),
            'serial' => $this->string(255),
            'lamp_hours' => $this->decimal(11,2),
            'description' => $this->text(),
            'info' => $this->text(),
            'purchase_price' => $this->decimal(11,2),
            'refund_amount' => $this->decimal(11,2),
            'test_date' => $this->datetime(),
            'tester' => $this->string(255),
            'test_status' => $this->string(255),
            'service' => $this->text(),
            'gear_id' => $this->integer(11)->notNull(),
            'status' => $this->integer(11)->defaultValue(1),
            'type' => $this->integer(11)->defaultValue(1),
            'create_time' => $this->datetime(),
            'update_time' => $this->datetime(),
            'group_id' => $this->integer(11),
            'number' => $this->integer(11),
            'FOREIGN KEY ([[gear_id]]) REFERENCES gear ([[id]]) ON DELETE CASCADE ON UPDATE CASCADE',
            'FOREIGN KEY ([[group_id]]) REFERENCES gear_group ([[id]]) ON DELETE CASCADE ON UPDATE CASCADE',
        ], $tableOptions);
        $this->createTable('event_gear_item', [
            'event_id' => $this->integer(11)->notNull(),
            'gear_item_id' => $this->integer(11)->notNull(),
            'quantity' => $this->integer(11)->defaultValue(0),
            'start_time' => $this->datetime(),
            'end_time' => $this->datetime(),
            'type' => $this->integer(11)->defaultValue(1),
            'create_time' => $this->datetime(),
            'update_time' => $this->datetime(),
            'PRIMARY KEY ([[event_id]], [[gear_item_id]])',
            'FOREIGN KEY ([[event_id]]) REFERENCES event ([[id]]) ON DELETE CASCADE ON UPDATE CASCADE',
            'FOREIGN KEY ([[gear_item_id]]) REFERENCES gear_item ([[id]]) ON DELETE CASCADE ON UPDATE CASCADE',
        ], $tableOptions);
        $this->createTable('event_invoice', [
            'id' => $this->primaryKey(),
            'filename' => $this->string(255),
            'extension' => $this->string(255),
            'type' => $this->integer(11)->defaultValue(1),
            'status' => $this->integer(11)->defaultValue(1),
            'content' => $this->text(),
            'create_time' => $this->datetime(),
            'update_time' => $this->datetime(),
            'info' => $this->text(),
            'event_id' => $this->integer(11)->notNull(),
            'mime_type' => $this->string(255),
            'base_name' => $this->string(255),
            'FOREIGN KEY ([[event_id]]) REFERENCES event ([[id]]) ON DELETE CASCADE ON UPDATE CASCADE',
        ], $tableOptions);
        $this->createTable('event_message', [
            'id' => $this->primaryKey(),
            'event_id' => $this->integer(11)->notNull(),
            'title' => $this->string(255),
            'content' => $this->text()->notNull(),
            'create_time' => $this->datetime(),
            'update_time' => $this->datetime(),
            'push' => $this->integer(11)->defaultValue(0),
            'sms' => $this->integer(11)->defaultValue(0),
            'email' => $this->integer(11)->defaultValue(0),
            'recipients_push' => $this->text(),
            'recipients_sms' => $this->text(),
            'recipients_email' => $this->text(),
            'sent_time' => $this->datetime(),
            'type' => $this->integer(11)->defaultValue(1),
            'status' => $this->integer(11)->defaultValue(1),
            'FOREIGN KEY ([[event_id]]) REFERENCES event ([[id]]) ON DELETE CASCADE ON UPDATE CASCADE',
        ], $tableOptions);
        $this->createTable('outer_gear', [
            'id' => $this->primaryKey(),
            'name' => $this->string(255)->notNull(),
            'quantity' => $this->integer(11),
            'brightness' => $this->string(255),
            'power_consumption' => $this->decimal(11,2),
            'status' => $this->integer(11)->defaultValue(1),
            'type' => $this->integer(11)->defaultValue(1),
            'category_id' => $this->integer(11)->notNull(),
            'width' => $this->decimal(11,2),
            'height' => $this->decimal(11,2),
            'volume' => $this->decimal(11,2),
            'depth' => $this->decimal(11,2),
            'weight' => $this->decimal(11,2),
            'info' => $this->text(),
            'photo' => $this->string(255),
            'create_time' => $this->datetime(),
            'update_time' => $this->datetime(),
            'price' => $this->decimal(11,2),
            'selling_price' => $this->decimal(11,2),
            'sort_order' => $this->integer(11)->defaultValue(999),
            'company_name' => $this->string(255),
            'FOREIGN KEY ([[category_id]]) REFERENCES gear_category ([[id]]) ON DELETE CASCADE ON UPDATE CASCADE',
        ], $tableOptions);
        $this->createTable('event_outer_gear', [
            'event_id' => $this->integer(11)->notNull(),
            'outer_gear_id' => $this->integer(11)->notNull(),
            'quantity' => $this->integer(11),
            'discount' => $this->integer(11),
            'start_time' => $this->datetime(),
            'end_time' => $this->datetime(),
            'type' => $this->integer(11)->notNull()->defaultValue(1),
            'PRIMARY KEY ([[event_id]], [[outer_gear_id]])',
            'FOREIGN KEY ([[event_id]]) REFERENCES event ([[id]]) ON DELETE CASCADE ON UPDATE CASCADE',
            'FOREIGN KEY ([[outer_gear_id]]) REFERENCES outer_gear ([[id]]) ON DELETE CASCADE ON UPDATE CASCADE',
        ], $tableOptions);
        $this->createTable('event_user', [
            'id' => $this->primaryKey(),
            'event_id' => $this->integer(11)->notNull(),
            'user_id' => $this->integer(11)->notNull(),
            'start_time' => $this->datetime(),
            'end_time' => $this->datetime(),
            'type' => $this->integer(11)->defaultValue(1),
            'create_time' => $this->datetime(),
            'update_time' => $this->datetime(),
            'FOREIGN KEY ([[event_id]]) REFERENCES event ([[id]]) ON DELETE CASCADE ON UPDATE CASCADE',
            'FOREIGN KEY ([[user_id]]) REFERENCES user ([[id]]) ON DELETE CASCADE ON UPDATE CASCADE',
        ], $tableOptions);
        $this->createTable('event_user_addon', [
            'id' => $this->primaryKey(),
            'user_id' => $this->integer(11)->notNull(),
            'event_id' => $this->integer(11)->notNull(),
            'name' => $this->string(255)->notNull(),
            'amount' => $this->decimal(11,2)->notNull()->defaultValue('0.00'),
            'info' => $this->text(),
            'create_time' => $this->datetime(),
            'update_time' => $this->datetime(),
            'start_time' => $this->datetime(),
            'end_time' => $this->datetime(),
            'FOREIGN KEY ([[user_id]]) REFERENCES user ([[id]]) ON DELETE CASCADE ON UPDATE CASCADE',
            'FOREIGN KEY ([[event_id]]) REFERENCES event ([[id]]) ON DELETE CASCADE ON UPDATE CASCADE',
        ], $tableOptions);
        $this->createTable('event_user_allowance', [
            'id' => $this->primaryKey(),
            'event_id' => $this->integer(11)->notNull(),
            'user_id' => $this->integer(11)->notNull(),
            'amount' => $this->decimal(11,2)->notNull(),
            'type' => $this->integer(11)->defaultValue(1),
            'status' => $this->integer(11)->defaultValue(1),
            'create_time' => $this->datetime(),
            'update_time' => $this->datetime(),
            'start_time' => $this->datetime(),
            'end_time' => $this->datetime(),
            'FOREIGN KEY ([[event_id]]) REFERENCES event ([[id]]) ON DELETE CASCADE ON UPDATE CASCADE',
            'FOREIGN KEY ([[user_id]]) REFERENCES user ([[id]]) ON DELETE CASCADE ON UPDATE CASCADE',
        ], $tableOptions);
        $this->createTable('user_event_role', [
            'id' => $this->primaryKey(),
            'name' => $this->string(255)->notNull(),
            'salary' => $this->decimal(11,2)->defaultValue('0.00'),
            'salary_customer' => $this->decimal(11,2)->defaultValue('0.00'),
            'create_time' => $this->datetime(),
            'update_time' => $this->datetime(),
            'compatibility' => $this->integer(11)->defaultValue(1),
        ], $tableOptions);
        $this->createTable('event_user_role', [
            'event_user_id' => $this->integer(11)->notNull(),
            'user_event_role_id' => $this->integer(11)->notNull(),
            'create_time' => $this->datetime(),
            'update_time' => $this->datetime(),
            'PRIMARY KEY ([[event_user_id]], [[user_event_role_id]])',
            'FOREIGN KEY ([[event_user_id]]) REFERENCES event_user ([[id]]) ON DELETE CASCADE ON UPDATE CASCADE',
            'FOREIGN KEY ([[user_event_role_id]]) REFERENCES user_event_role ([[id]]) ON DELETE CASCADE ON UPDATE CASCADE',
        ], $tableOptions);
        $this->createTable('event_user_working_time', [
            'id' => $this->primaryKey(),
            'user_id' => $this->integer(11)->notNull(),
            'event_id' => $this->integer(11)->notNull(),
            'start_time' => $this->datetime()->notNull(),
            'end_time' => $this->datetime()->notNull(),
            'duration' => $this->integer(11)->defaultValue(0),
            'create_time' => $this->datetime(),
            'update_time' => $this->datetime(),
            'department_id' => $this->integer(11)->notNull(),
            'role_id' => $this->integer(11),
            'FOREIGN KEY ([[user_id]]) REFERENCES user ([[id]]) ON DELETE CASCADE ON UPDATE CASCADE',
            'FOREIGN KEY ([[event_id]]) REFERENCES event ([[id]]) ON DELETE CASCADE ON UPDATE CASCADE',
            'FOREIGN KEY ([[department_id]]) REFERENCES department ([[id]]) ON DELETE CASCADE ON UPDATE CASCADE',
            'FOREIGN KEY ([[role_id]]) REFERENCES user_event_role ([[id]]) ON DELETE CASCADE ON UPDATE CASCADE',
        ], $tableOptions);
        $this->createTable('vehicle', [
            'id' => $this->primaryKey(),
            'name' => $this->string(255)->notNull(),
            'photo' => $this->string(255),
            'registration_number' => $this->string(255),
            'vin_number' => $this->string(255),
            'capacity' => $this->decimal(11,2),
            'volume' => $this->decimal(11,2),
            'fuel_consumption' => $this->decimal(11,2),
            'inspection_date' => $this->date(),
            'oc_date' => $this->date(),
            'price_km' => $this->decimal(11,2),
            'price_city' => $this->decimal(11,0),
            'reminder' => $this->integer(11)->defaultValue(0),
            'price_rent' => $this->decimal(11,2),
            'type' => $this->integer(11)->defaultValue(1),
            'status' => $this->integer(11)->defaultValue(1),
            'create_time' => $this->datetime(),
            'update_time' => $this->datetime(),
            'info' => $this->text(),
            'description' => $this->text(),
        ], $tableOptions);
        $this->createTable('event_vehicle', [
            'event_id' => $this->integer(11)->notNull(),
            'vehicle_id' => $this->integer(11)->notNull(),
            'start_time' => $this->datetime(),
            'end_time' => $this->datetime(),
            'type' => $this->integer(11)->defaultValue(1),
            'create_time' => $this->datetime(),
            'update_time' => $this->datetime(),
            'PRIMARY KEY ([[event_id]], [[vehicle_id]])',
            'FOREIGN KEY ([[event_id]]) REFERENCES event ([[id]]) ON DELETE CASCADE ON UPDATE CASCADE',
            'FOREIGN KEY ([[vehicle_id]]) REFERENCES vehicle ([[id]]) ON DELETE CASCADE ON UPDATE CASCADE',
        ], $tableOptions);
        $this->createTable('event_working_time_role', [
            'working_time_id' => $this->integer(11)->notNull(),
            'role_id' => $this->integer(11)->notNull(),
            'create_time' => $this->datetime(),
            'update_time' => $this->datetime(),
            'PRIMARY KEY ([[working_time_id]], [[role_id]])',
            'FOREIGN KEY ([[working_time_id]]) REFERENCES event_user_working_time ([[id]]) ON DELETE CASCADE ON UPDATE CASCADE',
            'FOREIGN KEY ([[role_id]]) REFERENCES user_event_role ([[id]]) ON DELETE CASCADE ON UPDATE CASCADE',
        ], $tableOptions);
        $this->createTable('gear_attachment', [
            'id' => $this->primaryKey(),
            'filename' => $this->string(255),
            'extension' => $this->string(255),
            'type' => $this->integer(11)->defaultValue(1),
            'status' => $this->integer(11)->defaultValue(1),
            'content' => $this->text(),
            'create_time' => $this->datetime(),
            'update_time' => $this->datetime(),
            'info' => $this->text(),
            'gear_id' => $this->integer(11)->notNull(),
            'mime_type' => $this->string(255),
            'base_name' => $this->string(255),
            'FOREIGN KEY ([[gear_id]]) REFERENCES gear ([[id]]) ON DELETE CASCADE ON UPDATE CASCADE',
        ], $tableOptions);
        $this->createTable('gear_history', [
            'id' => $this->primaryKey(),
            'date_time' => $this->datetime(),
            'info' => $this->text(),
            'create_time' => $this->datetime(),
            'update_time' => $this->datetime(),
            'category' => $this->string(255),
            'gear_id' => $this->integer(11),
            'type' => $this->integer(11)->defaultValue(1),
            'status' => $this->integer(11)->defaultValue(1),
            'FOREIGN KEY ([[gear_id]]) REFERENCES gear ([[id]]) ON DELETE CASCADE ON UPDATE CASCADE',
        ], $tableOptions);
        $this->createTable('gear_service', [
            'id' => $this->primaryKey(),
            'gear_item_id' => $this->integer(11)->notNull(),
            'description' => $this->text(),
            'create_time' => $this->datetime(),
            'update_time' => $this->datetime(),
            'status_time' => $this->datetime(),
            'status' => $this->integer(11)->defaultValue(0),
            'type' => $this->integer(11)->defaultValue(1),
            'info' => $this->text(),
            'FOREIGN KEY ([[gear_item_id]]) REFERENCES gear_item ([[id]]) ON DELETE CASCADE ON UPDATE CASCADE',
        ], $tableOptions);
        $this->createTable('location_attachment', [
            'id' => $this->primaryKey(),
            'filename' => $this->string(255),
            'extension' => $this->string(255),
            'type' => $this->integer(11)->defaultValue(1),
            'status' => $this->integer(11)->defaultValue(1),
            'content' => $this->text(),
            'create_time' => $this->datetime(),
            'update_time' => $this->datetime(),
            'info' => $this->text(),
            'location_id' => $this->integer(11),
            'mime_type' => $this->string(255),
            'base_name' => $this->string(255),
            'FOREIGN KEY ([[location_id]]) REFERENCES location ([[id]]) ON DELETE CASCADE ON UPDATE CASCADE',
        ], $tableOptions);
        $this->createTable('meeting', [
            'id' => $this->primaryKey(),
            'name' => $this->string(255)->notNull(),
            'start_time' => $this->datetime()->notNull(),
            'end_time' => $this->datetime()->notNull(),
            'customer_id' => $this->integer(11),
            'contact_id' => $this->integer(11),
            'description' => $this->text(),
            'status' => $this->integer(11)->defaultValue(1),
            'type' => $this->integer(11)->defaultValue(1),
            'create_time' => $this->datetime(),
            'update_time' => $this->datetime(),
            'location' => $this->string(255),
            'reminder' => $this->integer(11),
            'remind_sms' => $this->integer(11)->defaultValue(0),
            'remind_email' => $this->integer(11)->defaultValue(0),
            'remind_push' => $this->integer(11)->defaultValue(0),
            'remind_all' => $this->integer(11),
            'remind_owner' => $this->integer(11),
            'remind_company' => $this->integer(11),
            'created_by' => $this->integer(11),
            'FOREIGN KEY ([[customer_id]]) REFERENCES customer ([[id]]) ON DELETE CASCADE ON UPDATE CASCADE',
            'FOREIGN KEY ([[contact_id]]) REFERENCES contact ([[id]]) ON DELETE CASCADE ON UPDATE CASCADE',
            'FOREIGN KEY ([[created_by]]) REFERENCES user ([[id]]) ON DELETE CASCADE ON UPDATE CASCADE',
        ], $tableOptions);
        $this->createTable('meeting_user', [
            'meeting_id' => $this->integer(11)->notNull(),
            'user_id' => $this->integer(11)->notNull(),
            'PRIMARY KEY ([[meeting_id]], [[user_id]])',
            'FOREIGN KEY ([[meeting_id]]) REFERENCES meeting ([[id]]) ON DELETE CASCADE ON UPDATE CASCADE',
            'FOREIGN KEY ([[user_id]]) REFERENCES user ([[id]]) ON DELETE CASCADE ON UPDATE CASCADE',
        ], $tableOptions);
        $this->createTable('migration', [
            'version' => $this->string(180)->notNull(),
            'apply_time' => $this->integer(11),
            'PRIMARY KEY ([[version]])',
        ], $tableOptions);
        $this->createTable('notification', [
            'id' => $this->primaryKey(),
            'name' => $this->string(255)->notNull(),
            'label' => $this->string(255),
            'title' => $this->string(255),
            'content' => $this->text(),
            'hint' => $this->string(255),
            'info' => $this->text(),
            'mail' => $this->integer(11)->defaultValue(0),
            'sms' => $this->integer(11)->defaultValue(0),
            'push' => $this->integer(11)->defaultValue(0),
            'type' => $this->integer(11)->defaultValue(1),
            'status' => $this->integer(11)->defaultValue(1),
            'create_time' => $this->datetime(),
            'update_time' => $this->datetime(),
        ], $tableOptions);
        $this->createTable('offer', [
            'id' => $this->primaryKey(),
            'event_id' => $this->integer(11),
            'status' => $this->integer(11)->notNull()->defaultValue(0),
            'customer_id' => $this->integer(11),
            'contact_id' => $this->integer(11),
            'name' => $this->string(255)->notNull(),
            'location_id' => $this->integer(11),
            'term_from' => $this->string(255),
            'term_to' => $this->string(255),
            'page' => $this->string(255),
            'manager_id' => $this->integer(11),
            'offer_date' => $this->string(255)->notNull(),
            'comment' => $this->text(),
            'event_start' => $this->datetime(),
            'event_end' => $this->datetime(),
            'packing_start' => $this->datetime(),
            'packing_end' => $this->datetime(),
            'montage_start' => $this->datetime(),
            'montage_end' => $this->datetime(),
            'readiness_start' => $this->datetime(),
            'readiness_end' => $this->datetime(),
            'practice_start' => $this->datetime(),
            'practice_end' => $this->datetime(),
            'disassembly_start' => $this->datetime(),
            'disassembly_end' => $this->datetime(),
            'create_time' => $this->datetime()->notNull(),
            'update_time' => $this->datetime()->notNull(),
            'FOREIGN KEY ([[customer_id]]) REFERENCES customer ([[id]]) ON DELETE CASCADE ON UPDATE CASCADE',
            'FOREIGN KEY ([[location_id]]) REFERENCES location ([[id]]) ON DELETE CASCADE ON UPDATE CASCADE',
            'FOREIGN KEY ([[manager_id]]) REFERENCES user ([[id]]) ON DELETE CASCADE ON UPDATE CASCADE',
            'FOREIGN KEY ([[contact_id]]) REFERENCES contact ([[id]]) ON DELETE CASCADE ON UPDATE CASCADE',
            'FOREIGN KEY ([[event_id]]) REFERENCES event ([[id]]) ON DELETE CASCADE ON UPDATE CASCADE',
        ], $tableOptions);
        $this->createTable('offer_custom_items', [
            'id' => $this->primaryKey(),
            'offer_id' => $this->integer(11)->notNull(),
            'quantity' => $this->integer(11)->notNull(),
            'name' => $this->string(255)->notNull(),
            'price' => $this->decimal(11,2)->notNull(),
            'diff_count' => $this->integer(11)->notNull(),
            'discount' => $this->integer(11)->notNull(),
            'department_id' => $this->integer(11),
            'FOREIGN KEY ([[offer_id]]) REFERENCES offer ([[id]]) ON DELETE CASCADE ON UPDATE CASCADE',
            'FOREIGN KEY ([[department_id]]) REFERENCES department ([[id]]) ON DELETE CASCADE ON UPDATE CASCADE',
        ], $tableOptions);
        $this->createTable('offer_gear', [
            'offer_id' => $this->integer(11)->notNull(),
            'gear_id' => $this->integer(11)->notNull(),
            'quantity' => $this->integer(11)->defaultValue(1),
            'discount' => $this->integer(11)->defaultValue(0),
            'duration' => $this->integer(11)->defaultValue(1),
            'first_day_percent' => $this->integer(11)->defaultValue(100),
            'PRIMARY KEY ([[offer_id]], [[gear_id]])',
            'FOREIGN KEY ([[offer_id]]) REFERENCES offer ([[id]]) ON DELETE CASCADE ON UPDATE CASCADE',
            'FOREIGN KEY ([[gear_id]]) REFERENCES gear ([[id]]) ON DELETE CASCADE ON UPDATE CASCADE',
        ], $tableOptions);
        $this->createTable('offer_gear_item', [
            'offer_id' => $this->integer(11)->notNull(),
            'gear_item_id' => $this->integer(11)->notNull(),
            'quantity' => $this->integer(11)->notNull()->defaultValue(0),
            'start_time' => $this->datetime(),
            'end_time' => $this->datetime(),
            'type' => $this->integer(11)->notNull()->defaultValue(1),
            'create_time' => $this->datetime(),
            'update_time' => $this->datetime(),
            'PRIMARY KEY ([[offer_id]], [[gear_item_id]])',
            'FOREIGN KEY ([[offer_id]]) REFERENCES offer ([[id]]) ON DELETE CASCADE ON UPDATE CASCADE',
            'FOREIGN KEY ([[gear_item_id]]) REFERENCES gear_item ([[id]]) ON DELETE CASCADE ON UPDATE CASCADE',
        ], $tableOptions);
        $this->createTable('offer_gear_setting', [
            'gear_category_id' => $this->integer(11),
            'offer_id' => $this->integer(11)->notNull(),
            'duration' => $this->integer(11)->defaultValue(1),
            'next_day_percent' => $this->integer(11)->defaultValue(50),
            'type' => $this->integer(11)->defaultValue(0),
            'FOREIGN KEY ([[offer_id]]) REFERENCES offer ([[id]]) ON DELETE CASCADE ON UPDATE CASCADE',
            'FOREIGN KEY ([[gear_category_id]]) REFERENCES gear_category ([[id]]) ON DELETE CASCADE ON UPDATE CASCADE',
        ], $tableOptions);
        $this->createTable('offer_outer_gear', [
            'offer_id' => $this->integer(11)->notNull(),
            'outer_gear_id' => $this->integer(11)->notNull(),
            'quantity' => $this->integer(11),
            'discount' => $this->integer(11),
            'PRIMARY KEY ([[offer_id]], [[outer_gear_id]])',
            'FOREIGN KEY ([[offer_id]]) REFERENCES offer ([[id]]) ON DELETE CASCADE ON UPDATE CASCADE',
            'FOREIGN KEY ([[outer_gear_id]]) REFERENCES outer_gear ([[id]]) ON DELETE CASCADE ON UPDATE CASCADE',
        ], $tableOptions);
        $this->createTable('offer_role', [
            'offer_id' => $this->integer(11)->notNull(),
            'role_id' => $this->integer(11)->notNull(),
            'duration' => $this->integer(11)->defaultValue(1),
            'price' => $this->decimal(11,2),
            'quantity' => $this->integer(11)->defaultValue(1),
            'create_time' => $this->datetime(),
            'update_time' => $this->datetime(),
            'status' => $this->integer(11)->defaultValue(1),
            'type' => $this->integer(11)->defaultValue(1),
            'PRIMARY KEY ([[offer_id]], [[role_id]])',
            'FOREIGN KEY ([[offer_id]]) REFERENCES offer ([[id]]) ON DELETE CASCADE ON UPDATE CASCADE',
            'FOREIGN KEY ([[role_id]]) REFERENCES user_event_role ([[id]]) ON DELETE CASCADE ON UPDATE CASCADE',
        ], $tableOptions);
        $this->createTable('offer_setting', [
            'id' => $this->primaryKey(),
            'offer_id' => $this->integer(11)->notNull(),
            'category_id' => $this->integer(11),
            'first_day_percent' => $this->integer(11),
            'duration' => $this->integer(11),
            'discount' => $this->integer(11),
            'create_time' => $this->datetime(),
            'update_time' => $this->datetime(),
            'type' => $this->integer(11)->defaultValue(1),
            'FOREIGN KEY ([[offer_id]]) REFERENCES offer ([[id]]) ON DELETE CASCADE ON UPDATE CASCADE',
            'FOREIGN KEY ([[category_id]]) REFERENCES gear_category ([[id]]) ON DELETE CASCADE ON UPDATE CASCADE',
        ], $tableOptions);
        $this->createTable('skill', [
            'id' => $this->primaryKey(),
            'name' => $this->string(255)->notNull(),
            'description' => $this->text(),
            'type' => $this->integer(11)->defaultValue(1),
            'status' => $this->integer(11)->defaultValue(1),
        ], $tableOptions);
        $this->createTable('offer_user_skills', [
            'offer_id' => $this->integer(11)->notNull(),
            'user_id' => $this->integer(11)->notNull(),
            'skill_id' => $this->integer(11)->notNull(),
            'time_from' => $this->datetime()->notNull(),
            'time_to' => $this->datetime()->notNull(),
            'PRIMARY KEY ([[offer_id]], [[user_id]], [[skill_id]])',
            'FOREIGN KEY ([[offer_id]]) REFERENCES offer ([[id]]) ON DELETE CASCADE ON UPDATE CASCADE',
            'FOREIGN KEY ([[user_id]]) REFERENCES user ([[id]]) ON DELETE CASCADE ON UPDATE CASCADE',
            'FOREIGN KEY ([[skill_id]]) REFERENCES skill ([[id]]) ON DELETE CASCADE ON UPDATE CASCADE',
        ], $tableOptions);
        $this->createTable('offer_vehicle', [
            'offer_id' => $this->integer(11)->notNull(),
            'vehicle_id' => $this->integer(11)->notNull(),
            'quantity' => $this->integer(11)->notNull(),
            'price' => $this->decimal(11,2)->defaultValue('0.00'),
            'price_type' => $this->integer(11)->defaultValue(1),
            'distance' => $this->integer(11)->defaultValue(0),
            'PRIMARY KEY ([[offer_id]], [[vehicle_id]])',
            'FOREIGN KEY ([[offer_id]]) REFERENCES offer ([[id]]) ON DELETE CASCADE ON UPDATE CASCADE',
            'FOREIGN KEY ([[vehicle_id]]) REFERENCES vehicle ([[id]]) ON DELETE CASCADE ON UPDATE CASCADE',
        ], $tableOptions);
        $this->createTable('outer_gear_item', [
            'id' => $this->primaryKey(),
            'name' => $this->string(255)->notNull(),
            'photo' => $this->string(255),
            'weight' => $this->decimal(11,2),
            'width' => $this->decimal(11,2),
            'height' => $this->decimal(11,2),
            'depth' => $this->decimal(11,2),
            'weight_case' => $this->decimal(11,2),
            'height_case' => $this->decimal(11,2),
            'depth_case' => $this->decimal(11,2),
            'width_case' => $this->decimal(11,2),
            'volume' => $this->decimal(11,2),
            'warehouse' => $this->string(255),
            'location' => $this->string(255),
            'code' => $this->string(255),
            'serial' => $this->string(255),
            'lamp_hours' => $this->decimal(11,2),
            'description' => $this->text(),
            'info' => $this->text(),
            'purchase_price' => $this->decimal(11,2),
            'refund_amount' => $this->decimal(11,2),
            'test_date' => $this->datetime(),
            'tester' => $this->string(255),
            'test_status' => $this->string(255),
            'service' => $this->text(),
            'outer_gear_id' => $this->integer(11)->notNull(),
            'status' => $this->integer(11)->defaultValue(1),
            'type' => $this->integer(11)->defaultValue(1),
            'create_time' => $this->datetime(),
            'update_time' => $this->datetime(),
            'group_id' => $this->integer(11),
            'number' => $this->integer(11),
            'FOREIGN KEY ([[outer_gear_id]]) REFERENCES outer_gear ([[id]]) ON DELETE CASCADE ON UPDATE CASCADE',
            'FOREIGN KEY ([[group_id]]) REFERENCES gear_group ([[id]]) ON DELETE CASCADE ON UPDATE CASCADE',
        ], $tableOptions);
        $this->createTable('personal', [
            'id' => $this->primaryKey(),
            'name' => $this->string(255)->notNull(),
            'location' => $this->string(255),
            'start_time' => $this->datetime(),
            'end_time' => $this->datetime(),
            'repeat' => $this->integer(11)->defaultValue(0),
            'repeat_since' => $this->date(),
            'reminder' => $this->integer(11)->defaultValue(0),
            'description' => $this->text(),
            'user_id' => $this->integer(11),
            'create_time' => $this->datetime(),
            'update_time' => $this->datetime(),
            'status' => $this->integer(11)->defaultValue(1),
            'typ' => $this->integer(11)->defaultValue(1),
            'parent_id' => $this->integer(11),
            'remind_sms' => $this->integer(11)->defaultValue(0),
            'remind_email' => $this->integer(11)->defaultValue(0),
            'remind_push' => $this->integer(11)->defaultValue(0),
            'FOREIGN KEY ([[user_id]]) REFERENCES user ([[id]]) ON DELETE CASCADE ON UPDATE CASCADE',
        ], $tableOptions);
        $this->createTable('rent', [
            'id' => $this->primaryKey(),
            'name' => $this->string(255)->notNull(),
            'start_time' => $this->datetime()->notNull(),
            'end_time' => $this->datetime(),
            'deliver_time' => $this->datetime(),
            'return_time' => $this->datetime(),
            'info' => $this->text(),
            'customer_id' => $this->integer(11),
            'contact_id' => $this->integer(11),
            'status' => $this->integer(11)->defaultValue(0),
            'type' => $this->integer(11)->defaultValue(1),
            'reminder' => $this->integer(11),
            'description' => $this->text(),
            'create_time' => $this->datetime(),
            'update_time' => $this->datetime(),
            'private_note' => $this->text(),
            'invoice_status' => $this->integer(11),
            'invoice_number' => $this->string(255),
            'payment_status' => $this->integer(11),
            'created_by' => $this->integer(11),
            'code' => $this->string(255),
            'FOREIGN KEY ([[created_by]]) REFERENCES user ([[id]]) ON DELETE CASCADE ON UPDATE CASCADE',
            'FOREIGN KEY ([[customer_id]]) REFERENCES customer ([[id]]) ON DELETE CASCADE ON UPDATE CASCADE',
            'FOREIGN KEY ([[contact_id]]) REFERENCES contact ([[id]]) ON DELETE CASCADE ON UPDATE CASCADE',
        ], $tableOptions);
        $this->createTable('rent_gear_item', [
            'rent_id' => $this->integer(11)->notNull(),
            'gear_item_id' => $this->integer(11)->notNull(),
            'quantity' => $this->integer(11)->defaultValue(0),
            'start_time' => $this->datetime(),
            'end_time' => $this->datetime(),
            'type' => $this->integer(11)->defaultValue(1),
            'create_time' => $this->datetime(),
            'update_time' => $this->datetime(),
            'PRIMARY KEY ([[rent_id]], [[gear_item_id]])',
            'FOREIGN KEY ([[rent_id]]) REFERENCES rent ([[id]]) ON DELETE CASCADE ON UPDATE CASCADE',
            'FOREIGN KEY ([[gear_item_id]]) REFERENCES gear_item ([[id]]) ON DELETE CASCADE ON UPDATE CASCADE',
        ], $tableOptions);
        $this->createTable('setting_attachment', [
            'id' => $this->primaryKey(),
            'filename' => $this->string(255),
            'extension' => $this->string(255),
            'type' => $this->integer(11)->defaultValue(1),
            'status' => $this->integer(11)->defaultValue(1),
            'content' => $this->text(),
            'create_time' => $this->datetime(),
            'update_time' => $this->datetime(),
            'info' => $this->text(),
            'mime_type' => $this->string(255),
            'base_name' => $this->string(255),
        ], $tableOptions);
        $this->createTable('settings', [
            'id' => $this->primaryKey(),
            'type' => $this->string(255)->notNull(),
            'section' => $this->string(255)->notNull(),
            'key' => $this->string(255)->notNull(),
            'value' => $this->text(),
            'active' => $this->smallInteger(1),
            'created' => $this->datetime(),
            'modified' => $this->datetime(),
        ], $tableOptions);
        $this->createTable('settlement_user', [
            'id' => $this->primaryKey(),
            'user_id' => $this->integer(11)->notNull(),
            'event_id' => $this->integer(11)->notNull(),
            'year' => $this->integer(11)->notNull(),
            'month' => $this->integer(11)->notNull(),
            'department_data' => $this->text(),
            'role_data' => $this->text(),
            'addon_data' => $this->text(),
            'allowance_data' => $this->text(),
            'working_hours_data' => $this->text(),
            'sum' => $this->decimal(11,2),
            'status' => $this->smallInteger(6)->defaultValue(0),
            'create_time' => $this->datetime(),
            'update_time' => $this->datetime(),
            'FOREIGN KEY ([[user_id]]) REFERENCES user ([[id]]) ON DELETE CASCADE ON UPDATE CASCADE',
            'FOREIGN KEY ([[event_id]]) REFERENCES event ([[id]]) ON DELETE CASCADE ON UPDATE CASCADE',
        ], $tableOptions);
        $this->createTable('task', [
            'id' => $this->primaryKey(),
            'title' => $this->string(255)->notNull(),
            'content' => $this->text(),
            'start_time' => $this->datetime(),
            'end_time' => $this->datetime(),
            'remind_email' => $this->integer(11)->defaultValue(0),
            'remind_sms' => $this->integer(11)->defaultValue(0),
            'remind_push' => $this->integer(11)->defaultValue(0),
            'priority' => $this->integer(11)->defaultValue(10),
            'create_time' => $this->datetime(),
            'update_time' => $this->datetime(),
            'creator_id' => $this->integer(11),
            'user_id' => $this->integer(11)->notNull(),
            'type' => $this->integer(11)->defaultValue(1),
            'status' => $this->integer(11)->defaultValue(0),
            'FOREIGN KEY ([[creator_id]]) REFERENCES user ([[id]]) ON DELETE CASCADE ON UPDATE CASCADE',
        ], $tableOptions);
        $this->createTable('user_addon_rate', [
            'user_id' => $this->integer(11)->notNull(),
            'rate_id' => $this->integer(11)->notNull(),
            'role_id' => $this->integer(11)->notNull(),
            'PRIMARY KEY ([[user_id]], [[rate_id]], [[role_id]])',
            'FOREIGN KEY ([[user_id]]) REFERENCES user ([[id]]) ON DELETE CASCADE ON UPDATE CASCADE',
            'FOREIGN KEY ([[rate_id]]) REFERENCES addon_rate ([[id]]) ON DELETE CASCADE ON UPDATE CASCADE',
            'FOREIGN KEY ([[role_id]]) REFERENCES user_event_role ([[id]]) ON DELETE CASCADE ON UPDATE CASCADE',
        ], $tableOptions);
        $this->createTable('user_department', [
            'user_id' => $this->integer(11)->notNull(),
            'department_id' => $this->integer(11)->notNull(),
            'PRIMARY KEY ([[user_id]], [[department_id]])',
            'FOREIGN KEY ([[user_id]]) REFERENCES user ([[id]]) ON DELETE CASCADE ON UPDATE CASCADE',
            'FOREIGN KEY ([[department_id]]) REFERENCES department ([[id]]) ON DELETE CASCADE ON UPDATE CASCADE',
        ], $tableOptions);
        $this->createTable('user_notification', [
            'id' => $this->primaryKey(),
            'title' => $this->string(255),
            'content' => $this->text(),
            'sms' => $this->integer(11)->defaultValue(0),
            'mail' => $this->integer(11)->defaultValue(0),
            'push' => $this->integer(11),
            'user_id' => $this->integer(11)->notNull(),
            'create_time' => $this->datetime(),
            'update_time' => $this->datetime(),
            'target_class' => $this->string(255),
            'target_id' => $this->integer(11),
            'info' => $this->text(),
            'FOREIGN KEY ([[user_id]]) REFERENCES user ([[id]]) ON DELETE CASCADE ON UPDATE CASCADE',
        ], $tableOptions);
        $this->createTable('user_skill', [
            'user_id' => $this->integer(11)->notNull(),
            'skill_id' => $this->integer(11)->notNull(),
            'PRIMARY KEY ([[user_id]], [[skill_id]])',
            'FOREIGN KEY ([[user_id]]) REFERENCES user ([[id]]) ON DELETE CASCADE ON UPDATE CASCADE',
            'FOREIGN KEY ([[skill_id]]) REFERENCES skill ([[id]]) ON DELETE CASCADE ON UPDATE CASCADE',
        ], $tableOptions);
        $this->createTable('vacation', [
            'id' => $this->primaryKey(),
            'user_id' => $this->integer(11)->notNull(),
            'start_date' => $this->date()->notNull(),
            'end_date' => $this->date(),
            'day_number' => $this->integer(11),
            'status' => $this->integer(11)->defaultValue(0),
            'type' => $this->integer(11)->defaultValue(1),
            'create_time' => $this->datetime(),
            'update_time' => $this->datetime(),
            'FOREIGN KEY ([[user_id]]) REFERENCES user ([[id]]) ON DELETE CASCADE ON UPDATE CASCADE',
        ], $tableOptions);
        $this->createTable('vehicle_attachment', [
            'id' => $this->primaryKey(),
            'filename' => $this->string(255),
            'extension' => $this->string(255),
            'type' => $this->integer(11)->defaultValue(1),
            'status' => $this->integer(11)->defaultValue(1),
            'content' => $this->text(),
            'create_time' => $this->datetime(),
            'update_time' => $this->datetime(),
            'info' => $this->text(),
            'vehicle_id' => $this->integer(11)->notNull(),
            'mime_type' => $this->string(255),
            'base_name' => $this->string(255),
            'FOREIGN KEY ([[vehicle_id]]) REFERENCES vehicle ([[id]]) ON DELETE CASCADE ON UPDATE CASCADE',
        ], $tableOptions);
        $this->createTable('vehicle_user_remind', [
            'vehicle_id' => $this->integer(11)->notNull(),
            'user_id' => $this->integer(11)->notNull(),
            'PRIMARY KEY ([[vehicle_id]], [[user_id]])',
            'FOREIGN KEY ([[vehicle_id]]) REFERENCES vehicle ([[id]]) ON DELETE CASCADE ON UPDATE CASCADE',
            'FOREIGN KEY ([[user_id]]) REFERENCES user ([[id]]) ON DELETE CASCADE ON UPDATE CASCADE',
        ], $tableOptions);

    }

    public function down()
    {
        $this->execute('SET foreign_key_checks = 0');
        $this->dropTable('vehicle_user_remind');
        $this->dropTable('vehicle_attachment');
        $this->dropTable('vacation');
        $this->dropTable('user_skill');
        $this->dropTable('user_notification');
        $this->dropTable('user_department');
        $this->dropTable('user_addon_rate');
        $this->dropTable('task');
        $this->dropTable('settlement_user');
        $this->dropTable('settings');
        $this->dropTable('setting_attachment');
        $this->dropTable('rent_gear_item');
        $this->dropTable('rent');
        $this->dropTable('personal');
        $this->dropTable('outer_gear_item');
        $this->dropTable('offer_vehicle');
        $this->dropTable('offer_user_skills');
        $this->dropTable('skill');
        $this->dropTable('offer_setting');
        $this->dropTable('offer_role');
        $this->dropTable('offer_outer_gear');
        $this->dropTable('offer_gear_setting');
        $this->dropTable('offer_gear_item');
        $this->dropTable('offer_gear');
        $this->dropTable('offer_custom_items');
        $this->dropTable('offer');
        $this->dropTable('notification');
        $this->dropTable('migration');
        $this->dropTable('meeting_user');
        $this->dropTable('meeting');
        $this->dropTable('location_attachment');
        $this->dropTable('gear_service');
        $this->dropTable('gear_history');
        $this->dropTable('gear_attachment');
        $this->dropTable('event_working_time_role');
        $this->dropTable('event_vehicle');
        $this->dropTable('vehicle');
        $this->dropTable('event_user_working_time');
        $this->dropTable('event_user_role');
        $this->dropTable('user_event_role');
        $this->dropTable('event_user_allowance');
        $this->dropTable('event_user_addon');
        $this->dropTable('event_user');
        $this->dropTable('event_outer_gear');
        $this->dropTable('outer_gear');
        $this->dropTable('event_message');
        $this->dropTable('event_invoice');
        $this->dropTable('event_gear_item');
        $this->dropTable('gear_item');
        $this->dropTable('gear');
        $this->dropTable('gear_group');
        $this->dropTable('event_expense');
        $this->dropTable('event_department');
        $this->dropTable('department');
        $this->dropTable('customer_discount_customer');
        $this->dropTable('customer_discount_category');
        $this->dropTable('gear_category');
        $this->dropTable('customer_discount');
        $this->dropTable('auth_item_child');
        $this->dropTable('auth_assignment');
        $this->dropTable('auth_item');
        $this->dropTable('auth_rule');
        $this->dropTable('attachment');
        $this->dropTable('event');
        $this->dropTable('user');
        $this->dropTable('contact');
        $this->dropTable('location');
        $this->dropTable('customer');
        $this->dropTable('addon_rate');
        $this->execute('SET foreign_key_checks = 1');
    }
}
