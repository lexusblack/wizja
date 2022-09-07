<?php

namespace common\models\base;

use Yii;

/**
 * This is the base model class for table "event_provision_value".
 *
 * @property integer $id
 * @property integer $event_id
 * @property integer $provision_group_id
 * @property string $value
 * @property string $section
 */
class EventProvisionValue extends \yii\db\ActiveRecord
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
            [['event_id', 'provision_group_id'], 'integer'],
            [['value'], 'number'],
            [['section'], 'string', 'max' => 255]
        ];
    }

    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'event_provision_value';
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'event_id' => 'Event ID',
            'provision_group_id' => 'Provision Group ID',
            'value' => 'Value',
            'section' => 'Section',
        ];
    }
}
