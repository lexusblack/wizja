<?php

namespace common\models\base;

use Yii;

/**
 * This is the base model class for table "packlist_outer_gear".
 *
 * @property integer $id
 * @property integer $packlist_id
 * @property integer $event_outer_gear
 * @property integer $quantity
 * @property string $info
 *
 * @property \common\models\EventOuterGearModel $eventOuterGear
 * @property \common\models\Packlist $packlist
 */
class PacklistOuterGear extends \yii\db\ActiveRecord
{
    use \mootensai\relation\RelationTrait;


    /**
    * This function helps \mootensai\relation\RelationTrait runs faster
    * @return array relation names of this model
    */
    public function relationNames()
    {
        return [
            'eventOuterGear',
            'packlist'
        ];
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['packlist_id', 'event_outer_gear', 'quantity'], 'integer'],
            [['info'], 'string', 'max' => 255]
        ];
    }

    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'packlist_outer_gear';
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'packlist_id' => 'Packlist ID',
            'event_outer_gear' => 'Event Outer Gear',
            'quantity' => 'Quantity',
            'info' => 'Info',
        ];
    }
    


    public function getEventOuterGear()
    {
        return $this->hasOne(\common\models\EventOuterGear::className(), ['id' => 'event_outer_gear']);
    }
        
    /**
     * @return \yii\db\ActiveQuery
     */
    public function getPacklist()
    {
        return $this->hasOne(\common\models\Packlist::className(), ['id' => 'packlist_id']);
    }
    }
