<?php
namespace backend\components;

use Yii;
use yii\filters\VerbFilter;
use yii\filters\AccessControl;
use common\models\Event;

class Controller extends \yii\web\Controller
{
    const LAYOUT_PANEL = '@backend/themes/e4e/layouts/panel';
    const LAYOUT_MAIN_PANEL = '@backend/themes/e4e/layouts/main-panel';

    public $layout = 'main-panel';
	public function behaviors()
    {
        $behaviors = [
            'access' => [
                'class' => AccessControl::className(),
                'rules' => [
                    [
                        'allow' => true,
                        'roles' => ['@'],
                    ],
                ],
            ],
            'verbs' => [
                'class' => VerbFilter::className(),
                'actions' => [
                    'delete' => ['post'],
                ],
            ],
        ];
        return $behaviors;
    }

    protected function findEvent($id)
    {
        if (($model = Event::findOne($id)) !== null) {
            return $model;
        } else {
            throw new NotFoundHttpException(Yii::t('app', 'Strona nie istnieje.'));
        }
    }
}
