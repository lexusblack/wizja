<?php

namespace backend\controllers;

use common\components\filters\AccessControl;
use common\models\GearItem;
use Yii;
use common\models\GearGroup;
use common\models\GearGroupSearch;
use backend\components\Controller;
use yii\helpers\Url;
use yii\web\NotFoundHttpException;
use yii\web\Response;

/**
 * GearGroupController implements the CRUD actions for GearGroup model.
 */
class GearGroupController extends Controller
{

    public $enableCsrfValidation = false;

    public function behaviors()
    {
        $behaviors = parent::behaviors();

        $behaviors['access'] = [
            'class'=>AccessControl::className(),
            'rules' => [
                [
                    'allow'=>true,
                    'actions' => ['index'],
                    'roles' => ['gearCase'],
                ],
                [
                    'allow' => true,
                    'actions' => ['create'],
                    'roles' => ['gearOurWarehouseCreateCase'],

                ],
                [
                    'allow' => true,
                    'actions' => ['view'],
                    'roles' => ['gearCaseView'],
                ],
                [
                    'allow' => true,
                    'actions' => ['delete'],
                    'roles' => ['gearCaseDelete'],
                ],
                [
                    'allow' => true,
                    'actions' => ['update', 'test', 'copy-location'],
                    'roles' => ['gearCaseEdit'],
                ],
                [
                    'allow' => true,
                    'actions' => ['item-remove'],
                    'roles' => ['gearCaseRemoveItem']
                ]
            ],
        ];

        return $behaviors;
    }

    public function actionTest($id)
    {
        Yii::$app->response->format = Response::FORMAT_JSON;
        $model = $this->findModel($id);
        $post = Yii::$app->request->post();
        $model->tester = $post['tester'];
        $model->test_date = date('Y-m-d H:i:s');
        $model->save();
        $gears = GearItem::find()->where(['group_id'=>$id])->all();
        foreach ($gears as $gear)
        {
            $gear->tester = $post['tester'];
            $gear->test_date = date('Y-m-d H:i:s');
            $gear->test_status = Yii::t('app', "Sprawdzony");
            $gear->save();
        }
        $output = ['output'=>$model->tester." (".date("d.m.Y", strtotime($model->test_date)).")", 'message'=>''];
        return $output;
        exit;
    }

    /**
     * Lists all GearGroup models.
     * @return mixed
     */
    public function actionIndex()
    {
        $searchModel = new GearGroupSearch();
        $dataProvider = $searchModel->search(Yii::$app->request->queryParams);
        $dataProvider->query->andWhere(['active'=>1]);
        Url::remember();
        return $this->render('index', [
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
        ]);
    }

    /**
     * Displays a single GearGroup model.
     * @param integer $id
     * @return mixed
     */
    public function actionView($id)
    {
        $model = $this->findModel($id);
        Url::remember();
        return $this->render('view', [
            'model' => $model,
        ]);
    }

    /**
     * Creates a new GearGroup model.
     * If creation is successful, the browser will be redirected to the 'view' page.
     * @return mixed
     */
    public function actionCreate($id=null)
    {
        $model = new GearGroup();

        if ($model->load(Yii::$app->request->post()) && $model->save())
        {
            Yii::$app->session->setFlash('success', Yii::t('app', 'Zapisano!'));
            return $this->goBack();
        }
        else
        {
            return $this->render('create', [
                'model' => $model,
            ]);
        }
    }

    /**
     * Updates an existing GearGroup model.
     * If update is successful, the browser will be redirected to the 'view' page.
     * @param integer $id
     * @return mixed
     */
    public function actionUpdate($id)
    {
        $model = $this->findModel($id);
        if (!$model->volume)
            $model->volume = $model->height*$model->width*$model->depth/1000000;
        if ($model->load(Yii::$app->request->post()) && $model->save())
        {
            Yii::$app->session->setFlash('success', Yii::t('app', 'Zapisano!'));
            return $this->goBack();
        }
        else
        {
            return $this->render('update', [
                'model' => $model,
            ]);
        }
    }

    public function actionCopyLocation($id)
    {
        $model = $this->findModel($id);
        foreach ($model->gearItems as $item)
        {
            $item->warehouse = $model->warehouse;
            $item->location = $model->location;
            $item->save();
        }
        exit;
    }

    public function actionItemRemove($id)
    {
        $model = GearItem::findOne($id);
        $groupId = null;
        if ($model !== null)
        {
            $groupId = $model->group_id;
            $model->updateAttributes(['group_id'=>null]);

            Yii::$app->session->setFlash('success', Yii::t('app', 'Usunięto'));
        }
        if (Url::previous())
        {
            return $this->redirect(Url::previous());
        }
        return $this->redirect(['update', 'id' => $groupId]);
    }

    /**
     * Deletes an existing GearGroup model.
     * If deletion is successful, the browser will be redirected to the 'index' page.
     * @param integer $id
     * @return mixed
     */
    public function actionDelete($id)
    {
        $model = $this->findModel($id);
        $model->active = 0;
        $model->save();
        foreach ($model->gearItems as $gearItem) {
            $gearItem->active = 0;
            $gearItem->save();

            $gearItem->id = null;
            $gearItem->active = 1;
            $gearItem->isNewRecord = true;
            $gearItem->group_id = null;
            $gearItem->save();
        }

        Yii::$app->session->setFlash('success', Yii::t('app', 'Skasowano!'));
        if (Url::previous('warehouse'))
        {
            return $this->redirect(Url::previous('warehouse'));
        }

        return $this->redirect(['index']);
    }

    /**
     * Finds the GearGroup model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     * @param integer $id
     * @return GearGroup the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function findModel($id)
    {
        if (($model = GearGroup::findOne($id)) !== null) {
            return $model;
        } else {
            throw new NotFoundHttpException(Yii::t('app', 'Strona nie istnieje.'));
        }
    }
}
