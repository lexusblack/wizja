<?php

namespace common\models\base;

use Yii;

/**
 * This is the base model class for table "project_user_role".
 *
 * @property integer $project_user_id
 * @property integer $user_event_role_id
 */
class ProjectUserRole extends \yii\db\ActiveRecord
{
    use \mootensai\relation\RelationTrait;


    /**
    * This function helps \mootensai\relation\RelationTrait runs faster
    * @return array relation names of this model
    */
    public function relationNames()
    {
        return [
            ''
        ];
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['project_user_id', 'user_event_role_id'], 'required'],
            [['project_user_id', 'user_event_role_id'], 'integer']
        ];
    }

    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'project_user_role';
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'project_user_id' => 'Project User ID',
            'user_event_role_id' => 'User Event Role ID',
        ];
    }
}
