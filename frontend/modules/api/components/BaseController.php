<?php
namespace frontend\modules\api\components;

use yii\helpers\Inflector;
use yii\helpers\StringHelper;
use yii\rest\ActiveController;
use yii\data\ActiveDataProvider;

class BaseController extends ActiveController
{
    public $serializer = [
        'class' => 'yii\rest\Serializer',
        'collectionEnvelope' => 'items',
    ];

    public function init()
    {
        parent::init();
        $modelClass = StringHelper::basename($this->modelClass);
        $this->serializer['collectionEnvelope'] = Inflector::camel2id($modelClass);
    }

    public function beforeAction($action)
    {
        \Yii::info($this->route, 'api\action');
        return parent::beforeAction($action);
    }

    public function actions()
    {
        $actions = parent::actions();
        $actions['view']['class'] = 'frontend\modules\api\actions\ViewAction';
        $actions['create']['class'] = 'frontend\modules\api\actions\CreateAction';
        $actions['update']['class'] = 'frontend\modules\api\actions\UpdateAction';

//        // disable the "delete" and "create" actions
//        unset($actions['delete'], $actions['create']);

        // customize the data provider preparation with the "prepareDataProvider()" method
        $actions['index']['class'] = 'app\modules\api\actions\IndexAction';
//        $actions['index']['prepareDataProvider'] = [$this, 'prepareDataProvider'];

        return $actions;
    }

    public function prepareDataProvider()
    {
        $className = $this->modelClass;
        $query = $className::find();
        return new ActiveDataProvider([
            'query'=>$query,
            'pagination' => false
        ]);
    }

}