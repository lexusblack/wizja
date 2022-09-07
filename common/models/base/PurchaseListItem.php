<?php

namespace common\models\base;

use Yii;

/**
 * This is the base model class for table "purchase_list_item".
 *
 * @property integer $id
 * @property integer $purchase_list_id
 * @property string $name
 * @property integer $quantity
 * @property string $company_name
 * @property string $company_address
 * @property integer $outer_gear_id
 * @property integer $event_id
 * @property integer $status
 * @property integer $position
 * @property string $description
 *
 * @property \common\models\Event $event
 * @property \common\models\OuterGear $outerGear
 * @property \common\models\PurchaseList $purchaseList
 */
class PurchaseListItem extends \yii\db\ActiveRecord
{
    use \mootensai\relation\RelationTrait;


    /**
    * This function helps \mootensai\relation\RelationTrait runs faster
    * @return array relation names of this model
    */
    public function relationNames()
    {
        return [
            'event',
            'outerGear',
            'purchaseList'
        ];
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['purchase_list_id', 'quantity', 'outer_gear_id', 'event_id', 'status', 'position', 'event_expense_id'], 'integer'],
            [['name', 'company_name', 'company_address', 'description'], 'string', 'max' => 255],
            [['price'], 'number']
        ];
    }

    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'purchase_list_item';
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'purchase_list_id' => Yii::t('app', 'Lista'),
            'name' => Yii::t('app', 'Nazwa'),
            'quantity' => Yii::t('app', 'Ilość'),
            'company_name' => Yii::t('app', 'Firma'),
            'company_address' => Yii::t('app', 'Adres'),
            'outer_gear_id' => Yii::t('app', 'Sprzęt'),
            'event_id' => Yii::t('app', 'Wydarzenie'),
            'status' => Yii::t('app', 'Status'),
            'position' => Yii::t('app', 'Pozycja'),
            'description' => Yii::t('app', 'Uwagi'),
            'price' => Yii::t('app', 'Cena'),
        ];
    }
    
    /**
     * @return \yii\db\ActiveQuery
     */
    public function getEvent()
    {
        return $this->hasOne(\common\models\Event::className(), ['id' => 'event_id']);
    }
        
    /**
     * @return \yii\db\ActiveQuery
     */
    public function getOuterGear()
    {
        return $this->hasOne(\common\models\OuterGear::className(), ['id' => 'outer_gear_id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getEventOuterGear()
    {
        return $this->hasOne(\common\models\EventOuterGear::className(), ['outer_gear_id' => 'outer_gear_id', 'event_id'=>'event_id']);
    }
        
    /**
     * @return \yii\db\ActiveQuery
     */
    public function getPurchaseList()
    {
        return $this->hasOne(\common\models\PurchaseList::className(), ['id' => 'purchase_list_id']);
    }


    }
