<?php

namespace backend\modules\settlement\controllers;

use common\components\filters\AccessControl;
use common\helpers\ArrayHelper;
use common\helpers\Url;
use common\models\User;
use DateTime;
use Yii;
use common\models\SettlementUser;
use backend\models\ReportForm;
use backend\modules\settlement\models\SettlementUserSearch;
use yii\data\ArrayDataProvider;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;
use yii\web\UnauthorizedHttpException;
use yii\web\Response;

/**
 * UserController implements the CRUD actions for SettlementUser model.
 */
class UserController extends Controller
{
    public $enableCsrfValidation = false;
    public $layout = '@backend/themes/e4e/layouts/main-panel';
    /**
     * @inheritdoc
     */
    public function behaviors()
    {
        return [
            'access' => [
                'class'=>AccessControl::className(),
                'rules' => [
                    [
                        'allow'=>true,
                        'actions' => ['index', 'add-payment', 'delete-payment'],
                        'roles' => ['usersPayments'],
                    ],
                    [
                        'allow'=>true,
                        'actions' => ['set-status'],
                        'roles' => ['usersPaymentsChangeStatus'],
                    ],
                    [
                        'allow'=>true,
                        'actions' => ['show'],
                        'roles' => ['menuSettlement'],
                    ]
                ],
            ],
        ];
    }

    public function actionAddPayment($user_id, $year, $month, $id=null)
    {
        if ($id)
        {
            $model = \common\models\UserPayment::findOne($id);
        }else{
            $model = new \common\models\UserPayment(['user_id'=>$user_id, 'month'=>$month, 'year'=>$year, 'creator_id'=>Yii::$app->user->id]);

        }
        if ($model->load(Yii::$app->request->post())  && $model->save()) {
            $payments = \common\models\UserPayment::find()->where(['user_id'=>$user_id, 'month'=>$month, 'year'=>$year])->all();
            $sum = 0;
            foreach ($payments as $p)
            {
                $sum +=$p->amount;
            }
            Yii::$app->response->format = Response::FORMAT_JSON;
            return ['sum'=>Yii::$app->formatter->asCurrency($sum), 'user_id'=>$user_id];
        }else{
            $payments = \common\models\UserPayment::find()->where(['user_id'=>$user_id, 'month'=>$month, 'year'=>$year])->all();
            return $this->renderAjax('add-payment', [
                'model' => $model,
                'payments' => $payments
            ]);
        }
        
    }

    public function actionDeletePayment($id)
    {
        $model = \common\models\UserPayment::findOne($id);
        $payments = \common\models\UserPayment::find()->where(['user_id'=>$model->user_id, 'month'=>$model->month, 'year'=>$model->year])->andWhere(["<>", 'id', $id])->all();
        $model->delete();
        $sum = 0;
            foreach ($payments as $p)
            {
                $sum +=$p->amount;
            }
            Yii::$app->response->format = Response::FORMAT_JSON;
            return ['sum'=>Yii::$app->formatter->asCurrency($sum), 'user_id'=>$model->user_id];

    }

