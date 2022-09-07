<?php

namespace common\models\base;

use Yii;

/**
 * This is the base model class for table "gear_service_statut_permission".
 *
 * @property integer $id
 * @property integer $gear_service_statut_id
 * @property string $permission_group_id
 */
class GearServiceStatutPermission extends \yii\db\ActiveRecord
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
            [['gear_service_statut_id'], 'integer'],
            [['permission_group_id'], 'string', 'max' => 255]
        ];
    }

    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'gear_service_statut_permission';
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'gear_service_statut_id' => 'Gear Service Statut ID',
            'permission_group_id' => 'Permission Group ID',
        ];
    }
}
