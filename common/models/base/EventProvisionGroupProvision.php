<?php

namespace common\models\base;

use Yii;

/**
 * This is the base model class for table "event_provision_group_provision".
 *
 * @property integer $id
 * @property integer $event_provision_group_id
 * @property string $section
 * @property string $value
 * @property integer $type
 *
 * @property \common\models\EventProvisionGroup $eventProvisionGroup
 */
class EventProvisionGroupProvision extends \yii\db\ActiveRecord
{
    use \mootensai\relation\RelationTrait;


    /**
    * This function helps \mootensai\relation\RelationTrait runs faster
    * @return array relation names of this model
    */
    public function relationNames()
    {
        return [
            'eventProvisionGroup'
        ];
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['event_provision_group_id', 'type'], 'integer'],
            [['value'], 'number'],
            [['section'], 'string', 'max' => 255]
        ];
    }

    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'event_provision_group_provision';
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'event_provision_group_id' => 'Event Provision Group ID',
            'section' => 'Section',
            'value' => 'Value',
            'type' => 'Type',
        ];
    }
    
    /**
     * @return \yii\db\ActiveQuery
     */
    public function getEventProvisionGroup()
    {
        return $this->hasOne(\common\models\EventProvisionGroup::className(), ['id' => 'event_provision_group_id']);
    }
    }
