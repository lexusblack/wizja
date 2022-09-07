<?php

namespace common\models\base;

use Yii;

/**
 * This is the base model class for table "packlist_gear".
 *
 * @property integer $id
 * @property integer $event_gear_id
 * @property integer $quantity
 * @property integer $packlist_id
 * @property string $info
 *
 * @property \common\models\Gear $eventGear
 * @property \common\models\Packlist $packlist
 */
class PacklistGear extends \yii\db\ActiveRecord
{
    use \mootensai\relation\RelationTrait;


    /**
    * This function helps \mootensai\relation\RelationTrait runs faster
    * @return array relation names of this model
    */
    public function relationNames()
    {
        return [
            'eventGear',
            'packlist',
            'gear'
        ];
    }


    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['event_gear_id', 'quantity', 'packlist_id'], 'integer'],
            [['info'], 'string', 'max' => 255]
        ];
    }

    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'packlist_gear';
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'event_gear_id' => 'Event Gear ID',
            'quantity' => 'Quantity',
            'packlist_id' => 'Packlist ID',
            'info' => 'Info',
        ];
    }
    
    /**
     * @return \yii\db\ActiveQuery
     */
    public function getEventGear()
    {
        return $this->hasOne(\common\models\EventGear::className(), ['id' => 'event_gear_id']);
    }

    public function getGear()
    {
        return $this->hasOne(\common\models\Gear::className(), ['id' => 'gear_id']);
    }
        
    /**
     * @return \yii\db\ActiveQuery
     */
    public function getPacklist()
    {
        return $this->hasOne(\common\models\Packlist::className(), ['id' => 'packlist_id']);
    }
    }
