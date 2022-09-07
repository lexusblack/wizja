<?php

namespace common\models\base;

use Yii;
use yii\behaviors\TimestampBehavior;
use yii\helpers\ArrayHelper;

/**
 * This is the base model class for table "project".
 *
 * @property integer $id
 * @property string $name
 * @property integer $tasks_schema_id
 * @property string $code
 * @property integer $customer_id
 * @property integer $contact_id
 * @property string $start_time
 * @property string $end_time
 * @property string $create_time
 * @property string $update_time
 * @property integer $creator_id
 * @property string $description
 *
 * @property \common\models\AgencyOffer[] $agencyOffers
 * @property \common\models\Event[] $events
 * @property \common\models\Note[] $notes
 * @property \common\models\Offer[] $offers
 * @property \common\models\TasksSchema $tasksSchema
 * @property \common\models\Customer $customer
 * @property \common\models\Contact $contact
 * @property \common\models\User $creator
 * @property \common\models\ProjectDepartment[] $projectDepartments
 * @property \common\models\Department[] $departments
 * @property \common\models\ProjectUser[] $projectUsers
 * @property \common\models\Task[] $tasks
 * @property \common\models\TaskCategory[] $taskCategories
 */
class Project extends \common\components\BaseActiveRecord
{

    public $departmentIds;
    public $managerIds = [];
    /**
    * This function helps \mootensai\relation\RelationTrait runs faster
    * @return array relation names of this model
    */
    public function relationNames()
    {
        return [
            'agencyOffers',
            'events',
            'notes',
            'offers',
            'tasksSchema',
            'customer',
            'contact',
            'creator',
            'projectDepartments',
            'departments',
            'projectUsers',
            'tasks',
            'taskCategories',
            'managers'
        ];
    }


    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['tasks_schema_id', 'customer_id', 'contact_id', 'creator_id'], 'integer'],
            [['start_time', 'end_time', 'create_time', 'update_time'], 'safe'],
            [['description'], 'string'],
            [['departmentIds'], 'integer'],
            [['name'], 'string', 'max' => 255],
            [['code'], 'string', 'max' => 45]
        ];
    }

    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'project';
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'name' => Yii::t('app', 'Nazwa'),
            'tasks_schema_id' => Yii::t('app', 'Schemat zadań'),
            'code' => Yii::t('app', 'Identyfikator'),
            'customer_id' => Yii::t('app', 'Klient'),
            'contact_id' => Yii::t('app', 'Kontakt'),
            'start_time' => Yii::t('app', 'Początek'),
            'end_time' => Yii::t('app', 'Koniec'),
            'create_time' => 'Create Time',
            'update_time' => 'Update Time',
            'creator_id' => 'Creator ID',
            'description' => Yii::t('app', 'Opis'),
            'departmentIds'=> Yii::t('app', 'Działy'),
            'managerIds' => Yii::t('app', 'Project Manager')
        ];
    }
    
    /**
     * @return \yii\db\ActiveQuery
     */
    public function getAgencyOffers()
    {
        return $this->hasMany(\common\models\AgencyOffer::className(), ['project_id' => 'id']);
    }
        
    /**
     * @return \yii\db\ActiveQuery
     */
    public function getEvents()
    {
        return $this->hasMany(\common\models\Event::className(), ['project_id' => 'id']);
    }
        
    /**
     * @return \yii\db\ActiveQuery
     */
    public function getNotes()
    {
        return $this->hasMany(\common\models\Note::className(), ['project_id' => 'id'])->andWhere(['type'=>1])->orderBy(['datetime'=>SORT_DESC]);
    }
        
    /**
     * @return \yii\db\ActiveQuery
     */
    public function getOffers()
    {
        return $this->hasMany(\common\models\Offer::className(), ['project_id' => 'id']);
    }
       
    /**
     * @return \yii\db\ActiveQuery
     */
    /*
    public function getOffersAll()
    {
        $ids = ArrayHelper::map(Event::find()->where(['project_id'=>$this->id])->asArray()->all(), 'id', 'id');
        return Offer::find()->where(['IN', 'id', $ids])->orWhere(['project_id'=>$this->id]);
    }
*/
    /**
     * @return \yii\db\ActiveQuery
     */
    public function getTasksSchema()
    {
        return $this->hasOne(\common\models\TasksSchema::className(), ['id' => 'tasks_schema_id']);
    }
        
    /**
     * @return \yii\db\ActiveQuery
     */
    public function getCustomer()
    {
        return $this->hasOne(\common\models\Customer::className(), ['id' => 'customer_id']);
    }
        
    /**
     * @return \yii\db\ActiveQuery
     */
    public function getContact()
    {
        return $this->hasOne(\common\models\Contact::className(), ['id' => 'contact_id']);
    }
        
    /**
     * @return \yii\db\ActiveQuery
     */
    public function getCreator()
    {
        return $this->hasOne(\common\models\User::className(), ['id' => 'creator_id']);
    }
        
    /**
     * @return \yii\db\ActiveQuery
     */
    public function getProjectDepartments()
    {
        return $this->hasMany(\common\models\ProjectDepartment::className(), ['project_id' => 'id']);
    }
        
    /**
     * @return \yii\db\ActiveQuery
     */
    public function getDepartments()
    {
        return $this->hasMany(\common\models\Department::className(), ['id' => 'department_id'])->viaTable('project_department', ['project_id' => 'id']);
    }
        
    /**
     * @return \yii\db\ActiveQuery
     */
    public function getProjectUsers()
    {
        return $this->hasMany(\common\models\ProjectUser::className(), ['project_id' => 'id']);
    }
        
    /**
     * @return \yii\db\ActiveQuery
     */
    public function getTasks()
    {
        return $this->hasMany(\common\models\Task::className(), ['project_id' => 'id']);
    }
        
    /**
     * @return \yii\db\ActiveQuery
     */
    public function getTaskCategories()
    {
        return $this->hasMany(\common\models\TaskCategory::className(), ['project_id' => 'id'])->orderBy(['order'=>SORT_ASC]);
    }
    


    public function getFiles()
    {
        $ids = ArrayHelper::map(Note::find()->where(['project_id'=>$this->id])->asArray()->all(), 'id', 'id');
        return NoteAttachment::find()->where(['note_id'=>$ids])->all();
    }
    /**
     * @inheritdoc
     * @return array mixed
     */




}
