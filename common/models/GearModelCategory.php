<?php

namespace common\models;

use Yii;
use common\models\GearModel;
use common\helpers\ArrayHelper;

/**
 * This is the model class for table "gear_category".
 */
class GearModelCategory extends GearCategory
{
        public static function getDb() {
        return Yii::$app->db2;
    }

    public static function getNoEmptyList(){

	    $ids = GearModel::find()->all();
	    $ids = ArrayHelper::map($ids, 'id', 'category_id');
	    $roots = self::find()->indexBy('id')->where(['active'=>1, 'visible'=>1, 'readonly'=>0])->andWhere(['IN', 'id', $ids])->addOrderBy('root, lft')->all();
        $list = ArrayHelper::map($roots, 'id', 'name');
        return $list;
	}
}
