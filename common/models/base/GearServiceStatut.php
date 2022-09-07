<?php

namespace common\models\base;

use Yii;

/**
 * This is the base model class for table "gear_service_statut".
 *
 * @property integer $id
 * @property string $name
 * @property string $color
 * @property integer $type
 * @property integer $in_menu
 * @property integer $active
 */
class GearServiceStatut extends \yii\db\ActiveRecord
{

    
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
            [['name'], 'required'],
            [['type', 'in_menu', 'active', 'order'], 'integer'],
            [['name'], 'string', 'max' => 255],
            [['color'], 'string', 'max' => 45]
        ];
    }

    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'gear_service_statut';
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'name' => Yii::t('app', 'Nazwa'),
            'color' => Yii::t('app', 'Kolor'),
            'type' => Yii::t('app', 'Wpływ na sprzęt'),
            'in_menu' => Yii::t('app', 'Widoczny w menu'),
            'active' => 'Active',
        ];
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getPermissions()
    {
        return $this->hasMany(\common\models\AuthItem::className(), ['name' => 'permission_group_id'])->viaTable('gear_service_statut_permission', ['gear_service_statut_id' => 'id']);
    }
}
