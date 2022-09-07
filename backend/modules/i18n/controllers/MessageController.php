<?php

namespace backend\modules\i18n\controllers;

use common\components\filters\AccessControl;
use common\helpers\ArrayHelper;
use common\models\Language;
use common\actions\EditableColumnAction;
use Yii;
use common\models\Message;
use common\models\MessageSearch;
use backend\components\Controller;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;

/**
 * MessageController implements the CRUD actions for Message model.
 */
class MessageController extends Controller
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
                        'actions' => ['index'],
                        'roles' => ['settingsLanguageTranslate'],
                    ],
                    [
                        'allow' => true,
                        'actions' => ['edit'],
                        'roles' => ['settingsLanguageTranslateSave'],
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

    public function init()
    {
        $this->layout = Controller::LAYOUT_PANEL;
        Yii::$app->view->params['active_tab'] = 9;
        parent::init();
    }

    public function actions()
    {
        return ArrayHelper::merge(parent::actions(), [
            'edit' => [                                       // identifier for your editable column action
                'class' => EditableColumnAction::className(),     // action class name
                'modelClass' => Message::className(),                // the model for the record being edited
                'outputValue' => function ($model, $attribute, $key, $index) {
                    return $model->$attribute;      // return any custom output value if desired
                },
                'outputMessage' => function($model, $attribute, $key, $index) {
                    return '';                                  // any custom error to return after model save
                },
                'showModelErrors' => true,                        // show model validation errors after save
                'errorOptions' => ['header' => ''],           // error summary HTML options
                // 'postOnly' => true,
                // 'ajaxOnly' => true,
                // 'findModel' => function($id, $action) {},
                // 'checkAccess' => function($action, $model) { }
            ]
        ]);
    }

    /**
     * Lists all Message models.
     * @return mixed
     */
    public function actionIndex()
    {
        $searchModel = new MessageSearch();

        $searchModel->language = key(Language::getTranslationList());
        $dataProvider = $searchModel->search(Yii::$app->request->queryParams);

        return $this->render('index', [
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
        ]);
    }

    /**
     * Displays a single Message model.
     * @param integer $id
     * @param string $language
     * @return mixed
     */
    public function actionView($id, $language)
    {
        return $this->render('view', [
            'model' => $this->findModel($id, $language),
        ]);
    }

    /**
     * Creates a new Message model.
     * If creation is successful, the browser will be redirected to the 'view' page.
     * @return mixed
     */
    public function actionCreate()
    {
        $model = new Message();

        if ($model->load(Yii::$app->request->post()) && $model->save())
        {
            Yii::$app->session->setFlash('success',  Yii::t('app', 'Zapisano!'));
            return $this->redirect(['view', 'id' => $model->id, 'language' => $model->language]);
        }
        else
        {
            return $this->render('create', [
                'model' => $model,
            ]);
        }
    }

    /**
     * Updates an existing Message model.
     * If update is successful, the browser will be redirected to the 'view' page.
     * @param integer $id
     * @param string $language
     * @return mixed
     */
    public function actionUpdate($id, $language)
    {
        $model = $this->findModel($id, $language);

        if ($model->load(Yii::$app->request->post()) && $model->save())
        {
            Yii::$app->session->setFlash('success',  Yii::t('app', 'Zapisano!'));
            return $this->redirect(['view', 'id' => $model->id, 'language' => $model->language]);
        }
        else
        {
            return $this->render('update', [
                'model' => $model,
            ]);
        }
    }

    /**
     * Deletes an existing Message model.
     * If deletion is successful, the browser will be redirected to the 'index' page.
     * @param integer $id
     * @param string $language
     * @return mixed
     */
    public function actionDelete($id, $language)
    {
        $this->findModel($id, $language)->delete();

        return $this->redirect(['index']);
    }

    /**
     * Finds the Message model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     * @param integer $id
     * @param string $language
     * @return Message the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function findModel($id, $language)
    {
        if (($model = Message::findOne(['id' => $id, 'language' => $language])) !== null) {
            return $model;
        } else {
            throw new NotFoundHttpException( Yii::t('app', 'Strona nie istnieje.'));
        }
    }
}
