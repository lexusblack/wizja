<?php

namespace app\modules\customerDiscount\controllers;

use common\components\filters\AccessControl;
use Yii;
use common\models\CustomerDiscount;
use common\models\Customer;
use common\models\CustomerDiscountSearch;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;

/**
 * DefaultController implements the CRUD actions for CustomerDiscount model.
 */
class DefaultController extends Controller
{
    /**
     * @inheritdoc
     */

    public $layout = '@backend/themes/e4e/layouts/main-panel';

    public function behaviors()
    {
        return [
            'access' => [
                'class' => AccessControl::className(),
                'rules' => [
                    [
                        'allow'=>true,
                        'actions' => ['index'],
                        'roles' => ['clientDiscount'],
                    ],
                    [
                        'allow' => true,
                        'actions' => ['create', 'create-discount'],
                        'roles' => ['clientDiscountAdd'],

                    ],
                    [
                        'allow' => true,
                        'actions' => ['view'],
                        'roles' => ['clientDiscountView'],
                    ],
                    [
                        'allow' => true,
                        'actions' => ['delete'],
                        'roles' => ['clientDiscountDelete'],
                    ],
                    [
                        'allow' => true,
                        'actions' => ['update'],
                        'roles' => ['clientDiscountEdit'],
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

    /**
     * Lists all CustomerDiscount models.
     * @return mixed
     */
    public function actionIndex()
    {
        $searchModel = new CustomerDiscountSearch();
        $dataProvider = $searchModel->search(Yii::$app->request->queryParams);

        return $this->render('index', [
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
        ]);
    }

    /**
     * Displays a single CustomerDiscount model.
     * @param integer $id
     * @return mixed
     */
    public function actionView($id)
    {
        return $this->render('view', [
            'model' => $this->findModel($id),
        ]);
    }

    /**
     * Creates a new CustomerDiscount model.
     * If creation is successful, the browser will be redirected to the 'view' page.
     * @return mixed
     */


    public function actionCreateDiscount($customer_id)
    {
        $model = new CustomerDiscount();
        $customer = Customer::findOne($customer_id);
        $request = Yii::$app->request->post();
        if ($model->load($request) && isset($request['CustomerDiscount']['category_ids'] )) {

            $ids = explode(",", $request['CustomerDiscount']['category_ids']);
            foreach ($ids as $cat) {
                $modelx = clone $model;
                $modelx->save();
                $cdc = new \common\models\CustomerDiscountCategory();
                $cdc->customer_discount_id = $modelx->id;
                $cdc->category_id = $cat;
                $cdc->save();

                $cdc = new \common\models\CustomerDiscountCustomer();
                $cdc->customer_discount_id = $modelx->id;
                $cdc->customer_id = $customer_id;
                $cdc->save();
            }

            return $this->redirect(['/customer/view', 'id'=>$customer_id, '#'=>'tab-discount']);
        } else {
            return $this->render('create_discount', [
                'model' => $model,
                'customer'=>$customer
            ]);
        }
    }

    public function actionCreate()
    {
        $model = new CustomerDiscount();
        $discounts = [new \common\models\CustomerDiscountCategory()];
        $customers = [new \common\models\CustomerDiscountCustomer()];
        $request = Yii::$app->request->post();

        if ($model->load($request) && isset($request['CustomerDiscountCategory']) && $model->save()) {

        	foreach ($request['CustomerDiscountCategory'] as $key => $cat) {
        		$cdc = new \common\models\CustomerDiscountCategory();
        		$cdc->customer_discount_id = $model->id;
        		$cdc->category_id = $cat['category_id'];
        		$cdc->save();
        	}
            
            foreach ($request['CustomerDiscountCustomer'] as $key => $cus) {
                $cdc = new \common\models\CustomerDiscountCustomer();
                $cdc->customer_discount_id = $model->id;
                $cdc->customer_id = $cus['customer_id'];
                $cdc->save();
            }

            return $this->redirect(['index']);
        } else {
            return $this->render('create', [
                'model' => $model,
                'discounts' => $discounts,
                'customers' => $customers,
            ]);
        }
    }

    /**
     * Updates an existing CustomerDiscount model.
     * If update is successful, the browser will be redirected to the 'view' page.
     * @param integer $id
     * @return mixed
     */

    public function actionUpdate($id, $customer_id=null)
    {
        $model = $this->findModel($id);
        

        $request = Yii::$app->request->post();
        if ($model->load($request) && $model->save()) {
            if ($customer_id)
            {
                $customer = Customer::findOne($customer_id);
                \common\models\Note::createNote(4, 'customerDiscountChange', $customer, $customer->id);
                return $this->redirect(['/customer/view', 'id'=>$customer_id, '#'=>'tab-discount']);
            }else{
                return $this->redirect(['index']);
            }
            
        } else {
            if ($customer_id)
            {
                $customer = Customer::findOne($customer_id);
            }else{
                $customer = null;
            }
            return $this->render('update', [
                'model' => $model,
                'customer'=>$customer
            ]);
        }
    }


    /**
     * Deletes an existing CustomerDiscount model.
     * If deletion is successful, the browser will be redirected to the 'index' page.
     * @param integer $id
     * @return mixed
     */
    public function actionDelete($id, $customer_id=null)
    {
        $this->findModel($id)->delete();
        if ($customer_id){
            $customer = Customer::findOne($customer_id);

            return $this->redirect(['/customer/view', 'id'=>$customer_id, '#'=>'tab-discount']);
        }else{
            return $this->redirect(['index']);
        }
        
    }

    /**
     * Finds the CustomerDiscount model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     * @param integer $id
     * @return CustomerDiscount the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function findModel($id)
    {
        if (($model = CustomerDiscount::findOne($id)) !== null) {
            return $model;
        } else {
            throw new NotFoundHttpException(Yii::t('app', 'Strona nie istnieje.'));
        }
    }
}
