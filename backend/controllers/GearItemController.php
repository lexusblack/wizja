<?php

namespace backend\controllers;

use common\components\filters\AccessControl;
use common\models\EventSearch;
use common\models\GearService;
use common\models\Gear;
use common\models\GearServiceSearch;
use Yii;
use common\models\GearItem;
use common\models\GearItemSearch;
use common\models\GearItemInactiveSearch;
use backend\components\Controller;
use yii\data\ActiveDataProvider;
use yii\web\NotFoundHttpException;
use yii\helpers\Url;
use yii\web\Response;

/**
 * GearItemController implements the CRUD actions for GearItem model.
 */
class GearItemController extends Controller
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
                    'actions' => ['index', 'deleted'],
                    'roles' => ['gearGear'],
                ],
                [
                    'allow' => true,
                    'actions' => ['create', 'upload'],
                    'roles' => ['gearItemCreate'],

                ],
                [
                    'allow' => true,
                    'actions' => ['view'],
                    'roles' => ['gearItemView'],
                ],
                [
                    'allow' => true,
                    'actions' => ['delete', 'delete-info', 'delete-items'],
                    'roles' => ['gearItemDelete'],
                ],
                [
                    'allow' => true,
                    'actions' => ['update', 'lamps', 'info', 'test', 'edit'],
                    'roles' => ['gearItemEdit'],
                ],
                [
                    'allow' => true,
                    'actions' => ['service'],
                    'roles' => ['gearItemServiceCreate']
                ]
            ],
        ];

        return $behaviors;
    }

    public function actions()
    {
        return [

            'upload'=> [
                'class'=>\backend\actions\UploadAction::className(),
                'upload'=>'/gear-item'

            ]
        ];
    }

    /**
     * Lists all GearItem models.
     * @return mixed
     */
    public function actionDeleted()
    {
        Url::remember();
        $searchModel = new GearItemInactiveSearch();
        $dataProvider = $searchModel->search(Yii::$app->request->queryParams);
        return $this->render('deleted', [
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
        ]);
    }

    public function actionIndex()
    {
        Url::remember();
        $searchModel = new GearItemSearch();

        $dataProvider = $searchModel->search2(Yii::$app->request->queryParams);

        return $this->render('index', [
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
        ]);
    }

    /**
     * Displays a single GearItem model.
     * @param integer $id
     * @return mixed
     */
    public function actionView($id)
    {
        Url::remember();
        $model = $this->findModel($id);
        $service = GearService::getCurrentModel($model->id);
        $searchModel = new EventSearch();
        $params = Yii::$app->request->queryParams;
        $dataProvider = $searchModel->search($params);
        $ids = $model->getEvents()->column();

        $dataProvider->query->andWhere(['event.id'=>$ids]);
        $dataProvider->sort->defaultOrder = ['event_start'=>SORT_DESC];


        $serviceSearchModel = new GearServiceSearch();
        $params[$serviceSearchModel->formName()]['gear_item_id'] = $id;
        $serviceDataProvider = $serviceSearchModel->search($params);
        $serviceDataProvider->sort->defaultOrder = ['update_time'=>SORT_DESC];
        return $this->render('view', [
            'model' => $model,
            'service'=>$service,
            'dataProvider'=>$dataProvider,
            'searchModel' => $searchModel,
            'serviceDataProvider' => $serviceDataProvider,
            'serviceSearchModel' => $serviceSearchModel,

        ]);
    }

    /**
     * Creates a new GearItem model.
     * If creation is successful, the browser will be redirected to the 'view' page.
     * @return mixed
     */
    public function actionCreate($gearId=null)
    {
        $model = new GearItem();
        $model->gear_id = $gearId;
        if ($gearId)
        {
            $item = GearItem::find()->where(['gear_id'=>$gearId])->andWhere(['IS NOT', 'height_case', null])->one();
            if($item){
                $model->height_case = $item->height_case;
                $model->width_case = $item->width_case;
                $model->volume = $item->volume;
                $model->depth_case = $item->depth_case;
                $model->weight_case = $item->weight_case;
                
            }
        }
        if ($model->load(Yii::$app->request->post()) && $model->save())
        {
            Yii::$app->session->setFlash('success', Yii::t('app', 'Zapisano!'));
            return $this->goBack();
        }
        else
        {
            if ($gearId)
            {
                $gear = Gear::findOne($gearId);
                $model->name = $gear->name;
                $gi = GearItem::find()->where(['active'=>1, 'gear_id'=>$gearId])->orderBy('number DESC')->one();
                if ($gi)
                    $model->number = $gi->number+1; 
                else
                    $model->number = 1;
                         
            }

            return $this->render('create', [
                'model' => $model,
            ]);
        }
    }

    /**
     * Updates an existing GearItem model.
     * If update is successful, the browser will be redirected to the 'view' page.
     * @param integer $id
     * @return mixed
     */
    public function actionUpdate($id)
    {
        $model = $this->findModel($id);

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
    public function actionEdit($id)
    {
        $model = $this->findModel($id);
        $model->test_date = date('Y-m-d H:i:s');
        if ($model->load(Yii::$app->request->post()) && $model->save())
        {
            Yii::$app->response->format = Response::FORMAT_JSON;
            return $model;
            exit;
        }
        else
        {
            return $this->renderAjax('edit', [
                'model' => $model,
            ]);
        }
    }
    public function actionLamps($id)
    {
        Yii::$app->response->format = Response::FORMAT_JSON;
        $model = $this->findModel($id);
        $post = Yii::$app->request->post();
        $model->lamp_hours = $post['lamp_hours'];
        $model->save();
        $output = ['output'=>$model->lamp_hours, 'message'=>''];
        return $output;
        exit;
    }

    public function actionInfo($id)
    {
        Yii::$app->response->format = Response::FORMAT_JSON;
        $model = $this->findModel($id);
        $post = Yii::$app->request->post();
        $model->info = $post['info'];
        $model->save();
        $output = ['output'=>$model->info, 'message'=>''];
        return $output;
        exit;
    }

    public function actionTest($id)
    {
        Yii::$app->response->format = Response::FORMAT_JSON;
        $model = $this->findModel($id);
        $post = Yii::$app->request->post();
        $model->tester = $post['tester'];
        $model->test_date = date('Y-m-d H:i:s');
        $model->test_status = Yii::t('app', "Sprawdzony");
        $model->save();
        $output = ['output'=>$model->tester." (".date("d.m.Y", strtotime($model->test_date)).")", 'message'=>''];
        return $output;
        exit;
    }
    /**
     * Deletes an existing GearItem model.
     * If deletion is successful, the browser will be redirected to the 'index' page.
     * @param integer $id
     * @return mixed
     */
    public function actionDelete($id, $goBack = false)
    {
        $model = $this->findModel($id);
        $model->active = 0;
        $model->save();
        \common\models\Note::createNote(1, 'gearItemDeleted', $model, $model->id);
        return $this->redirect(['delete-info', 'id'=>$id]);
    }

    public function actionDeleteInfo($id)
    {
        $model = $this->findModel($id);
        $model->tester = Yii::$app->user->identity->displayLabel;
        if ($model->load(Yii::$app->request->post()) && $model->save())
        {
            Yii::$app->session->setFlash('success', Yii::t('app', 'Zapisano!'));
            return $this->goBack();
        }
        else
        {
            return $this->render('delete-info', [
                'model' => $model
                ]);
        }
    }

    public function actionDeleteItems($id)
    {
        $item = GearItem::find()->where(['gear_id'=>$id])->andWhere(['active'=>1])->one();
        $model = new GearItem();
        $model->attributes = $item->attributes;
        $model->active = 0;
        $model->name = $item->gear->name;
        $model->tester = Yii::$app->user->identity->displayLabel;
        if ($model->load(Yii::$app->request->post()) && $model->save())
        {
            $item->number = $item->gear->quantity - $model->number;
            $item->save();
            $item->gear->quantity = $item->gear->quantity - $model->number;
            $item->gear->save();
            Yii::$app->session->setFlash('success', Yii::t('app', 'Zapisano!'));
            return $this->goBack();
        }
        else
        {
            return $this->render('delete-items', [
                'model' => $model
                ]);
        }
    }

    /**
     * Finds the GearItem model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     * @param integer $id
     * @return GearItem the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function findModel($id)
    {
        if (($model = GearItem::findOne($id)) !== null) {
            return $model;
        } else {
            throw new NotFoundHttpException(Yii::t('app', 'Strona nie istnieje.'));
        }
    }

    public function actionHistory($id)
    {
        /* @var $dataProvider ActiveDataProvider */
        $searchModel = new EventSearch();
        $params = Yii::$app->request->queryParams;
        $dataProvider = $searchModel->search($params);

        $model = $this->findModel($id);
        $ids = $model->getEvents()->column();

        $dataProvider->query->andWhere(['id'=>$ids]);
        $dataProvider->sort->defaultOrder = ['event_start'=>SORT_DESC];


        $serviceSearchModel = new GearServiceSearch();
        $params[$serviceSearchModel->formName()]['gear_item_id'] = $id;
        $serviceDataProvider = $serviceSearchModel->search($params);
        $serviceDataProvider->sort->defaultOrder = ['update_time'=>SORT_DESC];

        return $this->render('history', [
            'model' => $model,
            'dataProvider'=>$dataProvider,
            'searchModel' => $searchModel,
            'serviceDataProvider' => $serviceDataProvider,
            'serviceSearchModel' => $serviceSearchModel,
        ]);
    }

    public function actionService($id)
    {
        $model = $this->findModel($id);
        $service = $model->sendToService();
        Yii::$app->session->setFlash('warning', Yii::t('app', 'Dodane na serwis'));
        return $this->redirect(['/gear-service/update', 'id'=>$service->id]);
    }
}
