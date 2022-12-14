<?php
// This class was automatically generated by a giiant build task
// You should not change it manually as it will be overwritten on next build

namespace common\models\base;

use Yii;

/**
 * This is the base-model class for table "user_department".
 *
 * @property integer $user_id
 * @property integer $department_id
 *
 * @property \common\models\User $user
 * @property \common\models\Department $department
 * @property string $aliasModel
 */
abstract class UserDepartment extends \common\components\BaseActiveRecord
{



    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'user_department';
    }


    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['user_id', 'department_id'], 'required'],
            [['user_id', 'department_id'], 'integer'],
            [['user_id', 'department_id'], 'unique', 'targetAttribute' => ['user_id', 'department_id'], 'message' => 'The combination of User ID and Department ID has already been taken.'],
            [['user_id'], 'exist', 'skipOnError' => true, 'targetClass' => User::className(), 'targetAttribute' => ['user_id' => 'id']],
            [['department_id'], 'exist', 'skipOnError' => true, 'targetClass' => Department::className(), 'targetAttribute' => ['department_id' => 'id']]
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'user_id' => Yii::t('app', 'ID użytkownika'),
            'department_id' => Yii::t('app', 'ID działu'),
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
     * @return \yii\db\ActiveQuery
     */
    public function getDepartment()
    {
        return $this->hasOne(\common\models\Department::className(), ['id' => 'department_id']);
    }




}
