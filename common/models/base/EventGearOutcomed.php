<?php

namespace common\models\base;

use Yii;

/**
 * This is the base model class for table "event_gear_outcomed".
 *
 * @property integer $packlist_id
 * @property integer $gear_id
 * @property integer $quantity
 * @property integer $event_id
 */
class EventGearOutcomed extends \yii\db\ActiveRecord
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
            [['packlist_id'], 'required'],
            [['packlist_id', 'gear_id', 'quantity', 'event_id'], 'integer']
        ];
    }

    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'event_gear_outcomed';
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'packlist_id' => 'Packlist ID',
            'gear_id' => 'Gear ID',
            'quantity' => 'Quantity',
            'event_id' => 'Event ID',
        ];
    }

        /**
     * @return \yii\db\ActiveQuery
     */
    public function getEvent()
    {
        return $this->hasOne(\common\models\Event::className(), ['id' => 'event_id']);
    }

    public function getPacklist()
    {
        return $this->hasOne(\common\models\Packlist::className(), ['id' => 'packlist_id']);
    }

        /**
     * @return \yii\db\ActiveQuery
     */
    public function getGear()
    {
        return $this->hasOne(\common\models\Gear::className(), ['id' => 'gear_id']);
    }
}
