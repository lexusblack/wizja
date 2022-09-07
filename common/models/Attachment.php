<?php

namespace common\models;

use common\helpers\ArrayHelper;
use Yii;
use \common\models\base\Attachment as BaseAttachment;

/**
 * This is the model class for table "attachment".
 */
class Attachment extends BaseAttachment
{
    const T_PUBLIC = 1;
    const T_PRIVATE = 0;

    const TYPE_FILE = 1;
    const TYPE_IMAGE = 5;
    const TYPE_GALLERY = 6;
    const TYPE_PANORAMA = 10;

    public static function getTypeList()
    {
        return [
            self::TYPE_FILE => Yii::t('app', 'Plik'),
            self::TYPE_IMAGE => Yii::t('app', 'Obraz'),
//            self::TYPE_GALLERY => Yii::t('app', 'Obraz galerii'),
            self::TYPE_PANORAMA => Yii::t('app', 'Panorama'),
        ];
    }

    public function getTypeLabel()
    {
        $list = self::getTypeList();
        $index = $this->type;
        return isset($list[$index]) ? $list[$index] : UNDEFINDED_STRING;
    }


    public static function getPublicList()
    {
        return [
            self::T_PUBLIC => Yii::t('app', 'Publiczny'),
            self::T_PRIVATE => Yii::t('app', 'Prywatny'),
        ];
    }

    public function getPublicLabel()
    {
        $list = self::getPublicList();
        $index = $this->type;
        return isset($list[$index]) ? $list[$index] : UNDEFINDED_STRING;
    }

    public function getFileUrl()
    {
        return Yii::getAlias('@uploads/attachment/'.$this->filename);
    }

    public function getFileThumbUrl($options = [])
    {
        $defaultOptions = [
            'thumbnail' => [
                'width' => 200,
                'height' => 200,
            ],
            'placeholder' => [
                'width' => 200,
                'height' => 200
            ]
        ];
        $options = ArrayHelper::merge($defaultOptions, $options);
        $thumb = Yii::$app->thumbnail->url($this->getFilePath(), $options);
        return $thumb;
    }

    public function getFilePath()
    {
        return Yii::getAlias('@uploadroot/attachment/'.$this->filename);
    }

    public function afterSave($insert, $changedAttributes)
    {
        parent::afterSave($insert, $changedAttributes);
        if ($insert)
            Note::createNote(2, 'eventAttachmentAdded', $this, $this->event_id);


    }
}
