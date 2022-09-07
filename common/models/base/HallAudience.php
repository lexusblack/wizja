<?php

namespace common\models\base;

use Yii;

/**
 * This is the base model class for table "hall_audience".
 *
 * @property integer $id
 * @property integer $hall_audience_type_id
 * @property integer $hall_group_id
 * @property integer $audience
 *
 * @property \common\models\HallAudienceType $hallAudienceType
 * @property \common\models\HallGroup $hallGroup
 */
class HallAudience extends \yii\db\ActiveRecord
{
    use \mootensai\relation\RelationTrait;


    /**
    * This function helps \mootensai\relation\RelationTrait runs faster
    * @return array relation names of this model
    */
    public function relationNames()
    {
        return [
            'hallAudienceType',
            'hallGroup'
        ];
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['hall_audience_type_id', 'hall_group_id', 'audience'], 'integer']
        ];
    }

    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'hall_audience';
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'hall_audience_type_id' => 'Hall Audience Type ID',
            'hall_group_id' => 'Hall Group ID',
            'audience' => 'Audience',
        ];
    }
    
    /**
     * @return \yii\db\ActiveQuery
     */
    public function getHallAudienceType()
    {
        return $this->hasOne(\common\models\HallAudienceType::className(), ['id' => 'hall_audience_type_id']);
    }
        
    /**
     * @return \yii\db\ActiveQuery
     */
    public function getHallGroup()
    {
        return $this->hasOne(\common\models\HallGroup::className(), ['id' => 'hall_group_id']);
    }
    }
