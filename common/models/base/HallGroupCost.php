<?php

namespace common\models\base;

use Yii;

/**
 * This is the base model class for table "hall_group_cost".
 *
 * @property integer $id
 * @property integer $hall_group_id
 * @property string $cost
 * @property string $name
 * @property string $currency
 *
 * @property \common\models\HallGroup $hallGroup
 */
class HallGroupCost extends \yii\db\ActiveRecord
{
    use \mootensai\relation\RelationTrait;


    /**
    * This function helps \mootensai\relation\RelationTrait runs faster
    * @return array relation names of this model
    */
    public function relationNames()
    {
        return [
            'hallGroup'
        ];
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['hall_group_id'], 'integer'],
            [['cost'], 'number'],
            [['name'], 'string', 'max' => 255],
            [['currency'], 'string', 'max' => 45]
        ];
    }

    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'hall_group_cost';
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'hall_group_id' => 'Hall Group ID',
            'cost' => 'Cost',
            'name' => 'Name',
            'currency' => 'Currency',
        ];
    }
    
    /**
     * @return \yii\db\ActiveQuery
     */
    public function getHallGroup()
    {
        return $this->hasOne(\common\models\HallGroup::className(), ['id' => 'hall_group_id']);
    }
    }
