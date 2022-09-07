<?php

namespace common\models;
use common\helpers\ArrayHelper;

use Yii;
use \common\models\base\AuthItem as BaseAuthItem;

/**
 * This is the model class for table "auth_item".
 */
class AuthItem extends BaseAuthItem
{

	public static function getList()
	{
		$model = AuthItem::find()->where(['type'=>1])->all();
		$list = [];
		foreach ($model as $m)
			{
				if ($m->superuser){
					if ($m->superuser==2)
						$list[$m->name] = $m->name." (user+)";
					else
					$list[$m->name] = $m->name." (superuser)";
				}
				else
					$list[$m->name] = $m->name;
			}
        return $list;

	}
}
