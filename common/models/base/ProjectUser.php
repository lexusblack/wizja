<?php

namespace common\models\base;

use Yii;

/**
 * This is the base model class for table "project_user".
 *
 * @property integer $id
 * @property integer $project_id
 * @property integer $user_id
 * @property integer $manager
 *
 * @property \common\models\Project $project
 * @property \common\models\User $user
 */
class ProjectUser extends \yii\db\ActiveRecord
{


    /**
    * This function helps \mootensai\relation\RelationTrait runs faster
    * @return array relation names of this model
    */
    public function relationNames()
    {
        return [
            'project',
            'user'
        ];
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['project_id', 'user_id', 'manager'], 'integer'],
             [['user_id'], 'required'],
        ];
    }

    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'project_user';
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'project_id' => 'Project ID',
            'user_id' => 'User ID',
            'manager' => 'Manager',
        ];
    }
    
    /**
     * @return \yii\db\ActiveQuery
     */
    public function getProject()
    {
        return $this->hasOne(\common\models\Project::className(), ['id' => 'project_id']);
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
    public function getRoles()
    {
        return $this->hasMany(\common\models\UserEventRole::className(), ['id' => 'user_event_role_id'])->viaTable('project_user_role', ['project_user_id' => 'id']);
    }

    }