    /**
     * Finds the SettlementUser model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     * @param integer $id
     * @return SettlementUser the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function findModel($id)
    {
        if (($model = SettlementUser::findOne($id)) !== null) {
            return $model;
        } else {
            throw new NotFoundHttpException(Yii::t('app', 'Strona nie istnieje.'));
        }
    }

    public function actionIndex($year=null, $month=null, $tab = 'event')
    {
        Url::remember();
        $report = new ReportForm();

        $request = Yii::$app->request;

        $formatter = Yii::$app->formatter;

        if ($year== null) {
            $year = date('Y');
        }

        if ($month == null) {
            $month = date('m');
        }

        $date = new \DateTime($year.'-'.$month.'-01');

        //SettlementUser::storeAll(null, $year, $month);

        $data = [
            'dateString'=> $date->format('Y-m'),
        ];
        $report->date_from = $date->format('Y-m-d');
        
        $searchModel = new SettlementUserSearch();
        $params = Yii::$app->request->queryParams;

        $params = ArrayHelper::merge($params, [$searchModel->formName()=>[
            'month'=>$month,
            'year'=>$year,
        ]]);
        
        $dataProvider = $searchModel->search($params);
        $dataProvider->pagination = false;
        
        //$dataProvider = null;
        $interval = new \DateInterval('P1M');
        $date->add($interval);
        $data['nextUrl'] = Url::to(['index', 'month'=>$date->format('n'), 'year'=>$date->format('Y')]);
        $report->date_to = $date->format('Y-m-d');
        $date->sub($interval);
        $date->sub($interval);
        $data['prevUrl'] = Url::to(['index', 'month'=>$date->format('n'), 'year'=>$date->format('Y')]);

        $summaryAll = [
            'salary'=>0,
            'provision'=>0,
            'provision_non'=>0,
            'roleAddons'=>0,
            'addons'=>0,
            'sum'=>0,
            'hours'=>0,
            'allowances'=>0,
            'sum_brutto'=>0,
            'sum_vat'=>0
        ];
        $paymentSum = [];
        $arr = [];
        $userSum = [];
        foreach ($dataProvider->getModels() as $model) {
            if (isset($model->user)){
            if (!isset($userSum[$model->user_id])){
                $userSum[$model->user_id]['user'] = $model->user;
                $userSum[$model->user_id]['sum'] = 0;
                $userSum[$model->user_id]['addons'] = 0;
                $userSum[$model->user_id]['salary']= 0;
                $userSum[$model->user_id]['allowances']  = 0;
                $userSum[$model->user_id]['roleAddons'] = 0;
                $userSum[$model->user_id]['hours'] = 0;
                $userSum[$model->user_id]['sum_brutto'] = 0;
                $userSum[$model->user_id]['sum_vat'] = 0;
                $userSum[$model->user_id]['provision'] = 0;
                $userSum[$model->user_id]['provision_non'] = 0;
                $userSum[$model->user_id]['status'] = $model->status;
                $userSum[$model->user_id]['id'] = $model->id;
            }
            /* @var $model SettlementUser */
            if (($model->working_hours_data!='a:0:{}')||($model->addon_data!='a:0:{}'))
            {
                $s = $model->event->getWorkingTimeSummary($model->user_id, false, $year, $month);
                $summaryAll = ArrayHelper::sumArrays($s, $summaryAll);
                    $userSum[$model->user_id]['sum'] += $s['sum'];
                    $userSum[$model->user_id]['provision'] += $s['provision'];
                    $userSum[$model->user_id]['provision_non'] += $s['provision_non'];
                    $userSum[$model->user_id]['addons'] += $s['addons'];
                    $userSum[$model->user_id]['salary']+= $s['salary'];
                    $userSum[$model->user_id]['allowances']  += $s['allowances'];
                    $userSum[$model->user_id]['roleAddons'] += $s['roleAddons'];
                    $userSum[$model->user_id]['hours'] += $s['hours'];
                if (isset($paymentSum[$model->user_id])) {
                    $paymentSum[$model->user_id] += $s['sum'];
                }
                else {
                    $paymentSum[$model->user_id] = $s['sum'];
                }               
            }

