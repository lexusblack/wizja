<?php

namespace common\models\base;

use Yii;

/**
 * This is the base model class for table "packlist_extra".
 *
 * @property integer $id
 * @property integer $packlist_id
 * @property integer $event_extra_id
 * @property integer $quantity
 * @property string $info
 *
 * @property \common\models\EventExtraItem $eventExtra
 * @property \common\models\Packlist $packlist
 */
class PacklistExtra extends \yii\db\ActiveRecord
{
    use \mootensai\relation\RelationTrait;


    /**
    * This function helps \mootensai\relation\RelationTrait runs faster
    * @return array relation names of this model
    */
    public function relationNames()
    {
        return [
            'eventExtra',
            'packlist'
        ];
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['packlist_id', 'event_extra_id', 'quantity'], 'integer'],
            [['info'], 'string', 'max' => 255]
        ];
    }

    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'packlist_extra';
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'packlist_id' => 'Packlist ID',
            'event_extra_id' => 'Event Extra ID',
            'quantity' => 'Quantity',
            'info' => 'Info',
        ];
    }
    
    /**
     * @return \yii\db\ActiveQuery
     */
    public function getEventExtraItem()
    {
        return $this->hasOne(\common\models\EventExtraItem::className(), ['id' => 'event_extra_id']);
    }
        
    /**
     * @return \yii\db\ActiveQuery
     */
    public function getPacklist()
    {
        return $this->hasOne(\common\models\Packlist::className(), ['id' => 'packlist_id']);
    }
    }
