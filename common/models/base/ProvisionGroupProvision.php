<?php

namespace common\models\base;

use Yii;

/**
 * This is the base model class for table "provision_group_provision".
 *
 * @property integer $id
 * @property integer $provision_group_id
 * @property string $section
 * @property string $value
 * @property integer $type
 *
 * @property \common\models\ProvisionGroup $provisionGroup
 */
class ProvisionGroupProvision extends \yii\db\ActiveRecord
{
    use \mootensai\relation\RelationTrait;


    /**
    * This function helps \mootensai\relation\RelationTrait runs faster
    * @return array relation names of this model
    */
    public function relationNames()
    {
        return [
            'provisionGroup'
        ];
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['provision_group_id', 'type'], 'integer'],
            [['value'], 'number'],
            [['section'], 'string', 'max' => 255]
        ];
    }

    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'provision_group_provision';
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'provision_group_id' => 'Provision Group ID',
            'section' => Yii::t('app', 'Sekcja'),
            'value' => Yii::t('app', 'Prowizja'),
            'type' => Yii::t('app', 'Typ'),
        ];
    }
    
    /**
     * @return \yii\db\ActiveQuery
     */
    public function getProvisionGroup()
    {
        return $this->hasOne(\common\models\ProvisionGroup::className(), ['id' => 'provision_group_id']);
    }
    }
