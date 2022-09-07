<?php

namespace common\models;

use Yii;
use \common\models\base\HallGroupStatut as BaseHallGroupStatut;

/**
 * This is the model class for table "hall_group_statut".
 */
class HallGroupStatut extends BaseHallGroupStatut
{
    /**
     * @inheritdoc
     */
    public function rules()
    {
        return array_replace_recursive(parent::rules(),
	    [
            [['active', 'position', 'final'], 'integer'],
            [['name'], 'string', 'max' => 255],
            [['color'], 'string', 'max' => 45]
        ]);
    }
	
}
