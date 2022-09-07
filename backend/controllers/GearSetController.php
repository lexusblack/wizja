<?php

namespace backend\controllers;

use Yii;
use common\components\filters\AccessControl;
use common\models\GearSet;
use common\models\GearSetSearch;
use backend\components\Controller;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;

/**
 * GearSetController implements the CRUD actions for GearSet model.
 */
class GearSetController extends Controller
{
    public function behaviors()
    {
        
$behaviors = parent::behaviors();

        $behaviors['access'] = [
            'class'=>AccessControl::className(),
            'rules' => [
                [
                    'allow'=>true,
                    'actions' => ['index'],
                    'roles' => ['gearSet'],
                ],
                [
                    'allow' => true,
                    'actions' => ['create'],
                    'roles' => ['gearSetCreate'],

                ],
                [
                    'allow' => true,
                    'actions' => ['view'],
                    'roles' => ['gearSetView'],
                ],
                [
                    'allow' => true,
                    'actions' => ['delete'],
                    'roles' => ['gearSetDelete'],
                ],
                [
                    'allow' => true,
                    'actions' => ['update'],
                    'roles' => ['gearSetEdit'],
                ],
                [
                    'allow' => true,
                    'actions' => ['add-gear-set-item', 'add-gear-set-outer-item', 'upload']
                ]
            ],
        ];

        $behaviors['verbs'] = [
                'class' => VerbFilter::className(),
                'actions' => [
                    'delete' => ['post'],
                ]
                ];
        return $behaviors;
    }

    public function actions()
    {
        return [

            'upload'=> [
                'class'=>\backend\actions\UploadAction::className(),
                'upload'=>'/gear'

            ]
        ];
    }
    /**
     * Lists all GearSet models.
     * @return mixed
     */
    public function actionIndex()
    {
        $searchModel = new GearSetSearch();
        $dataProvider = $searchModel->search(Yii::$app->request->queryParams);

        return $this->render('index', [
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
        ]);
    }

    /**
     * Displays a single GearSet model.
     * @param integer $id
     * @return mixed
     */
    public function actionView($id)
    {
        $model = $this->findModel($id);
        $providerGearSetItem = new \yii\data\ArrayDataProvider([
            'allModels' => $model->gearSetItems,
        ]);
        $providerGearSetOuterItem = new \yii\data\ArrayDataProvider([
            'allModels' => $model->gearSetOuterItems,
        ]);
        return $this->render('view', [
            'model' => $this->findModel($id),
            'providerGearSetItem' => $providerGearSetItem,
            'providerGearSetOuterItem' => $providerGearSetOuterItem,
        ]);
    }

    /**
     * Creates a new GearSet model.
     * If creation is successful, the browser will be redirected to the 'view' page.
     * @return mixed
     */
    public function actionCreate()
    {
        $model = new GearSet();
        $model->category_id = 1;
        if ($model->loadAll(Yii::$app->request->post()) && $model->saveAll()) {
            $model->saveOuterGear(Yii::$app->request->post('GearSetOuterItem'));
            return $this->redirect(['view', 'id' => $model->id]);
        } else {
            return $this->render('create', [
                'model' => $model,
            ]);
        }
    }

    /**
     * Updates an existing GearSet model.
     * If update is successful, the browser will be redirected to the 'view' page.
     * @param integer $id
     * @return mixed
     */
    public function actionUpdate($id)
    {
        $model = $this->findModel($id);
        if ($model->loadAll(Yii::$app->request->post()) && $model->saveAll()) {
            $model->saveOuterGear(Yii::$app->request->post('GearSetOuterItem'));
            return $this->redirect(['view', 'id' => $model->id]);
        } else {
            return $this->render('update', [
                'model' => $model,
            ]);
        }
    }

    /**
     * Deletes an existing GearSet model.
     * If deletion is successful, the browser will be redirected to the 'index' page.
     * @param integer $id
     * @return mixed
     */
    public function actionDelete($id)
    {
        $this->findModel($id)->deleteWithRelated();

        return $this->redirect(['index']);
    }

    
    /**
     * Finds the GearSet model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     * @param integer $id
     * @return GearSet the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function findModel($id)
    {
        if (($model = GearSet::findOne($id)) !== null) {
            return $model;
        } else {
            throw new NotFoundHttpException('The requested page does not exist.');
        }
    }
    
    /**
    * Action to load a tabular form grid
    * for GearSetItem
    * @author Yohanes Candrajaya <moo.tensai@gmail.com>
    * @author Jiwantoro Ndaru <jiwanndaru@gmail.com>
    *
    * @return mixed
    */
    public function actionAddGearSetItem()
    {
        if (Yii::$app->request->isAjax) {
            $row = Yii::$app->request->post('GearSetItem');
            if((Yii::$app->request->post('isNewRecord') && Yii::$app->request->post('_action') == 'load' && empty($row)) || Yii::$app->request->post('_action') == 'add')
                $row[] = [];
            return $this->renderAjax('_formGearSetItem', ['row' => $row]);
        } else {
            throw new NotFoundHttpException('The requested page does not exist.');
        }
    }

    public function actionAddGearSetOuterItem()
    {
        if (Yii::$app->request->isAjax) {
            $row = Yii::$app->request->post('GearSetOuterItem');
            if((Yii::$app->request->post('isNewRecord') && Yii::$app->request->post('_action') == 'load' && empty($row)) || Yii::$app->request->post('_action') == 'add')
                $row[] = [];
            return $this->renderAjax('_formGearSetOuterItem', ['row' => $row]);
        } else {
            throw new NotFoundHttpException('The requested page does not exist.');
        }
    }
}
