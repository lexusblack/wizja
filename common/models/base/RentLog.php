<?php

namespace common\models\base;

use Yii;
use yii\behaviors\TimestampBehavior;

/**
 * This is the base model class for table "rent_log".
 *
 * @property integer $id
 * @property string $content
 * @property integer $user_id
 * @property string $create_time
 * @property string $update_time
 * @property integer $rent_id
 *
 * @property \common\models\Rent $rent
 * @property \common\models\User $user
 */
class RentLog extends \yii\db\ActiveRecord
{
    use \mootensai\relation\RelationTrait;


    /**
    * This function helps \mootensai\relation\RelationTrait runs faster
    * @return array relation names of this model
    */
    public function relationNames()
    {
        return [
            'rent',
            'user'
        ];
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['content'], 'string'],
            [['user_id', 'rent_id'], 'integer'],
            [['create_time', 'update_time'], 'safe']
        ];
    }

    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'rent_log';
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'content' => Yii::t('app', 'Treść'),
            'user_id' => Yii::t('app', 'Użytkownik'),
            'create_time' => Yii::t('app', 'Data'),
            'update_time' => 'Update Time',
            'rent_id' => 'Rent ID',
        ];
    }
    
    /**
     * @return \yii\db\ActiveQuery
     */
    public function getRent()
    {
        return $this->hasOne(\common\models\Rent::className(), ['id' => 'rent_id']);
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
                'value' => date('Y-m-d H:i:s'),
            ],
        ];
    }
}
