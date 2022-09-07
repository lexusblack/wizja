<?php

namespace common\models\base;

use Yii;

/**
 * This is the base model class for table "note".
 *
 * @property integer $id
 * @property integer $user_id
 * @property string $text
 * @property string $datetime
 * @property integer $event_id
 * @property integer $rent_id
 * @property integer $project_id
 * @property integer $customer_id
 * @property integer $note_id
 * @property integer $type
 *
 * @property \common\models\Customer $customer
 * @property \common\models\Event $event
 * @property \common\models\Rent $rent
 * @property \common\models\Project $project
 * @property \common\models\User $user
 * @property \common\models\Note $note
 * @property \common\models\Note[] $notes
 * @property \common\models\NoteAttachment[] $noteAttachments
 */
class Note extends \yii\db\ActiveRecord
{
    use \mootensai\relation\RelationTrait;


    /**
    * This function helps \mootensai\relation\RelationTrait runs faster
    * @return array relation names of this model
    */
    public function relationNames()
    {
        return [
            'customer',
            'event',
            'rent',
            'project',
            'user',
            'note',
            'notes',
            'noteAttachments'
        ];
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['user_id', 'event_id', 'rent_id', 'project_id', 'customer_id', 'note_id', 'type'], 'integer'],
            [['text'], 'string'],
            [['datetime'], 'safe']
        ];
    }

    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'note';
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'user_id' => 'User ID',
            'text' => 'Text',
            'datetime' => 'Datetime',
            'event_id' => 'Event ID',
            'rent_id' => 'Rent ID',
            'project_id' => 'Project ID',
            'customer_id' => 'Customer ID',
            'note_id' => 'Note ID',
            'type' => 'Type',
        ];
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
    public function getEvent()
    {
        return $this->hasOne(\common\models\Event::className(), ['id' => 'event_id']);
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
    public function getNote()
    {
        return $this->hasOne(\common\models\Note::className(), ['id' => 'note_id']);
    }
        
    /**
     * @return \yii\db\ActiveQuery
     */
    public function getNotes()
    {
        return $this->hasMany(\common\models\Note::className(), ['note_id' => 'id']);
    }
        
    /**
     * @return \yii\db\ActiveQuery
     */
    public function getNoteAttachments()
    {
        return $this->hasMany(\common\models\NoteAttachment::className(), ['note_id' => 'id']);
    }
    }
