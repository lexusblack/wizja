<?php

namespace common\models;

use Yii;
use \common\models\base\Team as BaseTeam;

/**
 * This is the model class for table "team".
 */
class Team extends BaseTeam
{
    /**
     * @inheritdoc
     */

    public $userIds;
    public function rules()
    {
        return array_replace_recursive(parent::rules(),
	    [
            [['active'], 'integer'],
            [['name'], 'string', 'max' => 255],
            [['userIds'], 'each', 'rule' => ['integer']],
        ]);
    }

    public function behaviors()
    {
        $behaviors = parent::behaviors();


        $behaviors['link'] = [
            'class' => \common\behaviors\LinkBehavior::className(),
            'attributes' => [
                'userIds',
            ],
            'relations' => [
                'users',
            ],
            'modelClasses' => [
                'common\models\User',
            ],
        ];
        return $behaviors;
    }
	
}
