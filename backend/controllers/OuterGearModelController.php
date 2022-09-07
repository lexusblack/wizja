<?php

namespace backend\controllers;

use Yii;
use common\models\OuterGearModel;
use common\models\OuterGear;
use common\models\EventOuterGearModel;
use common\models\RentOuterGearModel;
use common\models\OuterGearModelSearch;
use backend\components\Controller;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;
use sadovojav\image\Thumbnail;
use yii\helpers\Url;
use common\helpers\ArrayHelper;

/**
 * OuterGearModelController implements the CRUD actions for OuterGearModel model.
 */
class OuterGearModelController extends Controller
{

    public $enableCsrfValidation = false;

    public function behaviors()
    {
        $behaviors['access'] = [
            'class'=>\yii\filters\AccessControl::className(),
            'rules' => [
                [
                    'allow'=>true,

                    'actions' => ['index', 'add-outer-gear', 'manage', 'favorite', 'store-order'],
                    'roles' => ['gearOuterWarehouse'],
                ],
                [
                    'allow' => true,
                    'actions' => ['create', 'upload'],
                    'roles' => ['outerGearCreate'],

                ],
                [
                    'allow' => true,
                    'actions' => ['view'],
                    'roles' => ['outerGearView'],
                ],
                [
                    'allow' => true,
                    'actions' => ['delete'],
                    'roles' => ['outerGearDelete'],
                ],
                [
                    'allow' => true,
                    'actions' => ['update'],
                    'roles' => ['outerGearEdit'],
                ],
                [
                    'allow' => true,
                    'actions' => ['error']
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
                'upload'=>'/outer-gear'

            ]
        ];
    }

    /**
     * Lists all OuterGearModel models.
     * @return mixed
     */
    public function actionIndex()
    {
        $searchModel = new OuterGearModelSearch();
        $dataProvider = $searchModel->search(Yii::$app->request->queryParams);

        return $this->render('index', [
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
        ]);
    }

        public function actionFavorite($id)
    {
        $gear = \common\models\OuterGearFavorite::findOne(['outer_gear_id'=>$id, 'user_id'=>Yii::$app->user->id]);
        if ($gear)
        {
            $gear->delete();
        }else{
            $gear = new \common\models\OuterGearFavorite();
            $gear->user_id = Yii::$app->user->id;
            $gear->outer_gear_id = $id;
            $gear->position = \common\models\OuterGearFavorite::find()->where(['user_id'=>Yii::$app->user->id])->count();
            $gear->save();
        }
        exit;
    }

    /**
     * Displays a single OuterGearModel model.
     * @param integer $id
     * @return mixed
     */
    
    public function actionManage($id, $type='event', $prod = 0)
    {
        if ($type=='event')
        {
        $model = EventOuterGearModel::findOne($id);
        $outerGears = OuterGear::find()->where(['outer_gear_model_id'=>$model->outer_gear_model_id, 'active'=>1])->all();
        $this->layout = false;
        $settings = \common\models\Settings::find()->indexBy('key')->where(['section'=>'main'])->all();

        $names = explode(" ", $model->outerGearModel->name);
        $models1 = \common\models\GearModel::find();
        $models2 = \common\models\GearModel::find();
        foreach($names as $name)
        {
            if (strlen($name)>2)
            {
                $models1->andWhere(['like', 'name', $name]);
                $models2->orWhere(['like', 'name', $name]);
            }

        }
        $ids1 = \common\helpers\ArrayHelper::map($models1->asArray()->all(), 'id', 'id');
        $ids2 = \common\helpers\ArrayHelper::map($models2->asArray()->all(), 'id', 'id');
        //echo var_dump($ids2);
                $crn_event = [];
                $crn_event2 = [];
        $company = \common\models\Company::find()->where(['code'=>Yii::$app->params['companyID']])->one();
        $lat_min = $company->latitude-0.2;
        $lat_max = $company->latitude+0.2;
        $lon_min = $company->longitude-0.2;
        $lon_max = $company->longitude+0.2;
        $crn_warehouse = \common\models\CrossRental::find()->where(['gear_model_id'=>$ids1])
        ->andWhere(['>', 'latitude', $lat_min])
        ->andWhere(['<', 'latitude', $lat_max])
        ->andWhere(['>', 'longitude', $lon_min])
        ->andWhere(['<', 'longitude', $lon_max])
        ->andWhere(['<>', 'owner', Yii::$app->params['companyID']])->all();
        $c_ids1 = ArrayHelper::map(\common\models\CrossRental::find()->where(['gear_model_id'=>$ids1])->andWhere(['>', 'latitude', $lat_min])
        ->andWhere(['<', 'latitude', $lat_max])
        ->andWhere(['>', 'longitude', $lon_min])
        ->andWhere(['<', 'longitude', $lon_max])
        ->andWhere(['<>', 'owner', Yii::$app->params['companyID']])->asArray()->all(), 'id', 'id');
        $crn_warehouse2 = \common\models\CrossRental::find()->where(['gear_model_id'=>$ids2])
        ->andWhere(['>', 'latitude', $lat_min])
        ->andWhere(['<', 'latitude', $lat_max])
        ->andWhere(['>', 'longitude', $lon_min])
        ->andWhere(['<', 'longitude', $lon_max])
        ->andWhere(['<>', 'owner', Yii::$app->params['companyID']])->andWhere(['NOT IN', 'id', $c_ids1])->all();
        if (isset($model->event->location)){
            if ($model->event->location!=$settings['companyCity']->value){
                $location = $model->event->location;
                        $lat_min = $location->latitude-0.2;
                        $lat_max = $location->latitude+0.2;
                        $lon_min = $location->longitude-0.2;
                        $lon_max = $location->longitude+0.2;
                $crn_event = \common\models\CrossRental::find()->where(['gear_model_id'=>$ids1])
                ->andWhere(['>', 'latitude', $lat_min])
                ->andWhere(['<', 'latitude', $lat_max])
                ->andWhere(['>', 'longitude', $lon_min])
                ->andWhere(['<', 'longitude', $lon_max])
                ->andWhere(['<>', 'owner', Yii::$app->params['companyID']])->all();
                $c_ids1 = ArrayHelper::map(\common\models\CrossRental::find()->where(['gear_model_id'=>$ids1])
                    ->andWhere(['>', 'latitude', $lat_min])
                    ->andWhere(['<', 'latitude', $lat_max])
                    ->andWhere(['>', 'longitude', $lon_min])
                    ->andWhere(['<', 'longitude', $lon_max])
                    ->andWhere(['<>', 'owner', Yii::$app->params['companyID']])->asArray()->all(), 'id', 'id');
                $crn_event2 = \common\models\CrossRental::find()->where(['gear_model_id'=>$ids2])
                ->andWhere(['>', 'latitude', $lat_min])
                ->andWhere(['<', 'latitude', $lat_max])
                ->andWhere(['>', 'longitude', $lon_min])
                ->andWhere(['<', 'longitude', $lon_max])
                ->andWhere(['<>', 'owner', Yii::$app->params['companyID']])->andWhere(['NOT IN', 'id', $c_ids1])->all();
            }
        }else{
            if ($model->event->address!="")
            {
                if (!strstr($model->event->address, $settings['companyCity']->value)){
                    $address = $model->event->address;
                $to =  $address;
                $to = urlencode($to);
                $apiKey= "AIzaSyAPDBOEfgjSaEHEiC8Zx3BpV5lT_cIRiBQ";  
                $data = file_get_contents("https://maps.googleapis.com/maps/api/geocode/json?address=".$to."&key=".$apiKey);
                $data = json_decode($data, true);
                if ($data['status']=="OK")
                {
                    if (isset($data['results'][0]))
                    {
                       $r = $data['results'][0];
                       $latitude = $r['geometry']['location']['lat'];
                       $longitude = $r['geometry']['location']['lng'];
                       $lat_min = $latitude-0.2;
                        $lat_max = $latitude+0.2;
                        $lon_min = $longitude-0.2;
                        $lon_max = $longitude+0.2;
                        $crn_event = \common\models\CrossRental::find()->where(['gear_model_id'=>$ids1])
                        ->andWhere(['>', 'latitude', $lat_min])
                        ->andWhere(['<', 'latitude', $lat_max])
                        ->andWhere(['>', 'longitude', $lon_min])
                        ->andWhere(['<', 'longitude', $lon_max])
                        ->andWhere(['<>', 'owner', Yii::$app->params['companyID']])->all();
                        $c_ids1 = ArrayHelper::map(\common\models\CrossRental::find()->where(['gear_model_id'=>$ids1])
                            ->andWhere(['>', 'latitude', $lat_min])
                            ->andWhere(['<', 'latitude', $lat_max])
                            ->andWhere(['>', 'longitude', $lon_min])
                            ->andWhere(['<', 'longitude', $lon_max])
                            ->andWhere(['<>', 'owner', Yii::$app->params['companyID']])->asArray()->all(), 'id', 'id');
                        $crn_event2 = \common\models\CrossRental::find()->where(['gear_model_id'=>$ids2])
                        ->andWhere(['>', 'latitude', $lat_min])
                        ->andWhere(['<', 'latitude', $lat_max])
                        ->andWhere(['>', 'longitude', $lon_min])
                        ->andWhere(['<', 'longitude', $lon_max])
                        ->andWhere(['<>', 'owner', Yii::$app->params['companyID']])->andWhere(['NOT IN', 'id', $c_ids1])->all();
                    }
                }
                
            }
            }
        }
        return $this->renderAjax('manage', [
            'model' => $model,
            'outerGears' => $outerGears,
            'type'=>$type,
            'prod'=>$prod,
            'crn'=>['cw'=>$crn_warehouse, 'cw2'=>$crn_warehouse2, 'ce'=>$crn_event, 'ce2'=>$crn_event2]
        ]);
        exit;            
    }else{
        $model = RentOuterGearModel::findOne($id);
        $outerGears = OuterGear::find()->where(['outer_gear_model_id'=>$model->outer_gear_model_id, 'active'=>1])->all();
        $this->layout = false;
        return $this->renderAjax('manage', [
            'model' => $model,
            'outerGears' => $outerGears,
            'type'=>$type,
            'prod'=>$prod,
        ]);
        exit;
    }

    }


    public function actionView($id)
    {
        $model = $this->findModel($id);
        Url::remember('', 'outer-model');
        $providerOuterGear = new \yii\data\ArrayDataProvider([
            'allModels' => $model->outerGears,
        ]);
        return $this->render('view', [
            'model' => $this->findModel($id),
            'providerOuterGear' => $providerOuterGear,
        ]);
    }

    /**
     * Creates a new OuterGearModel model.
     * If creation is successful, the browser will be redirected to the 'view' page.
     * @return mixed
     */
    public function actionCreate()
    {
        $model = new OuterGearModel();

        if ($model->load(Yii::$app->request->post()) && $model->save()) {
            return $this->redirect(['view', 'id' => $model->id]);
        } else {
            return $this->render('create', [
                'model' => $model,
            ]);
        }
    }

    /**
     * Updates an existing OuterGearModel model.
     * If update is successful, the browser will be redirected to the 'view' page.
     * @param integer $id
     * @return mixed
     */
    public function actionUpdate($id)
    {
        $model = $this->findModel($id);
        if($model->category_id==1)
        {
            $model->category_id = null;
        }
        if ($model->load(Yii::$app->request->post()) && $model->save()) {
            return $this->redirect(['view', 'id' => $model->id]);
        } else {
            return $this->render('update', [
                'model' => $model,
            ]);
        }
    }

    /**
     * Deletes an existing OuterGearModel model.
     * If deletion is successful, the browser will be redirected to the 'index' page.
     * @param integer $id
     * @return mixed
     */
    public function actionDelete($id)
    {
            $model = $this->findModel($id);
            $model->active = 0;
            $model->name = "dd";
            $model->save();

        return $this->goBack();
    }

    public function actionStoreOrder($favorite=null)
    {
        $data = Yii::$app->request->post('data', null);
        if ($favorite)
        {
            if ($data !== null)
                $models = \common\models\OuterGearFavorite::findAll(['outer_gear_id'=>$data]);
                foreach ($models as $model)
            {
                $model->position = array_search($model->outer_gear_id, $data)+1;
                $model->update(false);
            }
        }else{
            
            if ($data !== null)
            $models = OuterGearModel::findAll($data);
            foreach ($models as $model)
            {
                $model->sort_order = array_search($model->id, $data);
                $model->update(false);
            }
        }
    }

    
    /**
     * Finds the OuterGearModel model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     * @param integer $id
     * @return OuterGearModel the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function findModel($id)
    {
        if (($model = OuterGearModel::findOne($id)) !== null) {
            return $model;
        } else {
            throw new NotFoundHttpException(Yii::t('app', 'Strona nie istnieje.'));
        }
    }
    
    /**
    * Action to load a tabular form grid
    * for OuterGear
    * @author Yohanes Candrajaya <moo.tensai@gmail.com>
    * @author Jiwantoro Ndaru <jiwanndaru@gmail.com>
    *
    * @return mixed
    */
    public function actionAddOuterGear()
    {
        if (Yii::$app->request->isAjax) {
            $row = Yii::$app->request->post('OuterGear');
            if((Yii::$app->request->post('isNewRecord') && Yii::$app->request->post('_action') == 'load' && empty($row)) || Yii::$app->request->post('_action') == 'add')
                $row[] = [];
            return $this->renderAjax('_formOuterGear', ['row' => $row]);
        } else {
            throw new NotFoundHttpException(Yii::t('app', 'Strona nie istnieje.'));
        }
    }
}
