<?php

namespace common\models\base;

use Yii;
use yii\behaviors\TimestampBehavior;
use yii\behaviors\BlameableBehavior;

/**
 * This is the base model class for table "location_note".
 *
 * @property integer $id
 * @property string $text
 * @property integer $created_by
 * @property string $create_time
 * @property string $update_time
 * @property integer $location_id
 *
 * @property \common\models\Location $location
 */
class LocationNote extends \yii\db\ActiveRecord
{
    use \mootensai\relation\RelationTrait;

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['text'], 'string'],
            [['created_by', 'location_id'], 'integer'],
            [['create_time', 'update_time'], 'safe']
        ];
    }
    
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'location_note';
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' =>  Yii::t('app', 'ID'),
            'text' =>  Yii::t('app', 'Tekst'),
            'create_time' =>  Yii::t('app', 'Stworzono'),
            'update_time' =>  Yii::t('app', 'Zaktualizowano'),
            'location_id' =>  Yii::t('app', 'Miejsce'),
        ];
    }
    
    /**
     * @return \yii\db\ActiveQuery
     */
    public function getLocation()
    {
        return $this->hasOne(\common\models\Location::className(), ['id' => 'location_id']);
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
                'value' => new \yii\db\Expression('NOW()'),
            ],
            'blameable' => [
                'class' => BlameableBehavior::className(),
                'createdByAttribute' => 'created_by',
                'updatedByAttribute' => false,
            ],
        ];
    }
}
