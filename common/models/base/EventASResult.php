<?php

namespace common\models\base;

use Yii;

/**
 * This is the base model class for table "event_additional_statut_result".
 *
 * @property integer $id
 * @property integer $event_id
 * @property integer $event_additional_statut_id
 * @property integer $event_additional_statut_name_id
 */
class EventASResult extends \yii\db\ActiveRecord
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
            [['event_id', 'event_additional_statut_id', 'event_additional_statut_name_id'], 'integer']
        ];
    }

    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'event_additional_statut_result';
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'event_id' => 'Event ID',
            'event_additional_statut_id' => 'Event Additional Statut ID',
            'event_additional_statut_name_id' => 'Event Additional Statut Name ID',
        ];
    }



        /**
     * @return \yii\db\ActiveQuery
     */
    public function getEventAdditionalStatutName()
    {
        return $this->hasOne(\common\models\EventAdditionalStatutName::className(), ['id' => 'event_additional_statut_name_id']);
    }

        /**
     * @return \yii\db\ActiveQuery
     */
    public function getEventAdditionalStatut()
    {
        return $this->hasOne(\common\models\EventAdditionalStatut::className(), ['id' => 'event_additional_statut_id']);
    }
}
