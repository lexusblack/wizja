<?php

namespace common\models\base;

use Yii;
use yii\behaviors\TimestampBehavior;

/**
 * This is the base model class for table "agency_offer_service_category".
 *
 * @property integer $id
 * @property integer $agency_offer_id
 * @property string $name
 * @property integer $position
 * @property string $color
 * @property integer $provizion
 * @property string $create_time
 * @property string $update_time
 *
 * @property \common\models\AgencyOfferService[] $agencyOfferServices
 * @property \common\models\AgencyOffer $agencyOffer
 */
class AgencyOfferServiceCategory extends \yii\db\ActiveRecord
{
    use \mootensai\relation\RelationTrait;


    /**
    * This function helps \mootensai\relation\RelationTrait runs faster
    * @return array relation names of this model
    */
    public function relationNames()
    {
        return [
            'agencyOfferServices',
            'agencyOffer'
        ];
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['agency_offer_id', 'position', 'provizion'], 'integer'],
            [['create_time', 'update_time'], 'safe'],
            [['name', 'color'], 'string', 'max' => 255]
        ];
    }

    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'agency_offer_service_category';
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'agency_offer_id' => 'Agency Offer ID',
            'name' => 'Nazwa',
            'position' => 'Position',
            'color' => 'Color',
            'provizion' => 'Liczona w prowizji',
            'create_time' => 'Create Time',
            'update_time' => 'Update Time',
        ];
    }
    
    /**
     * @return \yii\db\ActiveQuery
     */
    public function getAgencyOfferServices()
    {
        return $this->hasMany(\common\models\AgencyOfferService::className(), ['category_id' => 'id'])->orderBy(['position'=>SORT_ASC]);
    }
        
    /**
     * @return \yii\db\ActiveQuery
     */
    public function getAgencyOffer()
    {
        return $this->hasOne(\common\models\AgencyOffer::className(), ['id' => 'agency_offer_id']);
    }
    
    /**
     * @inheritdoc
     * @return array mixed
     */
    public function behaviors()
    {
        return [
            'timestamp' => [
                'class' => TimestampBehavior::className(),
                'createdAtAttribute' => 'create_time',
                'updatedAtAttribute' => 'update_time',
                'value' => date('Y-m-d H:i:s'),
            ],
        ];
    }
}
