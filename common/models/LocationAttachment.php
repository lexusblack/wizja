<?php

namespace common\models;

use common\helpers\ArrayHelper;
use common\helpers\ImageHelper;
use Yii;
use \common\models\base\LocationAttachment as BaseLocationAttachment;

/**
 * This is the model class for table "location_attachment".
 */
class LocationAttachment extends BaseLocationAttachment
{
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

    public function getFileUrl()
    {
        return Yii::getAlias('@uploads/location/'.$this->filename);
    }

    public function getFilePath()
    {
        return Yii::getAlias('@uploadroot/location/'.$this->filename);
    }

    public function getFileThumbUrl($options = [])
    {
        $thumbUrl = ImageHelper::getFileThumbnailUrl($this->getFilePath());
        return $thumbUrl;

    }
}
