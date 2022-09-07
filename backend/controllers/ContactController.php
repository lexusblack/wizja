<?php

namespace backend\controllers;

use common\components\filters\AccessControl;
use Yii;
use common\models\Contact;
use common\models\ContactSearch;
use backend\components\Controller;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;
use yii\web\Response;
use yii\helpers\Url;
/**
 * ContactController implements the CRUD actions for Contact model.
 */
class ContactController extends Controller
{
    public $enableCsrfValidation = false;
    public function behaviors()
    {
        return [
            'access' => [
                'class' => AccessControl::className(),
                'rules' => [
                    [
                        'allow'=>true,
                        'actions' => ['index', 'upload'],
                        'roles' => ['clientContacts'],
                    ],
                    [
                        'allow' => true,
                        'actions' => ['create'],
                        'roles' => ['clientContactsAdd'],

                    ],
                    [
                        'allow' => true,
                        'actions' => ['view'],
                        'roles' => ['clientContactsSee'],
                    ],
                    [
                        'allow' => true,
                        'actions' => ['delete'],
                        'roles' => ['clientContactsDelete'],
                    ],
                    [
                        'allow' => true,
                        'actions' => ['update'],
                        'roles' => ['clientContactsEdit'],
                    ],
                    [
                        'allow' => true,
                        'actions' => ['list'],
                        'roles' => ['@'],
                    ],
                ],
            ],
            'verbs' => [
                'class' => VerbFilter::className(),
                'actions' => [
                    'delete' => ['POST'],
                ],
            ],
        ];
    }

    public function actions()
    {
        $actions = [

            'upload'=> [
                'class'=>\backend\actions\UploadAction::className(),
                'upload'=>'/contact'

            ]
        ];

        return array_merge(parent::actions(), $actions);
    }

    /**
     * Lists all Contact models.
     * @return mixed
     */
    public function actionIndex()
    {
        $searchModel = new ContactSearch();
        $dataProvider = $searchModel->search(Yii::$app->request->queryParams);
        Url::remember();
        return $this->render('index', [
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
        ]);
    }

    /**
     * Displays a single Contact model.
     * @param integer $id
     * @return mixed
     */
    public function actionView($id)
    {
        Url::remember();
        return $this->render('view', [
            'model' => $this->findModel($id),
        ]);
    }

    /**
     * Creates a new Contact model.
     * If creation is successful, the browser will be redirected to the 'view' page.
     * @return mixed
     */
    public function actionCreate($customerId=null)
    {
        $model = new Contact();
        $model->customer_id = $customerId;

        if ($model->load(Yii::$app->request->post()) && $model->save())
        {
            if (Yii::$app->request->isAjax)
            {
                Yii::$app->response->format = Response::FORMAT_JSON;
                return $model->attributes;
            }

           return $this->redirect(['/customer/view', 'id'=>$model->customer_id]);
        }
        else
        {
            return $this->render('create', [
                'model' => $model,
            ]);
        }
    }

    /**
     * Updates an existing Contact model.
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

    /**
     * Deletes an existing Contact model.
     * If deletion is successful, the browser will be redirected to the 'index' page.
     * @param integer $id
     * @return mixed
     */
    public function actionDelete($id)
    {
        $model = $this->findModel($id);
        if ((isset($model->events))||(isset($model->rents))||(isset($model->offers))||(isset($model->meetings)))
        {
            $model->status = 2;
            $model->save();
            $model->customer->createLog('contact_delete', $model->id);
        }else{
            $model->delete();
        }
        return $this->goBack();
    }

    /**
     * Finds the Contact model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     * @param integer $id
     * @return Contact the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function findModel($id)
    {
        if (($model = Contact::findOne($id)) !== null) {
            return $model;
        } else {
            throw new NotFoundHttpException(Yii::t('app', 'Strona nie istnieje.'));
        }
    }

    public function actionList($id=null, $q=null)
    {
        \Yii::$app->response->format = \yii\web\Response::FORMAT_JSON;
        $out = ['results' => ['id' => '', 'text' => '']];
//        if (!is_null($q)) {
        $data = Contact::getList($id, $q);
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
}
