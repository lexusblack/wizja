<?php

namespace common\models\base;

use Yii;

/**
 * This is the base model class for table "purchase".
 *
 * @property integer $id
 * @property integer $user_id
 * @property string $section
 * @property string $number
 * @property string $description
 * @property string $price
 * @property integer $purchase_type_id
 *
 * @property \common\models\PurchaseType $purchaseType
 * @property \common\models\User $user
 * @property \common\models\PurchaseEvent[] $purchaseEvents
 */
class Purchase extends \yii\db\ActiveRecord
{


    /**
    * This function helps \mootensai\relation\RelationTrait runs faster
    * @return array relation names of this model
    */
    public function relationNames()
    {
        return [
            'purchaseType',
            'user',
            'purchaseEvents'
        ];
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['user_id', 'purchase_type_id'], 'integer'],
            [['description'], 'string'],
            [['price'], 'number'],
            [['section'], 'string'],
            [['code'], 'string']
        ];
    }

    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'purchase';
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'user_id' => Yii::t('app', 'Użytkownik'),
            'section' => Yii::t('app', 'Sekcja'),
            'code' => Yii::t('app', 'Numer'),
            'description' => Yii::t('app', 'Opis'),
            'price' => Yii::t('app', 'Kwota'),
            'purchase_type_id' => Yii::t('app', 'Typ'),
            'sections'=>Yii::t('app', 'Sekcje'),
            'eventIds'=>Yii::t('app', 'Eventy'),
        ];
    }
    
    /**
     * @return \yii\db\ActiveQuery
     */
    public function getPurchaseType()
    {
        return $this->hasOne(\common\models\PurchaseType::className(), ['id' => 'purchase_type_id']);
    }
        
    /**
     * @return \yii\db\ActiveQuery
     */
    public function getUser()
    {
        return $this->hasOne(\common\models\User::className(), ['id' => 'user_id']);
    }
        
    /**
     * @return \yii\db\ActiveQuery
     */
    public function getPurchaseEvents()
    {
        return $this->hasMany(\common\models\PurchaseEvent::className(), ['purchase_id' => 'id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getEvents()
    {
        return $this->hasMany(\common\models\Event::className(), ['id' => 'event_id'])->viaTable('purchase_event', ['purchase_id' => 'id']);
    }
    }
