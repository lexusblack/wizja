<?php
// This class was automatically generated by a giiant build task
// You should not change it manually as it will be overwritten on next build

namespace common\models\base;

use Yii;

/**
 * This is the base-model class for table "event_gear_item".
 *
 * @property integer $event_id
 * @property integer $gear_item_id
 * @property integer $quantity
 * @property string $start_time
 * @property string $end_time
 * @property integer $type
 * @property string $create_time
 * @property string $update_time
 * @property integer $planned
 *
 * @property \common\models\Event $event
 * @property \common\models\GearItem $gearItem
 * @property string $aliasModel
 */
abstract class EventGearItem extends \common\components\BaseActiveRecord
{



    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'event_gear_item';
    }


    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['event_id', 'gear_item_id'], 'required'],
            [['event_id', 'gear_item_id', 'quantity', 'type', 'planned', 'packlist_id', 'gear_id'], 'integer'],
            [['start_time', 'end_time', 'create_time', 'update_time'], 'safe'],
            [['event_id', 'gear_item_id'], 'unique', 'targetAttribute' => ['event_id', 'gear_item_id'], 'message' => 'The combination of Event ID and Gear Item ID has already been taken.'],
            [['event_id'], 'exist', 'skipOnError' => true, 'targetClass' => Event::className(), 'targetAttribute' => ['event_id' => 'id']],
            [['gear_item_id'], 'exist', 'skipOnError' => true, 'targetClass' => GearItem::className(), 'targetAttribute' => ['gear_item_id' => 'id']]
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'event_id' => Yii::t('app', 'ID wydarzenia'),
            'gear_item_id' => Yii::t('app', 'ID egzemplarza'),
            'quantity' => Yii::t('app', 'Liczba'),
            'start_time' => Yii::t('app', 'Od'),
            'end_time' => Yii::t('app', 'Do'),
            'type' => Yii::t('app', 'Typ'),
            'create_time' => Yii::t('app', 'Stworzono'),
            'update_time' => Yii::t('app', 'Zaktualizowano'),
            'planned' => Yii::t('app', 'Planowany'),
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
    public function getGearItem()
    {
        return $this->hasOne(\common\models\GearItem::className(), ['id' => 'gear_item_id']);
    }




}
