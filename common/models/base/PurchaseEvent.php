<?php

namespace common\models\base;

use Yii;

/**
 * This is the base model class for table "purchase_event".
 *
 * @property integer $id
 * @property integer $event_id
 * @property integer $purchase_id
 *
 * @property \common\models\Event $event
 * @property \common\models\Purchase $purchase
 */
class PurchaseEvent extends \yii\db\ActiveRecord
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
            'purchase'
        ];
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['event_id', 'purchase_id'], 'integer']
        ];
    }

    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'purchase_event';
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'event_id' => 'Event ID',
            'purchase_id' => 'Purchase ID',
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
    public function getPurchase()
    {
        return $this->hasOne(\common\models\Purchase::className(), ['id' => 'purchase_id']);
    }
    }