            if (isset($arr[$model->event->getTimeStart()])) {
	            $arr[ $model->event->getTimeStart() . $this->getUniqueId() ] = $model;
            }
            else {
	            $arr[ $model->event->getTimeStart() ] = $model;
            }
        }
        }

        $pms = User::find()->where(['role'=>30])->andWhere(['active'=>1])->all();
        foreach ($pms as $pm)
        {
            if (!isset($userSum[$pm->id])){
                $userSum[$pm->id]['user'] = $pm;
                $userSum[$pm->id]['name'] = $pm->last_name;
                $userSum[$pm->id]['sum'] = 0;
                $userSum[$pm->id]['addons'] = 0;
                $userSum[$pm->id]['salary']= 0;
                $userSum[$pm->id]['allowances']  = 0;
                $userSum[$pm->id]['roleAddons'] = 0;
                $userSum[$pm->id]['hours'] = 0;
                $userSum[$pm->id]['sum_brutto'] = 0;
                $userSum[$pm->id]['sum_vat'] = 0;
                $userSum[$pm->id]['status'] = 0;
                $userSum[$pm->id]['provision'] = 0;
                $userSum[$pm->id]['provision_non'] = 0;
                $userSum[$pm->id]['id'] = false;
            }
            //sprawdzamy czy już nie policzona
            $provision = $pm->getEventProvisions($year, $month);
            $provision_non = $pm->getEventProvisionsNon($year, $month);
            $userSum[$pm->id]['sum']+=$provision;
            $userSum[$pm->id]['provision']+=$provision;
            $userSum[$pm->id]['provision_non']+=$provision_non;
            $summaryAll['provision_non'] += $provision_non;
            $summaryAll['provision']+=$provision;
            $summaryAll['sum']+=$provision;
            if ($pm->rate_type==720)
            {
                $userSum[$pm->id]['sum']+=$pm->rate_amount;
                $userSum[$pm->id]['salary']+=$pm->rate_amount;
                $summaryAll['salary']+=$pm->rate_amount;
            $summaryAll['sum']+=$pm->rate_amount;
            }           
        }

        foreach ($userSum as $k=>$v) {
            $user = $v['user'];
            $brutto = ($v['sum']+5*$user->nfz_rate/36)/(1-$user->tax_rate/100)+$user->zus_rate;
            $brutto2 = $v['sum']+$user->nfz_rate+$user->zus_rate;
                        if ($brutto>$brutto2)
                        {
                            $podatek = $brutto-$brutto2;
                        }else{
                            $podatek = 0;
                            $brutto = $brutto2;
                        }
            $userSum[$k]['sum_brutto'] = $brutto;
            $userSum[$k]['sum_vat'] = $brutto*(1+$user->vat_rate/100);
            $summaryAll['sum_brutto']+=$brutto;
            $summaryAll['sum_vat']+=$userSum[$k]['sum_vat'];
            $userSum[$k]['name'] = $userSum[$k]['user']->last_name;

        }
        foreach ($summaryAll as $k=>$v)
        {
            if ($k!="hours")
                $summaryAll[$k] = $v;
        }

        $data['summary'] = $summaryAll;

        $dropdownItems[0] = "---";
        $selectedItem = null;
        $now = new \DateTime();
        for ($i = 0; $i < 12; $i++) {
            if ($year == $now->format('Y') && $month == $now->format('m')) {
                $selectedItem = $now->format("Y-m");
            }
            $dropdownItems[$now->format("Y-m")] = $now->format("Y-m");
            $now->sub(new \DateInterval("P1M"));
        }


        ksort($arr);
	    $provider = new ArrayDataProvider([
		    'allModels' => $arr,
		    'sort' => [
			    'attributes' => ['id', 'username', 'email'],
		    ],
		    'pagination' => false
	    ]);

        usort($userSum, function($a, $b) {return strcmp($a['name'], $b['name']);});
        $userNormal = User::find()->where(['active'=>1])->andWhere(['rate_type'=>720])->all();

        return $this->render('index', [
        	'provider' => $provider,
            'dropdownItems' => $dropdownItems,
            'selectedItem' => $selectedItem,
            'data'=>$data,
            'searchModel'=>$searchModel,
            'dataProvider'=>$dataProvider,
            'tab' => $tab,
            'year' => $year,
            'month' => $month,
            'paymentSum' => $paymentSum,
            'userSum' =>$userSum,
            'userNormal'=>$userNormal,
            'report' =>$report
        ]);

    }


    public function actionSetStatus($id, $status=0)
    {
        $model = SettlementUser::findOne($id);
        if ($model===null)
        {
            throw new NotFoundHttpException();
        }
        SettlementUser::setSettled($model->user_id, $model->year, $model->month, $status);
        Yii::$app->session->setFlash('success', Yii::t('app', 'Zapisano'));
        return $this->redirect(Url::previous());
    }

    public function actionShow($userId=null, $year=null, $month=null, $ajax = false) {
        Url::remember();
        if ($userId==null) {
            $userId = Yii::$app->user->id;
        }

        if ($year== null) {
            $year = date('Y');
        }

        if ($month == null) {
            $month = date('m');
        }

        $formatter = Yii::$app->formatter;
        $date = new DateTime($year.'-'.$month.'-01');

        $user = User::findOne($userId);
        if ($user === null) {
            throw new NotFoundHttpException(Yii::t('app', 'Nie znaleziono użytkownika'));
        }

        if (Yii::$app->request->isPost) {
            if (Yii::$app->user->id != $user->id) {
                throw new UnauthorizedHttpException(Yii::t('app', 'Błędne żądanie'));
            }
            if (SettlementUser::setSettled($user->id, $year, $month, Yii::$app->request->post('status')) && Yii::$app->user->can('menuSettlementSave')) {
                Yii::$app->session->setFlash('success', Yii::t('app', 'Zapisano!'));
            }
            else {
                Yii::$app->session->setFlash('error', Yii::t('app', 'Błąd!'));
            }
            return $this->refresh();
        }

        SettlementUser::storeAll($userId, $year, $month);

        $data = [
            'rate'=>$formatter->asCurrency($user->rate_amount),
            'rateType'=>$user->getRateName(),
            'dateString'=> $date->format('Y-m'),
        ];

        $searchModel = new SettlementUserSearch();
        $params = Yii::$app->request->queryParams;
        if (isset($params['id']))
            unset($params['id']);
        $params[$searchModel->formName()]=array_merge($params, [
            'user_id' => $userId,
            'month'=>$month,
            'year'=>$year,
        ]);

        $dataProvider = $searchModel->search($params);
        $dataProvider->query->innerJoinWith('event');
        $dataProvider->setSort([
        'attributes' => [
            'event_start' => [
                            'asc' =>    [  'event.event_start' => SORT_ASC ],
                            'desc' =>   [ 'event.event_start' => SORT_DESC ],
                            'label' => 'start'
            ],                 
        ],
        'defaultOrder' => ['event_start' => SORT_ASC],
   ]);
        //$dataProvider->query->andWhere(['<>', 'working_hours_data', 'a:0:{}']);

        $dataProvider->pagination = false;

        $interval = new \DateInterval('P1M');
        $date->add($interval);
        $data['nextUrl'] = Url::to(['show', 'month'=>$date->format('n'), 'year'=>$date->format('Y'), 'userId'=>$user->id]);

        $date->sub($interval);
        $date->sub($interval);
        $data['prevUrl'] = Url::to(['show', 'month'=>$date->format('n'), 'year'=>$date->format('Y'), 'userId'=>$user->id]);

        $summaryAll = [
            'salary'=>0,
            'roleAddons'=>0,
            'addons'=>0,
            'sum'=>0,
            'allowances'=>0,
            'hours'=>0,
            'provision'=>0,
            'provision_non'=>0

        ];
        $status = 0;
        foreach ($dataProvider->getModels() as $model)
        {
            $status+= $model->status;
            /* @var $model SettlementUser */
            $s = $model->event->getWorkingTimeSummary($user->id, false, $year, $month);
            $summaryAll['sum'] += $s['sum'];
            $summaryAll['addons'] += $s['addons'];
            $summaryAll['salary']+= $s['salary'];
            $summaryAll['allowances']  += $s['allowances'];
            $summaryAll['roleAddons'] += $s['roleAddons'];
            $summaryAll['hours'] += $s['hours'];
            $summaryAll['provision'] += $s['provision'];
            $summaryAll['provision_non'] += $s['provision_non'];
            //echo $s['sum']." ".$model->event->name."<br/>";
            //$summaryAll = ArrayHelper::sumArrays($s, $summaryAll);

        }
        foreach ($summaryAll as $k=>$v)
        {
            $summaryAll[$k] = $formatter->asCurrency($v);
            $summaryAll2[$k] = $v; 
        }

        $data['summary'] = $summaryAll;
        $data['summary2'] = $summaryAll2;
        $data['status'] = $status > 0 ? SettlementUser::STATUS_SETTLED : SettlementUser::STATUS_UNSETTLED;


        $dropdownItems[0] = "---";
        $selectedItem = null;
        $now = new \DateTime();
        for ($i = 0; $i < 12; $i++) {
            if ($year == $now->format('Y') && $month == $now->format('m')) {
                $selectedItem = $now->format("Y-m");
            }
            $dropdownItems[$now->format("Y-m")] = $now->format("Y-m");
            $now->sub(new \DateInterval("P1M"));
        }

	    if ($ajax == 1) {
		    return $this->renderAjax('show', [
			    'dropdownItems' => $dropdownItems,
			    'selectedItem' => $selectedItem,
			    'data'=>$data,
			    'user'=>$user,
			    'searchModel'=>$searchModel,
			    'dataProvider'=>$dataProvider,
			    'ajax' => $ajax,
		    ]);
	    }
        if ($month<10)
            $month = "0".$month;
        return $this->render('show', [
            'dropdownItems' => $dropdownItems,
            'selectedItem' => $selectedItem,
            'data'=>$data,
            'user'=>$user,
            'searchModel'=>$searchModel,
            'dataProvider'=>$dataProvider,
	        'ajax' => $ajax,
            'year' => $year,
            'month' => $month
        ]);

    }
}
