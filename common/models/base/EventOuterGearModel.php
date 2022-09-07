<?php

namespace common\models\base;

use Yii;
use yii\behaviors\TimestampBehavior;
use yii\behaviors\BlameableBehavior;

/**
 * This is the base model class for table "event_outer_gear_model".
 *
 * @property integer $id
 * @property integer $event_id
 * @property integer $outer_gear_model_id
 * @property integer $quantity
 * @property string $start_time
 * @property string $end_time
 * @property string $create_time
 * @property string $update_time
 *
 * @property \common\models\OuterGearModel $outerGearModel
 * @property \common\models\Event $event
 */
class EventOuterGearModel extends \yii\db\ActiveRecord
{
    use \mootensai\relation\RelationTrait;

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['event_id', 'outer_gear_model_id', 'quantity', 'prod'], 'integer'],
            [['start_time', 'end_time', 'create_time', 'update_time'], 'safe']
        ];
    }
    
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'event_outer_gear_model';
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => Yii::t('app','ID'),
            'event_id' => Yii::t('app','ID wydarzenia'),
            'outer_gear_model_id' => Yii::t('app','ID sprzętu zewnętrznego'),
            'quantity' => Yii::t('app','Liczba'),
            'start_time' => Yii::t('app','Początek pracy'),
            'end_time' => Yii::t('app','Koniec pracy'),
            'create_time' => Yii::t('app','Stworzono'),
            'update_time' => Yii::t('app','Zaktualizowano'),
        ];
    }
    
    /**
     * @return \yii\db\ActiveQuery
     */
    public function getOuterGearModel()
    {
        return $this->hasOne(\common\models\OuterGearModel::className(), ['id' => 'outer_gear_model_id']);
    }
        
    /**
     * @return \yii\db\ActiveQuery
     */
    public function getEvent()
    {
        return $this->hasOne(\common\models\Event::className(), ['id' => 'event_id']);
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
                'value' => new \yii\db\Expression('NOW()'),
            ]
        ];
    }
}
