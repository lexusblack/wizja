<?php

namespace common\models;

use common\helpers\ArrayHelper;
use Yii;
use \common\models\base\AddonRate as BaseAddonRate;

/**
 * This is the model class for table "addon_rate".
 */
class AddonRate extends BaseAddonRate
{
    const PERIOD_EVENT = -1;
    const PERIOD_8H = 8;
    const PERIOD_12H = 12;
    const PERIOD_24H = 24;
    const PERIOD_DAY = 0;

    public $roleIds;

    function behaviors() {
	    $behaviors = parent::behaviors();

	    $behaviors['link'] = [
		    'class' => \common\behaviors\LinkBehavior::className(),
		    'attributes' => [
			    'roleIds',
		    ],
		    'relations' => [
			    'roles',
		    ],
		    'modelClasses'=>[
			    'common\models\UserEventRole',
		    ],
	    ];

	    return $behaviors;
    }

	public function rules()
	{
		$rules = [
			[['roleIds'], 'each', 'rule'=>['integer']],
		];
		return array_merge(parent::rules(), $rules);
	}

	public static function getPeriodList()
    {
        $list = [
            self::PERIOD_EVENT => Yii::t('app', 'Wydarzenie'),
            self::PERIOD_DAY =>Yii::t('app',  'Raz dziennie'),
            self::PERIOD_8H => Yii::t('app', '8 godzin'),
            self::PERIOD_12H => Yii::t('app', '12 godzin'),
            self::PERIOD_24H => Yii::t('app', '24 godziny'),
        ];

        return $list;
    }

    public function getPeriodLabel()
    {
        return ArrayHelper::getValue(static::getPeriodList(), $this->period, UNDEFINDED_STRING);
    }
}
