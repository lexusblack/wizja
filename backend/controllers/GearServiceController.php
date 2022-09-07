<?php

namespace backend\controllers;

use common\components\filters\AccessControl;
use Yii;
use common\models\GearService;
use common\models\GearServiceStatut;
use common\models\GearServiceSearch;
use backend\components\Controller;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;
use yii\helpers\Url;
use yii\web\Response;

/**
 * GearServiceController implements the CRUD actions for GearService model.
 */
class GearServiceController extends Controller
{
    public $enableCsrfValidation = false;
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
                        'actions' => ['index'],
                        'roles' => ['gearService'],
                    ],
                    [
                        'allow' => true,
                        'actions' => ['view'],
                        'roles' => ['gearServiceView'],
                    ],
                    [
                        'allow' => true,
                        'actions' => ['delete'],
                        'roles' => ['gearServiceDelete'],
                    ],
                    [
                        'allow' => true,
                        'actions' => ['update', 'send-back', 'create', 'priority'],
                        'roles' => ['gearServiceUpdate', 'gearItemServiceCreate'],
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
     * Lists all GearService models.
     * @return mixed
     */
    public function actionIndex($statut=null)
    {
        if (!$statut)
            $statut = GearServiceStatut::find()->where(['active'=>1])->one()->id;
        $searchModel = new GearServiceSearch();
        $searchModel->status = $statut;
        $dataProvider = $searchModel->search(Yii::$app->request->queryParams);
        Url::remember();
        return $this->render('index2', [
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
            'statut'=>$statut,
            'params'=>Yii::$app->request->queryParams
        ]);
    }

    /**
     * Displays a single GearService model.
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
     * Creates a new GearService model.
     * If creation is successful, the browser will be redirected to the 'view' page.
     * @return mixed
     */
    public function actionCreate($id)
    {
        $model = new GearService();
        $model->gear_item_id = $id;
        if ($model->load(Yii::$app->request->post()))
        {
            $return = true;
            if (!$model->warehouse_from)
            {
                $model->addError('warehouse_from', Yii::t('app', 'Pole obowiązkowe'));
                $return = false;
            }
            $statut = GearServiceStatut::find()->where(['id'=>$model->status])->one();
            if ($statut->type==1)
            {
                if (!$model->warehouse_to)
                {
                    $model->addError('warehouse_to', Yii::t('app', 'Sprzęt niezdolny do pracy, musi być przeniesiony do magazynu serwisowego'));
                    $return = false;
                }else{
                                        $w= \common\models\Warehouse::findOne($model->warehouse_to);

                    if ($w->type!=2)
                    {
                        $model->addError('warehouse_to', Yii::t('app', 'Sprzęt niezdolny do pracy, musi być przeniesiony do magazynu serwisowego'));
                            $return = false;
                    }
                }
            }
            if ($model->gearItem->gear->no_items)
            {
                if (!$model->quantity)
                {
                    $model->addError('quantity', Yii::t('app', 'Pole obowiązkowe'));
                    $return = false;
                }

                $total = \common\models\WarehouseQuantity::find()->where(['warehouse_id'=>$model->warehouse_from, 'gear_id'=>$model->gearItem->gear_id])->one();
                if ($total->quantity<$model->quantity)
                {
                    $model->addError('quantity', Yii::t('app', 'W magazynie nie ma tyle sprzętu'));
                    $return = false;
                }

            }
            if (!$return)
            {
                return $this->render('create', [
                            'model' => $model,
                        ]);
            }
            if ($model->save())
                {
                    //robimy przesunięcie magazynowe
                    if ($model->warehouse_to)
                    {
                        if ($model->warehouse_from!=$model->warehouse_to)
                        {
                            $m = new \common\models\GearMovement();
                            $m->type = 3;
                            $m->warehouse_from = $model->warehouse_from;
                            $m->warehouse_to = $model->warehouse_to;
                            $m->gear_id = $model->gearItem->gear_id;
                            $m->user_id = Yii::$app->user->id;
                            $m->datetime = date("Y-m-d H:i:s");
                            if ($model->gearItem->gear->no_items)
                            {
                                $m->quantity = $model->quantity;
                            }else{
                                $m->quantity = 1;
                                
                            }
                            $m->info = Yii::t('app', 'Serwis');
                            $m->save();

                            $wq = \common\models\WarehouseQuantity::findOne(['gear_id'=>$m->gear_id, 'warehouse_id'=>$m->warehouse_from]);
                            $wq->quantity-=$m->quantity;
                            $wq->save();
                            $wq2 = \common\models\WarehouseQuantity::findOne(['gear_id'=>$m->gear_id, 'warehouse_id'=>$m->warehouse_to]);
                            $wq2->quantity+=$m->quantity;
                            $wq2->save();
                            if (!$model->gearItem->gear->no_items)
                            {

                                $model->gearItem->warehouse_id = $model->warehouse_to;
                                $model->gearItem->save();
                                $move = new \common\models\GearMovementItem();
                                $move->gear_item_id = $model->gear_item_id;
                                $move->gear_movement_id = $m->id;
                                $move->save();

                            }
                        }
                    }
                        Yii::$app->session->setFlash('success', Yii::t('app', 'Zapisano!'));
                        return $this->redirect(['index']);
                }else{
                        return $this->render('create', [
                            'model' => $model,
                        ]);
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
     * Updates an existing GearService model.
     * If update is successful, the browser will be redirected to the 'view' page.
     * @param integer $id
     * @return mixed
     */
    public function actionUpdate($id)
    {
        $model = $this->findModel($id);
        $old_warehouse = $model->warehouse_to;
        if ($model->load(Yii::$app->request->post()))
        {
            $return = true;
            $statut = GearServiceStatut::find()->where(['id'=>$model->status])->one();
            if ($statut->type==1)
            {
                if (!$model->warehouse_to)
                {
                    $model->addError('warehouse_to', Yii::t('app', 'Sprzęt niezdolny do pracy, musi być przeniesiony do magazynu serwisowego'));
                    $return = false;
                }else{
                    $w= \common\models\Warehouse::findOne($model->warehouse_to);
                    if ($w->type!=2)
                    {
                        $model->addError('warehouse_to', Yii::t('app', 'Sprzęt niezdolny do pracy, musi być przeniesiony do magazynu serwisowego'));
                            $return = false;
                    }
                }
            }
            if ($old_warehouse!=$model->warehouse_to)
            {
                if ($old_warehouse)
                {
                    if (!$model->warehouse_to)
                    {
                        $model->warehouse_to=$old_warehouse;
                    }

                }
            }
            if (!$return)
            {
                    return $this->render('update', [
                            'model' => $model,
                        ]);
            }
            if ($model->save())
            {
                if ($old_warehouse!=$model->warehouse_to)
                {
                    if (!$old_warehouse)
                    {
                        $old_warehouse = $model->warehouse_from;
                    }
                    if ($old_warehouse)
                    {
                    $m = new \common\models\GearMovement();
                            $m->type = 3;
                            $m->warehouse_from = $old_warehouse;
                            $m->warehouse_to = $model->warehouse_to;
                            $m->gear_id = $model->gearItem->gear_id;
                            $m->user_id = Yii::$app->user->id;
                            $m->datetime = date("Y-m-d H:i:s");
                            if ($model->gearItem->gear->no_items)
                            {
                                $m->quantity = $model->quantity;
                            }else{
                                $m->quantity = 1;

                            }
                            $m->info = Yii::t('app', 'Serwis');
                            $m->save();
                            //echo var_dump($m);
                            //exit;

                            $wq = \common\models\WarehouseQuantity::findOne(['gear_id'=>$m->gear_id, 'warehouse_id'=>$m->warehouse_from]);
                            $wq->quantity-=$m->quantity;
                            $wq->save();
                            $wq2 = \common\models\WarehouseQuantity::findOne(['gear_id'=>$m->gear_id, 'warehouse_id'=>$m->warehouse_to]);
                            $wq2->quantity+=$m->quantity;
                            $wq2->save();
                    }
                            if (!$model->gearItem->gear->no_items)
                            {

                                $model->gearItem->warehouse_id = $model->warehouse_to;
                                $model->gearItem->save();
                                if ($old_warehouse)
                                {
                                $move = new \common\models\GearMovementItem();
                                $move->gear_item_id = $model->gear_item_id;
                                $move->gear_movement_id = $m->id;
                                $move->save();
                                }

                            }
                        

                }
            Yii::$app->session->setFlash('success', Yii::t('app', 'Zapisano!'));
            return $this->goBack();
            }else{
                    return $this->render('update', [
                'model' => $model,
            ]);
            }
        }
        else
        {
            return $this->render('update', [
                'model' => $model,
            ]);
        }
    }

    public function actionPriority($id)
    {
        Yii::$app->response->format = Response::FORMAT_JSON;
        $model = $this->findModel($id);
        $post = Yii::$app->request->post();
        $status = $post['GearService'][$post['editableIndex']]['priority'];
        $model->priority = $status;
        $model->save();
        $list = \common\models\GearService::getPriorityList();
        $output = ['output'=>$list[$model->priority], 'message'=>''];
        return $output;
        exit;
    }

    /**
     * Deletes an existing GearService model.
     * If deletion is successful, the browser will be redirected to the 'index' page.
     * @param integer $id
     * @return mixed
     */
    public function actionDelete($id)
    {
        $this->findModel($id)->delete();

        return $this->redirect(['index']);
    }

    /**
     * Finds the GearService model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     * @param integer $id
     * @return GearService the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function findModel($id)
    {
        if (($model = GearService::findOne($id)) !== null) {
            return $model;
        } else {
            throw new NotFoundHttpException(Yii::t('app', 'Strona nie istnieje.'));
        }
    }

    public function actionSendBack($id)
    {
        $session = Yii::$app->session;
        $model = $this->findModel($id);
        if ($model->sendBack())
        {
            $session->setFlash('succes', Yii::t('app', 'Egzemplarz zwrócono z serwisu'));
            return $this->redirect(['/gear-item/view', 'id'=>$model->gear_item_id]);
        }

        return $this->redirect(['view', 'id'=>$model->id]);
    }
}


