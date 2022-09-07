<?php

namespace common\models\base;

use Yii;

/**
 * This is the base model class for table "project_department".
 *
 * @property integer $project_id
 * @property integer $department_id
 *
 * @property \common\models\Project $project
 * @property \common\models\Department $department
 */
class ProjectDepartment extends \yii\db\ActiveRecord
{
    use \mootensai\relation\RelationTrait;


    /**
    * This function helps \mootensai\relation\RelationTrait runs faster
    * @return array relation names of this model
    */
    public function relationNames()
    {
        return [
            'project',
            'department'
        ];
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['project_id', 'department_id'], 'required'],
            [['project_id', 'department_id'], 'integer'],
            [['project_id', 'department_id'], 'unique', 'targetAttribute' => ['project_id', 'department_id'], 'message' => 'The combination of Project ID and Department ID has already been taken.']
        ];
    }

    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'project_department';
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'project_id' => 'Project ID',
            'department_id' => 'Department ID',
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
    public function getDepartment()
    {
        return $this->hasOne(\common\models\Department::className(), ['id' => 'department_id']);
    }
    }
