<?php

namespace common\models\base;

use Yii;

/**
 * This is the base model class for table "gear_service_history".
 *
 * @property integer $id
 * @property integer $gear_service_id
 * @property integer $user_id
 * @property string $description
 * @property integer $statut_from
 * @property integer $statut_to
 * @property string $datetime
 */
class GearServiceHistory extends \yii\db\ActiveRecord
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
            [['gear_service_id', 'user_id', 'statut_from', 'statut_to'], 'integer'],
            [['datetime'], 'safe'],
            [['description'], 'string', 'max' => 255]
        ];
    }

    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'gear_service_history';
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'gear_service_id' => 'Gear Service ID',
            'user_id' => 'User ID',
            'description' => 'Description',
            'statut_from' => 'Statut From',
            'statut_to' => 'Statut To',
            'datetime' => 'Datetime',
        ];
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getUser()
    {
        return $this->hasOne(\common\models\User::className(), ['id' => 'user_id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getStatutTo()
    {
        return $this->hasOne(\common\models\GearServiceStatut::className(), ['id' => 'statut_to']);
    }
}
