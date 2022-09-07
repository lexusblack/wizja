<?php

namespace common\models\base;

use Yii;
use yii\behaviors\TimestampBehavior;
use yii\behaviors\BlameableBehavior;

/**
 * This is the base model class for table "checklist".
 *
 * @property integer $id
 * @property string $name
 * @property integer $user_id
 * @property string $deadline
 * @property integer $done
 * @property integer $priority
 * @property string $create_time
 * @property string $update_time
 *
 * @property \common\models\User $user
 */
class Checklist extends \yii\db\ActiveRecord
{
    use \mootensai\relation\RelationTrait;

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['name'], 'string'],
            [['user_id', 'done', 'priority'], 'integer'],
            [['deadline', 'create_time', 'update_time'], 'safe']
        ];
    }
    
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'checklist';
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => Yii::t('app','ID'),
            'name' => Yii::t('app','Treść'),
            'user_id' => Yii::t('app','Użytkownik'),
            'deadline' => Yii::t('app','Deadline'),
            'done' => Yii::t('app','Wykonano'),
            'priority' => Yii::t('app','Priorytet'),
            'create_time' => Yii::t('app','Stworzono'),
            'update_time' => Yii::t('app','Zaktualizowano'),
        ];
    }
    
    /**
     * @return \yii\db\ActiveQuery
     */
    public function getUser()
    {
        return $this->hasOne(\common\models\User::className(), ['id' => 'user_id']);
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
            ]
        ];
    }
}
