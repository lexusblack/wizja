<?php

namespace common\models;

use Yii;
use \common\models\base\MobileQrScan as BaseMobileQrScan;

/**
 * This is the model class for table "mobile_qr_scan".
 */
class MobileQrScan extends BaseMobileQrScan
{
	const TYPE_GEAR_OUR = 1;
	const TYPE_GEAR_OUTER = 2;
	const TYPE_CASE = 3;

}
