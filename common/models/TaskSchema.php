<?php

namespace common\models;

use Yii;
use \common\models\base\TaskSchema as BaseTaskSchema;

/**
 * This is the model class for table "task_schema".
 */
class TaskSchema extends BaseTaskSchema
{
    /**
     * @inheritdoc
     */

    public $roleIds;
    public $userIds;
    public $teamIds;
    public $notificationRoleIds;
    public $notificationUserIds;

    public function rules()
    {
        return array_replace_recursive(parent::rules(),
	    [
            [['tasks_schema_cat_id', 'order'], 'integer'],
            [['description'], 'string'],
            [['name'], 'string', 'max' => 255],
            [['roleIds', 'userIds', 'notificationRoleIds', 'notificationUserIds', 'teamIds'], 'each', 'rule' => ['integer']],
        ]);
    }

    public function behaviors()
    {
        $behaviors = parent::behaviors();


        $behaviors['link'] = [
            'class' => \common\behaviors\LinkBehavior::className(),
            'attributes' => [
                'roleIds',
                'userIds',
                'notificationRoleIds',
                'notificationUserIds',
                'teamIds'
            ],
            'relations' => [
                'roles',
                'users',
                'notificationRoles',
                'notificationUsers',  
                'teams'             
            ],
            'modelClasses' => [
                'common\models\UserEventRole',
                'common\models\User',
                'common\models\UserEventRole',
                'common\models\User',
                'common\models\Team'
            ],
        ];
        return $behaviors;
    }

    public function attributeLabels()
    {
        $labels = [
            'userIds' => Yii::t('app', 'Użytkownicy przypisani do zadania'),
            'teamIds' => Yii::t('app', 'Zespoły przypisane do zadania'),
            'roleIds' => Yii::t('app', 'Role przypisane do zadania'),
            'notificationUserIds' => Yii::t('app', 'Potwierdzenie o wykonaniu do (użytkownicy)'),
            'notificationRoleIds' => Yii::t('app', 'Potwierdzenie o wykonaniu do (role)'),
        ];
        return array_merge(parent::attributeLabels(), $labels);
    }
	
}
