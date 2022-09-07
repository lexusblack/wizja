<?php

namespace backend\controllers;

use backend\actions\UploadAction;
use backend\models\CustomerForm;
use common\components\filters\AccessControl;
use common\models\Notification;
use Yii;
use common\models\Customer;
use common\models\CustomerSearch;
use backend\components\Controller;
use yii\helpers\Json;
use yii\helpers\Url;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;
use yii\web\Response;
use common\components\GusApi\GusApi;
use common\components\GusApi\RegonConstantsInterface;
use common\components\GusApi\Exception\InvalidUserKeyException;
use common\components\GusApi\ReportTypes;
use common\components\GusApi\ReportTypeMapper;
use yii\web\UploadedFile;
/**
 * CustomerController implements the CRUD actions for Customer model.
 */
class CustomerController extends Controller
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
                    'actions' => ['index', 'upload', 'add-note', 'show-notes', 'tabs-data'],
                    'roles' => ['clientClients'],
                ],
                [
                    'allow' => true,
                    'actions' => ['create', 'gus'],
                    'roles' => ['clientClientsAdd'],

                ],
                [
                    'allow' => true,
                    'actions' => ['view'],
                    'roles' => ['clientClientsSee'],
                ],
                [
                    'allow' => true,
                    'actions' => ['delete'],
                    'roles' => ['clientClientsDelete'],
                ],
                [
                    'allow' => true,
                    'actions' => ['update'],
                    'roles' => ['clientClientsEdit'],
                ],
                [
                    'allow' => true,
                    'actions' => ['import'],
                    'roles' => ['clientClientsEdit'],
                ],
                [
                    'allow' => true,
                    'actions' => ['list'],
                    'roles' => ['@']
                ]
            ]
        ];

        return $behaviors;
    }

    public function actionTabsData($tab, $id)
    {
        $model = $this->findModel($id);
        if ($tab == "tab-project")
                $html = $this->renderPartial('_tabProjects', ['model' => $model]);
        if ($tab == "tab-offers")
                $html = $this->renderPartial('_tabOffers', ['model' => $model]);
        if ($tab == "tab-notes")
                $html = $this->renderPartial('_tabNotes', ['model' => $model]);
        if ($tab == "tab-meetings")
                $html = $this->renderPartial('_tabMeetings', ['model' => $model]);
        return Json::encode($html);
    }

    public function actions()
    {
        return [
            'upload'=> [
                'class'=>\backend\actions\UploadAction::className(),
                'upload'=>'/logo'

            ]
        ];
    }

    public function actionShowNotes($id)
    {
        $notes = \common\models\CustomerNote::find()->where(['customer_id'=>$id])->all();
        return $this->renderAjax('show-notes', [
            'notes'=>$notes
        ]);
    }

    public function actionAddNote($id)
    {
        $event = $this->findModel($id);
        $model = new \common\models\CustomerNote();
        $model->offer_id = $id;
        $model->customer_id = $id;
        $model->user_id = Yii::$app->user->identity->id;
        $model->datetime = date("Y-m-d H:i:s");
        if ($model->load(Yii::$app->request->post())  && $model->save()) {
            return true;
        }else{
            return $this->renderAjax('_customer_note_form', [
            'model'=>$model,
            'ajax'=>true
        ]);
        }

    }

    /**
     * Lists all Customer models.
     * @return mixed
     */
    public function actionIndex()
    {
        $searchModel = new CustomerSearch();
        $params = Yii::$app->request->queryParams;
        if (count($params)<1) {
          $params = Yii::$app->session['customerparams'];
          } else {
            Yii::$app->session['customerparams'] = $params;
        }
        $dataProvider = $searchModel->search($params);
        //$dataProvider = $searchModel->search(Yii::$app->request->queryParams);
        $dataProvider->query->andWhere(['active' => 1]);
        return $this->render('index', [
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
        ]);
    }

    /**
     * Displays a single Customer model.
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
     * Creates a new Customer model.
     * If creation is successful, the browser will be redirected to the 'view' page.
     * @return mixed
     */
    public function actionCreate()
    {
        $model = new Customer();
        $model->customer = 1;
        $model->supplier = 1;
        if ($model->load(Yii::$app->request->post()))
        {
            if ($model->nip)
            {
                $customer = Customer::find()->where(['nip'=>$model->nip])->one();
                if ($customer)
                {
                    $customer->load(Yii::$app->request->post());
                    $model = $customer;
                    $model->active = 1;
                }               
            }

            if ($model->save())
            {
                $model->linkObjects();
                Notification::sendCustomerNotification($model, Notification::CREATE_NEW_CUSTOMER, $model);
                if (Yii::$app->request->isAjax)
                {
                    Yii::$app->response->format = Response::FORMAT_JSON;
                    return $model->attributes;
                }
                Yii::$app->session->setFlash('success', Yii::t('app', 'Zapisano!'));
                return $this->redirect(['view', 'id' => $model->id]);                
            }else{
                echo var_dump($model->getErrors());
            }

            
        }
        else
        {
            return $this->render('create', [
                'model' => $model,
            ]);
        }
    }

    /**
     * Updates an existing Customer model.
     * If update is successful, the browser will be redirected to the 'view' page.
     * @param integer $id
     * @return mixed
     */
    public function actionUpdate($id)
    {
        $model = $this->findModel($id);
        $model->loadLinkedObjects();
        if ($model->load(Yii::$app->request->post()) && $model->save())
        {
            $model->linkObjects();
            Yii::$app->session->setFlash('success', Yii::t('app', 'Zapisano!'));
            return $this->redirect(['view', 'id' => $model->id]);
        }
        else
        {
            return $this->render('update', [
                'model' => $model,
            ]);
        }
    }

    /**
     * Deletes an existing Customer model.
     * If deletion is successful, the browser will be redirected to the 'index' page.
     * @param integer $id
     * @return mixed
     */
    public function actionDelete($id)
    {
        $this->enableCsrfValidation = false;
        $model = $this->findModel($id);
        $model->active = 0;
        $model->save();
        \common\models\Note::createNote(4, 'customerDeleted', $model, $model->id);
        return $this->redirect(['index']);
    }

    public function actionImport()
    {
        $modelForm = new CustomerForm;

        if (Yii::$app->request->isPost)
        {
            $modelForm->filename = UploadedFile::getInstance($modelForm, 'filename');
            if ($modelForm->upload()) {
            $fileName = Yii::getAlias('@uploadroot/xls/'.$modelForm->filename);
            $data = \moonland\phpexcel\Excel::widget([
            'mode' => 'import', 
            'fileName' => $fileName, 
            'setFirstRecordAsKeys' => true, // if you want to set the keys of record column with first record, if it not set, the header with use the alphabet column on excel. 
            'setIndexSheetByName' => true, // set this if your excel data with multiple worksheet, the index of array will be set with the sheet name. If this not set, the index will use numeric. 
            'getOnlySheet' => 'sheet1', // you can set this property if you want to get the specified sheet from the excel data with multiple worksheet.
            ]);
            $models = [];
            $modelsNot = [];
            foreach ($data as $c)
            {
                if ((isset($c['Nazwa']))&&($c['Nazwa']!=""))
                {
                    $nip = strval($c['NIP']);
                    $nip = str_replace("-", "", $nip);
                    $nip = str_replace(" ", "", $nip);
                    $modelFind = false;
                    if ($nip!="")
                        $modelFind = Customer::find()->where(['nip'=>$nip, 'active'=>1])->one();
                    if (!$modelFind)
                        $model = new Customer();
                    else
                        $model = $modelFind;
                    $model->company = $c['Nazwa'];
                    $model->name = $c['Nazwa'];
                    $model->address = $c['Adres'];
                    $model->city = $c['Miasto'];
                    $model->zip = strval($c['Kod']);
                    $model->phone = strval($c['Telefon']);
                    $model->email = $c['Mail'];
                    $model->nip = $nip;
                    $model->customer = 1;
                    $model->supplier = 1;
                    $model->bank_account = strval($c['Numer konta']);
                    if ($model->save())
                    {
                        $models[] = $model->name;
                    }else{
                        $modelsNot[]=$model->name;
                    }

                }else{
                        $modelsNot[]=$c['Nazwa'];
                    }
            }
        }
        return $this->render('import-report', [
                'models' => $models,
                'modelsNot'=>$modelsNot
            ]);           
        }else{
            return $this->render('import', [
                'model' => $modelForm,
            ]);
        }


    }

    /**
     * Finds the Customer model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     * @param integer $id
     * @return Customer the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function findModel($id)
    {
        if (($model = Customer::findOne($id)) !== null) {
            return $model;
        } else {
            throw new NotFoundHttpException(Yii::t('app', 'Strona nie istnieje.'));
        }
    }

    public function actionGus($nip)
    {



        $key = 'cbcfa5f815f644fb88d0'; // <--- your user key / twój klucz użytkownika

        $gus = new GusApi(
            $key
        );

        if ($gus->serviceStatus() === RegonConstantsInterface::SERVICE_AVAILABLE) {

            try {

                if (!isset($_SESSION['sid']) || !$gus->isLogged($_SESSION['sid'])) {
                    $_SESSION['sid'] = $gus->login();
                }

                if (isset($nip)) {
                    $nip = str_replace("-", "", $nip);
                    $nip = str_replace(" ", "", $nip);
                    $mapper = new ReportTypeMapper();
                    try {
                        $gusReport = $gus->getByNip($_SESSION['sid'], $nip);
                        $reportType = $mapper->getReportType($gusReport[0]);
                        $r = $gus->getFullReport(
                            $_SESSION['sid'],
                            $gusReport[0],
                            $reportType);
                        $gusArray['name'] = $gusReport[0]->getName();
                        $gusArray['regon'] = $gusReport[0]->getRegon();
                        $gusArray['zip'] = $gusReport[0]->getZipCode();
                        $gusArray['city'] = $gusReport[0]->getCity();
                        $gusArray['address'] = $gusReport[0]->getStreet();
                        $r=$r->dane;
                        if ($r->praw_adSiedzNumerNieruchomosci!="")
                        {
                            $gusArray['address'].=" ".$r->praw_adSiedzNumerNieruchomosci;
                        }
                        if (($r->praw_adSiedzNumerLokalu!="")&&($r->praw_adSiedzNumerLokalu!=0))
                        {
                            $gusArray['address'].="/".$r->praw_adSiedzNumerLokalu;
                        }
                        if ($r->fiz_adSiedzNumerNieruchomosci!="")
                        {
                            $gusArray['address'].=" ".$r->fiz_adSiedzNumerNieruchomosci;
                        }
                        if (($r->fiz_adSiedzNumerLokalu!="")&&($r->fiz_adSiedzNumerLokalu!=0))
                        {
                            $gusArray['address'].="/".$r->fiz_adSiedzNumerLokalu;
                        }                        
                        $response['gus'] = $gusArray;
                        $response['error'] = 'ok';
                        echo json_encode($response);
                    } catch (\common\components\GusApi\Exception\NotFoundException $e) {
                        $response['error'] = Yii::t('app', 'Nie znaleziono firmy o podanym numerze NIP.');
                        echo json_encode($response);

                    }
                }

            } catch (InvalidUserKeyException $e) {
                        $response['error'] = Yii::t('app', 'Wystąpił błąd!');
                        echo json_encode($response);
            }
        } else if ($gus->serviceStatus() === RegonConstantsInterface::SERVICE_UNAVAILABLE) {
                        $response['error'] = Yii::t('app', 'Wystąpił błąd!');
                        echo json_encode($response);
        } else {
                        $response['error'] = Yii::t('app', 'Wystąpił błąd!');
                        echo json_encode($response);
        }
        exit;
    }

    public function actionList($q=null, $customer=null, $supplier=null)
    {
        $attrs = [
            'supplier'=>$supplier,
            'customer'=>$customer,
        ];
        \Yii::$app->response->format = \yii\web\Response::FORMAT_JSON;
        $out = ['results' => ['id' => '', 'text' => '']];
//        if (!is_null($q)) {
        $data = Customer::getList($q, $attrs);
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
