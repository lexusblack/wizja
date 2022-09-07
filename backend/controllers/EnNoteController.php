<?php

namespace backend\controllers;

use Yii;
use common\models\EnNote;
use common\models\CrossRental;
use common\models\LocationPlan;
use common\models\LocationPanorama;
use yii\data\ActiveDataProvider;
use backend\components\Controller;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;

/**
 * EnNoteController implements the CRUD actions for EnNote model.
 */
class EnNoteController extends Controller
{
    public function behaviors()
    {
        return [
            'verbs' => [
                'class' => VerbFilter::className(),
                'actions' => [
                    'delete' => ['post'],
                ],
            ],
            'access' => [
                'class' => \yii\filters\AccessControl::className(),
                'rules' => [
                    [
                        'allow' => true,
                        'actions' => ['index', 'create-notes'],
                        'roles' => ['@']
                    ],
                    [
                        'allow' => false
                    ]
                ]
            ]
        ];
    }

    /**
     * Lists all EnNote models.
     * @return mixed
     */
    public function actionIndex()
    {
        $d2 = date('Y-m-d', strtotime('-100 days'));
        $notes = EnNote::find()->where(['>', 'datetime', $d2])->orderBy(['datetime'=>SORT_DESC])->all();
        EnNote::setAllRead();
        return $this->render('index', [
            'notes' => $notes,
        ]);
    }

    public function actionCreateNotes()
    {
        $crn = CrossRental::find()->all();
        foreach ($crn as $c)
        {
            EnNote::createNote('CRN', $c);
        }
        $plans = LocationPlan::find()->andWhere(['public'=>1])->all();
        foreach ($plans as $c)
        {
            EnNote::createNote('Plan', $c);
        }
        $panoramas = LocationPanorama::find()->andWhere(['public'=>1])->all();
        foreach ($plans as $c)
        {
            EnNote::createNote('Panorama', $c);
        }
        exit;
    }

    protected function findModel($id)
    {
        if (($model = EnNote::findOne($id)) !== null) {
            return $model;
        } else {
            throw new NotFoundHttpException('The requested page does not exist.');
        }
    }
}
