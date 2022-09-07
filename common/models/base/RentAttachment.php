<?php

namespace common\models\base;

use Yii;

/**
 * This is the base model class for table "rent_attachment".
 *
 * @property integer $id
 * @property string $filename
 * @property string $extension
 * @property integer $type
 * @property integer $status
 * @property string $content
 * @property string $create_time
 * @property string $update_time
 * @property string $info
 * @property integer $rent_id
 * @property string $mime_type
 * @property string $base_name
 * @property integer $public
 *
 * @property \common\models\Rent $rent
 */
class RentAttachment extends \yii\db\ActiveRecord
{
    use \mootensai\relation\RelationTrait;


    /**
    * This function helps \mootensai\relation\RelationTrait runs faster
    * @return array relation names of this model
    */
    public function relationNames()
    {
        return [
            'rent'
        ];
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['type', 'status', 'rent_id', 'public'], 'integer'],
            [['content', 'info'], 'string'],
            [['create_time', 'update_time'], 'safe'],
            [['rent_id'], 'required'],
            [['filename', 'extension', 'mime_type', 'base_name'], 'string', 'max' => 255]
        ];
    }

    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'rent_attachment';
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
            'type' => 'Type',
            'status' => 'Status',
            'content' => 'Content',
            'create_time' => 'Create Time',
            'update_time' => 'Update Time',
            'info' => 'Info',
            'rent_id' => 'Rent ID',
            'mime_type' => 'Mime Type',
            'base_name' => 'Base Name',
            'public' => 'Public',
        ];
    }
    
    /**
     * @return \yii\db\ActiveQuery
     */
    public function getRent()
    {
        return $this->hasOne(\common\models\Rent::className(), ['id' => 'rent_id']);
    }
    }
