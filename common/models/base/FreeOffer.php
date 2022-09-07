<?php

namespace common\models\base;

use Yii;

/**
 * This is the base model class for table "offer".
 *
 * @property integer $id
 * @property string $name
 * @property string $start_time
 * @property string $end_time
 * @property string $company
 * @property string $desctiption
 * @property integer $city_id
 * @property string $work_info
 * @property string $transport_info
 * @property string $money_info
 * @property integer $deal_type
 * @property string $skills
 * @property string $devices
 * @property string $own_device
 * @property integer $user_id
 * @property string $user_mail
 * @property string $company_name
 */
class FreeOffer extends \yii\db\ActiveRecord
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
            
            [['id', 'city_id',  'user_id'], 'integer'],
            [['start_time', 'end_time'], 'safe'],
            [['description', 'work_info', 'transport_info', 'money_info', 'skills', 'devices', 'own_device', 'deal_type'], 'string'],
            [['name', 'user_mail', 'company_name'], 'string', 'max' => 255],
            [['company'], 'string', 'max' => 45]
            ];
    }

    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'offer';
    }

    /**
     * @return \yii\db\Connection the database connection used by this AR class.
     */
    public static function getDb()
    {
        return Yii::$app->get('dbfree');
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'name' => Yii::t('app', 'Tytuł'),
            'start_time' => Yii::t('app', 'Data początkowa'),
            'end_time' => Yii::t('app', 'Data końcowa'),
            'company' => 'Company',
            'description' => Yii::t('app', 'Opis'),
            'city_id' => Yii::t('app', 'Najbliższe miasto'),
            'work_info' => Yii::t('app', 'Dokładny opis pracy'),
            'transport_info' => Yii::t('app', 'Informacje dotyczące transportu'),
            'money_info' => Yii::t('app', 'Informacje dotyczące wynagrodzenia'),
            'deal_type' => Yii::t('app', 'Rodzaj umowy'),
            'skills' => Yii::t('app', 'Potrzebne umiejętności'),
            'devices' => Yii::t('app', 'Potrzebne umiejętności obsługi urządzeń'),
            'own_device' => Yii::t('app', 'Preferowany własny sprzęt'),
            'user_id' => 'User ID',
            'user_mail' => 'User Mail',
            'company_name' => 'Company Name',
            'address' => Yii::t('app', 'Dokładny adres'),
            'skillIds'=>Yii::t('app', 'Wymagane umiejętności')
        ];
    }

        public function getCity()
    {
        return $this->hasOne(\common\models\City::className(), ['id' => 'city_id']);
    }
}
