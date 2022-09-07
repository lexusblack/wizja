<?php

namespace common\models\base;

use Yii;
use yii\behaviors\TimestampBehavior;

/**
 * This is the base model class for table "month_cost".
 *
 * @property integer $id
 * @property string $name
 * @property integer $department_id
 * @property string $section
 * @property integer $creator_id
 * @property string $create_time
 * @property string $update_time
 * @property string $amount
 *
 * @property \common\models\Department $department
 * @property \common\models\User $creator
 */
class MonthCost extends \yii\db\ActiveRecord
{


    /**
    * This function helps \mootensai\relation\RelationTrait runs faster
    * @return array relation names of this model
    */
    public function relationNames()
    {
        return [
            'department',
            'creator'
        ];
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['department_id', 'creator_id', 'type', 'group_id'], 'integer'],
            [['create_time', 'update_time'], 'safe'],
            [['amount'], 'number'],
            [['name', 'section'], 'string', 'max' => 255]
        ];
    }

    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'month_cost';
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'name' => Yii::t('app', 'Nazwa'),
            'department_id' => Yii::t('app', 'Dział'),
            'section' => Yii::t('app', 'Sekcja'),
            'creator_id' => Yii::t('app', 'Dodał'),
            'create_time' => 'Create Time',
            'update_time' => 'Update Time',
            'amount' => Yii::t('app', 'Kwota'),
            'sections'=>Yii::t('app', 'Sekcje')
        ];
    }
    
    /**
     * @return \yii\db\ActiveQuery
     */
    public function getDepartment()
    {
        return $this->hasOne(\common\models\Department::className(), ['id' => 'department_id']);
    }
        
    /**
     * @return \yii\db\ActiveQuery
     */
    public function getCreator()
    {
        return $this->hasOne(\common\models\User::className(), ['id' => 'creator_id']);
    }
    
    /**
     * @inheritdoc
     * @return array mixed
     */
    public function behaviors()
    {
        return [
            'timestamp' => [
                'class' => TimestampBehavior::className(),
                'createdAtAttribute' => 'create_time',
                'updatedAtAttribute' => 'update_time',
                'value' => date('Y-m-d H:i:s'),
            ],
        ];
    }
}
