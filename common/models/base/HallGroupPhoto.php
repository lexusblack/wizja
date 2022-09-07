<?php

namespace common\models\base;

use Yii;

/**
 * This is the base model class for table "hall_group_photo".
 *
 * @property integer $id
 * @property string $filename
 * @property string $extension
 * @property integer $status
 * @property string $create_time
 * @property string $update_time
 * @property integer $hall_group_id
 * @property string $mime_type
 * @property string $base_name
 *
 * @property \common\models\HallGroup $hallGroup
 */
class HallGroupPhoto extends \yii\db\ActiveRecord
{


    /**
    * This function helps \mootensai\relation\RelationTrait runs faster
    * @return array relation names of this model
    */
    public function relationNames()
    {
        return [
            'hallGroup'
        ];
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['status', 'hall_group_id'], 'integer'],
            [['create_time', 'update_time'], 'safe'],
            [['filename', 'base_name'], 'string', 'max' => 255],
            [['extension', 'mime_type'], 'string', 'max' => 45]
        ];
    }

    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'hall_group_photo';
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'filename' => 'Filename',
            'extension' => 'Extension',
            'status' => 'Status',
            'create_time' => 'Create Time',
            'update_time' => 'Update Time',
            'hall_group_id' => Yii::t('app', 'Powierzchnia'),
            'mime_type' => 'Mime Type',
            'base_name' => 'Base Name',
        ];
    }
    
    /**
     * @return \yii\db\ActiveQuery
     */
    public function getHallGroup()
    {
        return $this->hasOne(\common\models\HallGroup::className(), ['id' => 'hall_group_id']);
    }
    }
