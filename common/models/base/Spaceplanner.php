<?php

namespace common\models\base;

use Yii;
use yii\behaviors\TimestampBehavior;
use yii\behaviors\BlameableBehavior;

/**
 * This is the base model class for table "spaceplanner".
 *
 * @property integer $id
 * @property string $name
 * @property integer $user_id
 * @property string $description
 * @property string $snapshot
 * @property string $create_time
 * @property string $update_time
 */
class Spaceplanner extends \yii\db\ActiveRecord
{
    use \mootensai\relation\RelationTrait;

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['user_id'], 'integer'],
            [['snapshot'], 'string'],
            [['create_time', 'update_time'], 'safe'],
            [['name', 'description'], 'string', 'max' => 255]
        ];
    }
    
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'spaceplanner';
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' =>  Yii::t('app', 'ID'),
            'name' =>  Yii::t('app', 'Nazwa'),
            'user_id' =>  Yii::t('app', 'ID użytkownika'),
            'description' =>  Yii::t('app', 'Opis'),
            'snapshot' =>  Yii::t('app', 'Migawka'),
            'create_time' =>  Yii::t('app', 'Stworzono'),
            'update_time' =>  Yii::t('app', 'Zaktualizowano'),
        ];
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
                'createdByAttribute' => 'user_id',
                'updatedByAttribute' => false,
            ],
        ];
    }
}
