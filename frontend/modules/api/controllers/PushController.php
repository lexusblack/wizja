<?php
namespace frontend\modules\api\controllers;

use app\modules\api\actions\push\MessageAction;
use yii\web\Controller;
use yii\filters\VerbFilter;

class PushController extends Controller
{
    public $enableCsrfValidation = false;

    public function actions()
    {
        return [
            'message'=> [
                'class'=>MessageAction::className(),
            ]
        ];
    }

    public function behaviors()
    {
        return [
            'verbs' => [
                'class' => VerbFilter::className(),
                'actions' => [
                    'message'  => ['post'],

                ],
            ],
        ];
    }


}