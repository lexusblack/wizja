<?php

namespace common\models\base;

use Yii;

/**
 * This is the base model class for table "hall_group_statut".
 *
 * @property integer $id
 * @property string $name
 * @property string $color
 * @property integer $active
 * @property integer $position
 * @property integer $final
 *
 * @property \common\models\EventHallGroup[] $eventHallGroups
 */
class HallGroupStatut extends \yii\db\ActiveRecord
{
    use \mootensai\relation\RelationTrait;


    /**
    * This function helps \mootensai\relation\RelationTrait runs faster
    * @return array relation names of this model
    */
    public function relationNames()
    {
        return [
            'eventHallGroups'
        ];
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['active', 'position', 'final'], 'integer'],
            [['name'], 'string', 'max' => 255],
            [['color'], 'string', 'max' => 45]
        ];
    }

    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'hall_group_statut';
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'name' => 'Name',
            'color' => 'Color',
            'active' => 'Active',
            'position' => 'Position',
            'final' => 'Final',
        ];
    }
    
    /**
     * @return \yii\db\ActiveQuery
     */
    public function getEventHallGroups()
    {
        return $this->hasMany(\common\models\EventHallGroup::className(), ['statut_id' => 'id']);
    }
    }
