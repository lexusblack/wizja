<?php

namespace common\models\base;

use Yii;

/**
 * This is the base model class for table "firm".
 *
 * @property integer $id
 * @property string $name
 * @property string $address
 * @property string $zip
 * @property string $city
 * @property string $logo
 * @property string $nip
 * @property string $phone
 * @property string $email
 * @property string $bank_name
 * @property string $bank_number
 * @property string $warehouse_adress
 * @property string $warehouse_zip
 * @property string $warehouse_city
 * @property integer $active
 */
class Firm extends \yii\db\ActiveRecord
{
    use \mootensai\relation\RelationTrait;


    /**
    * This function helps \mootensai\relation\RelationTrait runs faster
    * @return array relation names of this model
    */
    public function relationNames()
    {
        return [
            ''
        ];
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['name'], 'required'],
            [['active'], 'integer'],
            [['name', 'address', 'city', 'logo', 'email', 'warehouse_adress', 'warehouse_city'], 'string', 'max' => 255],
            [['zip', 'nip', 'phone', 'bank_name', 'bank_number', 'warehouse_zip'], 'string', 'max' => 45]
        ];
    }

    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'firm';
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'name' => 'Nazwa',
            'address' => 'Adres',
            'zip' => 'od pocztowy',
            'city' => 'Miejscowość',
            'logo' => 'Logo',
            'nip' => 'NIP',
            'phone' => 'Telefon',
            'email' => 'E-mail',
            'bank_name' => 'Nazwa banku',
            'bank_number' => 'Numer konta',
            'warehouse_adress' => 'Adres magazynu',
            'warehouse_zip' => 'Kod pocztowy',
            'warehouse_city' => 'Miejscowość',
            'active' => 'Active',
        ];
    }


    /**
     * @inheritdoc
     * @return \app\models\FirmQuery the active query used by this AR class.
     */
    public static function find()
    {
        return new \app\models\FirmQuery(get_called_class());
    }
}
