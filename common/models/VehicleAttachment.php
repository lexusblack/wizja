<?php

namespace common\models;

use Yii;
use \common\models\base\VehicleAttachment as BaseVehicleAttachment;

/**
 * This is the model class for table "vehicle_attachment".
 */
class VehicleAttachment extends BaseVehicleAttachment
{

    public function getFileUrl()
    {
        $url = $this->loadFileUrl('filename', '@uploads/vehicle-attachment/');
        return $url;
    }

    public function getFilePath()
    {
        $url = $this->loadFileUrl('filename', '@uploadroot/vehicle-attachment/');
        return $url;
    }
}
