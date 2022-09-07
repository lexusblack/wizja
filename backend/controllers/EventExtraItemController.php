<?php

namespace backend\controllers;

use Yii;
use common\models\EventExtraItem;
use common\models\EventExpense;
use backend\modules\offers\models\OfferExtraItem;
use yii\data\ActiveDataProvider;
use backend\components\Controller;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;
use yii\web\Response;

/**
 * EventExtraItemController implements the CRUD actions for EventExtraItem model.
 */
class EventExtraItemController extends Controller
{
    public function behaviors()
    {
        return [
            'verbs' => [
                'class' => VerbFilter::className(),
                'actions' => [
                    'delete' => ['post'],
                ],
            ],
            'access' => [
                'class' => \yii\filters\AccessControl::className(),
                'rules' => [
                    [
                        'allow' => true,
                        'actions' => ['index', 'view', 'create', 'update', 'delete', 'assign'],
                        'roles' => ['@']
                    ],
                    [
                        'allow' => false
                    ]
                ]
            ]
        ];
    }

    /**
     * Lists all EventExtraItem models.
     * @return mixed
     */
    public function actionIndex()
    {
        $dataProvider = new ActiveDataProvider([
            'query' => EventExtraItem::find(),
        ]);

        return $this->render('index', [
            'dataProvider' => $dataProvider,
        ]);
    }

    /**
     * Displays a single EventExtraItem model.
     * @param integer $id
     * @return mixed
     */
    public function actionView($id)
    {
        $model = $this->findModel($id);
        return $this->render('view', [
            'model' => $this->findModel($id),
        ]);
    }

    /**
     * Creates a new EventExtraItem model.
     * If creation is successful, the browser will be redirected to the 'view' page.
     * @return mixed
     */
    public function actionCreate($event_id, $packlist_id)
    {
        $model = new EventExtraItem();
        $model->event_id = $event_id;
        if ($model->load(Yii::$app->request->post()) && $model->save()) {
            $model->createPacklist($packlist_id);
            return $this->redirect(['/event/view', 'id' => $model->event_id, '#'=>'tab-gear']);
        } else {
            return $this->render('create', [
                'model' => $model,
            ]);
        }
    }

    public function actionAssign($event_id, $packlist=null)
    {
        $id = Yii::$app->request->post('itemid');
        $quantity = Yii::$app->request->post('quantity');
        $model = EventExtraItem::find()->where(['event_id'=>$event_id, 'offer_extra_item_id'=>$id])->one();
        if ($model)
        {
            $model->quantity+=$quantity;
            $model->save();
        }else{
            $oei = OfferExtraItem::findOne($id);
            $model = new EventExtraItem();
            $model->event_id = $event_id;
            $model->offer_extra_item_id = $id;
            $model->quantity = $quantity;
            $model->name = $oei->name;
            $model->gear_category_id = $oei->category_id;
            $model->weight = $oei->weight*$quantity;
            $model->volume = $oei->volume*$quantity;
            $model->save();
            $model->createPacklist($packlist);
            if ($oei->cost)
            {
                $ee = new EventExpense();
                $ee->event_id = $event_id;
                $ee->name = $oei->name."[x".$quantity."]";
                $ee->section = $oei->category->name;
                $ee->amount = $oei->cost;
                $ee->amount_customer = $quantity*$oei->price*(1-$oei->discount/100)+$quantity*$oei->price*(1-$oei->discount/100)*$oei->first_day_percent*($oei->duration-1);
                if (!$ee->save())
                {
                    Yii::$app->response->format = Response::FORMAT_JSON;
                    $response = [
                        'success'=>0,
                        'error'=>'',
                        'connected'=>[],
                        'outerconnected'=>[]
                    ];
                    return $response;
                }
            }

        }

        Yii::$app->response->format = Response::FORMAT_JSON;
        $response = [
            'success'=>1,
            'error'=>'',
            'connected'=>[],
            'outerconnected'=>[]
        ];
        return $response;
        exit;
    }

    /**
     * Updates an existing EventExtraItem model.
     * If update is successful, the browser will be redirected to the 'view' page.
     * @param integer $id
     * @return mixed
     */
    public function actionUpdate($id)
    {
        $model = \common\models\PacklistExtra::findOne($id);
        $model = $model->eventExtraItem;

        if ($model->load(Yii::$app->request->post()) && $model->save()) {
            return $this->redirect(['/event/view', 'id' => $model->event_id, '#'=>'tab-gear']);
        } else {
            return $this->render('update', [
                'model' => $model,
            ]);
        }
    }

    /**
     * Deletes an existing EventExtraItem model.
     * If deletion is successful, the browser will be redirected to the 'index' page.
     * @param integer $id
     * @return mixed
     */
    public function actionDelete($id)
    {
        $model = \common\models\PacklistExtra::findOne($id);
        $e = $model->eventExtraItem;
        $q = $model->quantity;
        $model->delete();
        $event_id = $e->event_id;
        $e->quantity -=$q;
        if ($e->quantity>0)
            $e->save();
        else
            $e->delete();

        return $this->redirect(['/event/view', 'id' => $event_id, '#'=>'tab-gear']);
    }

    
    /**
     * Finds the EventExtraItem model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     * @param integer $id
     * @return EventExtraItem the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function findModel($id)
    {
        if (($model = EventExtraItem::findOne($id)) !== null) {
            return $model;
        } else {
            throw new NotFoundHttpException('The requested page does not exist.');
        }
    }
}
