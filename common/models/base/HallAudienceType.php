<?php

namespace common\models\base;

use Yii;

/**
 * This is the base model class for table "hall_audience_type".
 *
 * @property integer $id
 * @property string $name
 * @property string $photo
 *
 * @property \common\models\HallAudience[] $hallAudiences
 */
class HallAudienceType extends \yii\db\ActiveRecord
{
    use \mootensai\relation\RelationTrait;


    /**
    * This function helps \mootensai\relation\RelationTrait runs faster
    * @return array relation names of this model
    */
    public function relationNames()
    {
        return [
            'hallAudiences'
        ];
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['name', 'photo'], 'string', 'max' => 255]
        ];
    }

    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'hall_audience_type';
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'name' => Yii::t('app', 'Nazwa'),
            'photo' => Yii::t('app', 'Zdjęcie'),
        ];
    }
    
    /**
     * @return \yii\db\ActiveQuery
     */
    public function getHallAudiences()
    {
        return $this->hasMany(\common\models\HallAudience::className(), ['hall_audience_type_id' => 'id']);
    }
    }
