<?php

namespace common\models\base;

use Yii;

/**
 * This is the base model class for table "company".
 *
 * @property integer $id
 * @property string $name
 * @property string $link
 * @property string $code
 * @property string $mail
 * @property string $phone
 * @property string $start_date
 */
class Company extends \yii\db\ActiveRecord
{

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['name', 'link', 'code'], 'required'],
            [['start_date'], 'safe'],
            [['name', 'link', 'code', 'mail', 'phone', 'language', 'logo'], 'string', 'max' => 255],
            [['gears', 'events', 'rents', 'users', 'customer_id', 'type', 'columns', 'active', 'subscribed', 'superusers'], 'integer']
        ];
    }

    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'company';
    }

    /**
     * @return \yii\db\Connection the database connection used by this AR class.
     */
    public static function getDb()
    {
        return Yii::$app->get('db2');
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'name' => 'Nazwa',
            'link' => 'Link',
            'code' => 'Identyfikator',
            'mail' => 'Mail',
            'phone' => 'Telefon',
            'start_date' => 'Start',
            'version' => 'Wersja',
            'gears'=>'Sprzęt',
            'users'=>'Użytkownicy',
            'superusers'=>'SuperUserzy',
            'rents'=>'Wypożyczenia',
            'events'=>'Eventy',
            'customer_id'=>'Kontahent',
            'active'=>'Aktywny',
            'language'=>'Język',
            'subscribed' => 'Umowa',
            'logo' => 'Logo'
        ];
    }
}
