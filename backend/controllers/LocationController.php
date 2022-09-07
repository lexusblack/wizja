<?php

namespace backend\controllers;

use common\components\filters\AccessControl;
use Yii;
use common\models\Location;
use common\models\LocationSearch;
use backend\components\Controller;
use yii\helpers\Url;
use yii\web\NotFoundHttpException;
use yii\web\Response;

/**
 * LocationController implements the CRUD actions for Location model.
 */
class LocationController extends Controller
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
                    'actions' => ['index', 'send-mail', 'public', 'update-coord'],
                    'roles' => ['locationLocations'],
                ],
                [
                    'allow' => true,
                    'actions' => ['create', 'upload'],
                    'roles' => ['locationLocationsAdd'],

                ],
                [
                    'allow' => true,
                    'actions' => ['view'],
                    'roles' => ['locationLocationsView'],
                ],
                [
                    'allow' => true,
                    'actions' => ['delete'],
                    'roles' => ['locationLocationsDelete'],
                ],
                [
                    'allow' => true,
                    'actions' => ['update', 'upload'],
                    'roles' => ['locationLocationsEdit'],
                ],
                [
                    'allow' => true,
                    'actions' => ['list'],
                    'roles' => ['@'],
                ],
            ],
        ];

        return $behaviors;
    }

    public function actions()
    {
        $actions = [

            'upload'=> [
                'class'=>\backend\actions\UploadAction::className(),
                'upload'=>'/location'

            ]
        ];

        return array_merge(parent::actions(), $actions);
    }
    /**
     * Lists all Location models.
     * @return mixed
     */
    public function actionIndex()
    {
        $searchModel = new LocationSearch();
        $dataProvider = $searchModel->search(Yii::$app->request->queryParams);
        $dataProvider->query->andWhere(['active'=>1]);

        return $this->render('index', [
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
        ]);
    }

    public function actionPublic()
    {
        $searchModel = new LocationSearch();
        $dataProvider = $searchModel->search(Yii::$app->request->queryParams);
        $dataProvider->query->andWhere(['active'=>1, 'public'=>2]);

        return $this->render('public', [
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
        ]);
    }

    public function actionUpdateCoord()
    {
        $models = Location::find()->where(['latitude'=>NULL])->orderBy(['id'=>SORT_DESC])->limit(500)->all();
        foreach ($models as $model)
        {
            $model->saveCoordinates();
        }
        exit;
    }

    /**
     * Displays a single Location model.
     * @param integer $id
     * @return mixed
     */
    public function actionView($id)
    {
        Url::remember('', 'location');
        $model = $this->findModel($id);
        $model->saveCoordinates();
        return $this->render('view', [
            'model' => $model,
        ]);
    }

    /**
     * Creates a new Location model.
     * If creation is successful, the browser will be redirected to the 'view' page.
     * @return mixed
     */
    public function actionCreate()
    {
        $model = new Location();

        if ($model->load(Yii::$app->request->post()) && $model->save())
        {
            if (Yii::$app->request->isAjax)
            {
                Yii::$app->response->format = Response::FORMAT_JSON;
                return $model->attributes;
            }
            Yii::$app->session->setFlash('success', Yii::t('app', 'Zapisano!'));
            return $this->redirect(['view', 'id' => $model->id]);
        }
        else
        {
            return $this->render('create', [
                'model' => $model,
            ]);
        }
    }

    /**
     * Updates an existing Location model.
     * If update is successful, the browser will be redirected to the 'view' page.
     * @param integer $id
     * @return mixed
     */
    public function actionUpdate($id)
    {
        $model = $this->findEditableModel($id);

        if ($model->load(Yii::$app->request->post()) && $model->save())
        {
            Yii::$app->session->setFlash('success', Yii::t('app', 'Zapisano!'));
            return $this->redirect(['view', 'id' => $model->id]);
        }
        else
        {
            $providerPhoto = new \yii\data\ArrayDataProvider([
                'allModels' => $model->locationPhotos,
            ]);
            return $this->render('update', [
                'model' => $model,
                'providerPhoto'=>$providerPhoto
            ]);
        }
    }

    /**
     * Deletes an existing Location model.
     * If deletion is successful, the browser will be redirected to the 'index' page.
     * @param integer $id
     * @return mixed
     */
    public function actionDelete($id)
    {
        $model = $this->findEditableModel($id);
        $model->active = 0;
        $model->save();

        return $this->redirect(['index']);
    }

    /**
     * Finds the Location model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     * @param integer $id
     * @return Location the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function findModel($id)
    {
        if (($model = Location::findOne($id)) !== null) {
            return $model;
        } else {
            throw new NotFoundHttpException(Yii::t('app', 'Strona nie istnieje.'));
        }
    }

    protected function findEditableModel($id)
    {
        if (($model = Location::find()->editable()->andWhere(['id'=>$id])->one()) !== null) {
            return $model;
        } else {
            throw new NotFoundHttpException(Yii::t('app', 'Strona nie istnieje.'));
        }
    }

    public function actionList($q=null)
    {
        \Yii::$app->response->format = \yii\web\Response::FORMAT_JSON;
        $out = ['results' => ['id' => '', 'text' => '']];
//        if (!is_null($q)) {
        $data = Location::getList($q);
        $out['results'] = [];
        foreach ($data as $key=>$value)
        {
            $out['results'][] = [
                'id' => $key,
                'text' => $value,
            ];
        }

        return $out;
    }


    public function actionSendMail($id)
    {
        $model = new \backend\models\SendErrorMail();
        $location = $this->findModel($id);
        $model->location_id = $id;
        if ($model->load(Yii::$app->request->post()) && $model->validate()){
            $user = Yii::$app->user->identity;
            $mail = \Yii::$app->mailer->compose('@app/views/location/mail', [
                'model' =>  $model,
                'location' => $location
            ])
            ->setFrom([Yii::$app->params['mailingEmail']=>$user->email])
            ->setTo([Yii::$app->params['errorEmail']])
            ->setSubject($model->subject);            
            if ($mail->send())
            {
                Yii::$app->session->setFlash('success', Yii::t('app', 'Email wysłany!'));
            } else {
                Yii::$app->session->setFlash('danger', Yii::t('app', 'Błąd!'));
            }

            return $this->redirect(['view', 'id'=>$id]);
        } 
        return $this->render('send-mail', [
            'model' => $model,
            'location'=>$location
        ]);
    }
}
