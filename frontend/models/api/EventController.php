<?php

namespace frontend\models\api;

/**
* This is the class for REST controller "EventController".
*/

use yii\filters\AccessControl;
use yii\helpers\ArrayHelper;

class EventController extends \yii\rest\ActiveController
{
public $modelClass = 'common\models\Event';
}
