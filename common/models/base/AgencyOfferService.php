<?php

namespace common\models\base;

use Yii;
use yii\behaviors\TimestampBehavior;

/**
 * This is the base model class for table "agency_offer_service".
 *
 * @property integer $id
 * @property integer $agency_offer_id
 * @property string $name
 * @property integer $position
 * @property string $create_time
 * @property string $update_time
 * @property integer $count
 * @property string $price
 * @property string $client_price
 * @property string $total_price
 * @property string $info
 * @property integer $category_id
 *
 * @property \common\models\AgencyOffer $agencyOffer
 * @property \common\models\AgencyOfferServiceCategory $category
 */
class AgencyOfferService extends \yii\db\ActiveRecord
{
    use \mootensai\relation\RelationTrait;


    /**
    * This function helps \mootensai\relation\RelationTrait runs faster
    * @return array relation names of this model
    */
    public function relationNames()
    {
        return [
            'agencyOffer',
            'category'
        ];
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['agency_offer_id', 'position', 'count', 'category_id'], 'integer'],
            [['create_time', 'update_time'], 'safe'],
            [['price', 'client_price', 'total_price'], 'number'],
            [['info'], 'string'],
            [['name'], 'string', 'max' => 255]
        ];
    }

    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'agency_offer_service';
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'agency_offer_id' => 'Agency Offer ID',
            'name' => 'Name',
            'position' => 'Position',
            'create_time' => 'Create Time',
            'update_time' => 'Update Time',
            'count' => 'Count',
            'price' => 'Price',
            'client_price' => 'Client Price',
            'total_price' => 'Total Price',
            'info' => 'Info',
            'category_id' => 'Category ID',
        ];
    }
    
    /**
     * @return \yii\db\ActiveQuery
     */
    public function getAgencyOffer()
    {
        return $this->hasOne(\common\models\AgencyOffer::className(), ['id' => 'agency_offer_id']);
    }
        
    /**
     * @return \yii\db\ActiveQuery
     */
    public function getCategory()
    {
        return $this->hasOne(\common\models\AgencyOfferServiceCategory::className(), ['id' => 'category_id']);
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
