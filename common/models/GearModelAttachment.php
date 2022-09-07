<?php

namespace common\models;
use Yii;
use \common\models\base\GearModelAttachment as BaseGearModelAttachment;
use yii\behaviors\TimestampBehavior;
use yii\behaviors\BlameableBehavior;
/**
 * This is the model class for table "gear_model_attachment".
 */
class GearModelAttachment extends BaseGearModelAttachment
{

    public function behaviors()
    {
        return [
            'timestamp' => [
                'class' => TimestampBehavior::className(),
                'createdAtAttribute' => 'create_time',
                'updatedAtAttribute' => 'update_time',
                'value' => new \yii\db\Expression('NOW()'),
            ]
        ];
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return array_replace_recursive(parent::rules(),
	    [
            [['type', 'status', 'gear_model_id'], 'integer'],
            [['create_time', 'update_time'], 'safe'],
            [['filename'], 'string', 'max' => 255],
            [['extension'], 'string', 'max' => 255]
        ]);
    }
    public function getFileUrl()
    {
        return Yii::getAlias('@uploadsAll/gear-attachment/'.$this->filename);
    }

    public function getFilePath()
    {
        return Yii::getAlias('@uploadrootAll/gear-attachment/'.$this->filename);
    }
	
}
