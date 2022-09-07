<?php

namespace common\models\base;

use Yii;

/**
 * This is the base model class for table "event_report_provisions".
 *
 * @property integer $id
 * @property integer $event_report_id
 * @property integer $provision_group_id
 * @property string $value
 *
 * @property \common\models\EventReport $eventReport
 * @property \common\models\ProvisionGroup $provisionGroup
 */
class EventReportProvisions extends \yii\db\ActiveRecord
{
    use \mootensai\relation\RelationTrait;


    /**
    * This function helps \mootensai\relation\RelationTrait runs faster
    * @return array relation names of this model
    */
    public function relationNames()
    {
        return [
            'eventReport',
            'provisionGroup'
        ];
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['event_id', 'provision_group_id'], 'integer'],
            [['value'], 'number']
        ];
    }

    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'event_report_provisions';
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'event_id' => 'Event Report ID',
            'provision_group_id' => 'Provision Group ID',
            'value' => 'Value',
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
    public function getProvisionGroup()
    {
        return $this->hasOne(\common\models\ProvisionGroup::className(), ['id' => 'provision_group_id']);
    }
    }
