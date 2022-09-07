<?php

namespace common\models;

use Yii;
use \common\models\base\SettingAttachment as BaseSettingAttachment;
use yii\helpers\ArrayHelper;

/**
 * This is the model class for table "setting_attachment".
 */
class SettingAttachment extends BaseSettingAttachment
{
    const TYPE_OFFER = 1;

    public function behaviors()
    {
        return ArrayHelper::merge(
            parent::behaviors(),
            [
                # custom behaviors
            ]
        );
    }

    public function rules()
    {
        return ArrayHelper::merge(
             parent::rules(),
             [
                  # custom validation rules
             ]
        );
    }

    public function getFileUrl()
    {
        return Yii::getAlias('@uploads/settings-attachment/'.$this->filename);
    }

    public function getFilePath()
    {
        return Yii::getAlias('@uploadroot/settings-attachment/'.$this->filename);
    }
}
