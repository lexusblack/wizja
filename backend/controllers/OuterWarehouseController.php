<?php
namespace backend\controllers;

use common\components\filters\AccessControl;
use common\helpers\ArrayHelper;
use common\models\Event;
use common\models\EventLog;
use common\models\EventOuterGearModel;
use common\models\EventOuterGear;
use common\models\RentOuterGearModel;
use common\models\RentOuterGear;
use common\models\GearCategory;
use common\models\OuterGear;
use common\models\OuterGearModel;
use common\models\Offer;
use common\models\Rent;
use Yii;
use backend\components\Controller;
use yii\data\ActiveDataProvider;
use yii\helpers\Url;
use yii\web\BadRequestHttpException;
use yii\web\NotFoundHttpException;
use yii\web\Response;

class OuterWarehouseController extends Controller
{
    protected $_gearDataProvider;

    public $title;
    public $returnRoute;
    public $enableCsrfValidation = false;

    public function behaviors()
    {
        return [
            'access' => [
                'class' => AccessControl::className(),
                'rules' => [
                    [
                        'allow'=>true,
                        'actions' => ['index', 'active-model'],
                        'roles' => ['gearOuterWarehouse'],
                    ],
                    [
                        'allow'=>true,
                        'actions' => ['assign', 'assign-outer-gear', 'manage-outer-gear', 'assign-o-gear', 'manage-gear-connected', 'save', 'add-to-event', 'cross-rental', 'cross-rental2', 'remove-from-packlist'],
                        'roles' => ['eventEventEditEyeOuterGearManage','menuOffersEdit'],
                    ],
                    [
                        'allow'=>true,
                        'actions' => ['store-order'],
                        'roles' => ['gearOuterWarehouseMove'],
                    ],
                ],
            ],
        ];
    }

    public function actions()
    {
        return [

            'upload'=> [
                'class'=>\backend\actions\UploadAction::className(),
                'upload'=>'/gear'

            ]
        ];
    }

    public function actionRemoveFromPacklist($id)
    {
        $p = \common\models\PacklistOuterGear::findOne($id);
        $p->delete();
        exit;
    }

    public function actionActiveModel($activeModel=null)
    {
        $itemProvider = OuterGear::find()
            ->andFilterWhere([
                'outer_gear_model_id'=>$activeModel, 'active'=>1
            ]);
            $gearItemDataProvider = new ActiveDataProvider([
            'query'=>$itemProvider,
            'pagination'=>false,
            'sort'=>false,
        ]);
        return $this->renderAjax('active_model', ['itemProvider'=>$gearItemDataProvider ]);
    }

    public function actionCrossRental($id, $event_id, $gear_id, $conflict_id)
    {
        $cr = \common\models\CrossRental::findOne($id);
        $customer = \common\models\Customer::find()->where(['nip'=>$cr->company->nip])->one();
        $gear = \common\models\Gear::findOne($gear_id);
        if (!$customer)
        {
            //dodajemy klienta
            $customer = new \common\models\Customer();
            $customer->name = $cr->owner_name;
            $customer->nip = $cr->company->nip;
            $customer->customer = 1;
            $customer->supplier = 1;
            $customer->city = $cr->owner_city;
            $customer->address = $cr->owner_address;
            $customer->email = $cr->owner_mail;
            $customer->phone = $cr->owner_phone;
            $customer->country = $cr->owner_country;
            $customer->save();

        }else{
            if (!$customer->active)
            {
                $customer->active = 1;
                $customer->save();
            }
        }
        $ogm = \common\models\OuterGearModel::find()->where(['name'=>$cr->gearModel->name])->one();
        if (!$ogm)
        {
            //dodajemy model sprzętu zewnętrznego do magazynu
            $ogm = new \common\models\OuterGearModel();
            $ogm->attributes = $cr->gearModel->attributes;
            $ogm->category_id = $gear->category_id;
            $ogm->type = 1;
            if ($ogm->save())
            {
                $uploadDir = Yii::getAlias('@uploadroot/gear/');
                $sourceDir = Yii::getAlias('@uploadrootAll/gear/');
                copy($sourceDir.$ogm->photo, $uploadDir.$ogm->photo); 
            }  
        }else{
            if (!$ogm->active)
            {
                $ogm->active = 1;
                $ogm->save();
            }
        }
        $og = \common\models\OuterGear::find()->where(['outer_gear_model_id'=>$ogm->id, 'company_id'=>$customer->id])->one();
        if (!$og)
        {
            $og = new \common\models\OuterGear();
            $og->outer_gear_model_id = $ogm->id;
            $og->company_id = $customer->id;
            $og->quantity = $cr->quantity;
            $og->save();
        }else{
            if (!$og->active)
            {
                $og->active = 1;
                $og->save();
            }
        }
        return $this->renderAjax('cross-rental', ['outerGear'=>$og, 'event_id'=>$event_id, 'conflict_id'=>$conflict_id]);
    }

    public function actionCrossRental2($id, $event_id, $gear_id)
    {
        $cr = \common\models\CrossRental::findOne($id);
        $customer = \common\models\Customer::find()->where(['nip'=>$cr->company->nip])->one();
        $gear = \common\models\OuterGearModel::findOne($gear_id);
        if (!$customer)
        {
            //dodajemy klienta
            $customer = new \common\models\Customer();
            $customer->name = $cr->owner_name;
            $customer->nip = $cr->company->nip;
            $customer->customer = 1;
            $customer->supplier = 1;
            $customer->city = $cr->owner_city;
            $customer->address = $cr->owner_address;
            $customer->email = $cr->owner_mail;
            $customer->phone = $cr->owner_phone;
            $customer->country = $cr->owner_country;
            $customer->save();

        }else{
            if (!$customer->active)
            {
                $customer->active = 1;
                $customer->save();
            }
        }
        $ogm = \common\models\OuterGearModel::find()->where(['name'=>$cr->gearModel->name])->one();
        if (!$ogm)
        {
            //dodajemy model sprzętu zewnętrznego do magazynu
            $ogm = new \common\models\OuterGearModel();
            $ogm->attributes = $cr->gearModel->attributes;
            $ogm->category_id = $gear->category_id;
            $ogm->type = 1;
            if ($ogm->save())
            {
                $uploadDir = Yii::getAlias('@uploadroot/gear/');
                $sourceDir = Yii::getAlias('@uploadrootAll/gear/');
                copy($sourceDir.$ogm->photo, $uploadDir.$ogm->photo); 
            }  
        }else{
            if (!$ogm->active)
            {
                $ogm->active = 1;
                $ogm->save();
            }
        }
        $og = \common\models\OuterGear::find()->where(['outer_gear_model_id'=>$ogm->id, 'company_id'=>$customer->id])->one();
        if (!$og)
        {
            $og = new \common\models\OuterGear();
            $og->outer_gear_model_id = $ogm->id;
            $og->company_id = $customer->id;
            $og->quantity = $cr->quantity;
            $og->save();
        }else{
            if (!$og->active)
            {
                $og->active = 1;
                $og->save();
            }
        }
        return $this->renderAjax('cross-rental2', ['outerGear'=>$og, 'event_id'=>$event_id, 'gear'=>$gear, 'ogm'=>$ogm]);
    }


    public function actionAddToEvent($task_id, $event_id)
    {
        $model = new EventOuterGearModel;
        $model->event_id = $event_id;
        $model->quantity = 1;
        $model->prod = 1;
        if ($model->load(Yii::$app->request->post()))
        {
                            $id = $event_id;
                            $itemId = $model->outer_gear_model_id;
                            $quantity = $model->quantity;
                            Event::assignOuterGearModel($event_id, $model->outer_gear_model_id, $model->quantity);
                            $eogm = EventOuterGearModel::findOne(['event_id'=>$id, 'outer_gear_model_id'=>$itemId]);
                            $ids = ArrayHelper::map(OuterGear::find()->where(['outer_gear_model_id'=>$itemId])->asArray()->all(), 'id', 'id');
                            $eogs = EventouterGear::find()->where(['event_id'=>$id, 'outer_gear_id'=>$ids])->all();
                            $sum = 0;
                            foreach ($eogs as $eog)
                            {
                                $sum+=$eog->quantity;
                            }
                            if ($sum >= $eogm->quantity)
                            {
                                $eogm->resolved = 1;
                            }else{
                                $eogm->resolved = 0;
                            }
                            $eogm->prod = 1;
                            $eogm->save();
                            exit;
        }
        return $this->renderAjax('add-to-event', ['model'=>$model]);
    }

    /**
     * Lists all Gear models.
     * @return mixed
     */
    public function actionIndex($c=null, $s=null, $s2=null, $q=null, $from_date=null, $to_date=null, $activeModel=null)
    {
        Url::remember();
        $this->_setDataProviders($activeModel, $c, $s,$s2,$q,$from_date, $to_date);
        $gearItemDataProvider = null;
        if ($activeModel)
        {
            $itemProvider = OuterGear::find()
            ->andFilterWhere([
                'outer_gear_model_id'=>$activeModel, 'active'=>1
            ]);
            $gearItemDataProvider = new ActiveDataProvider([
            'query'=>$itemProvider,
            'pagination'=>false,
            'sort'=>false,
        ]);
        }
        return $this->render('index', [
            'gearDataProvider'=>$this->_gearDataProvider,
            'activeModel' => $activeModel,
            'itemProvider' => $gearItemDataProvider,
            's' => $s,
        ]);
    }

    public function actionAssign($id, $type, $c=null, $s=null, $s2=null, $q=null, $from_date=null, $to_date=null, $activeModel=null, $activeGroup=null, $conflict=null, $type2=null, $item=null, $return=null)
    {
        $model = $this->_loadModel($id, $type);

//        $assignedItems = $model->getGearItems()->column();
        $className = $this->_getClassName($type);
        $assignedItems = $className::getAssignedOuterModelQuantities($id);
        if ($model===null)
        {
            throw new NotFoundHttpException(Yii::t('app', 'Nie znaleziono modelu!'));
        }
        $view_str = "offer-assign";
        $eventRelation = \common\models\OfferGear::find()->indexBy('gear_id')->where(['offer_id'=>$id])->all();
        $title = Yii::t('app', 'Przypisz sprzęt');
        $this->_setDataProviders($activeModel, $c, $s,$s2,$q,$from_date, $to_date);
        $itemProvider = null;
        if ($activeModel)
        {
            $itemProvider = OuterGear::find()
            ->andFilterWhere([
                'outer_gear_model_id'=>$activeModel
            ])->andWhere(['active'=>1]);
        }
        return $this->render('assign', [
            'event'=>$model,
            'type'=>$type,
            'title' => $title,
            'activeModel'=>$activeModel,
            'gearDataProvider'=>$this->_gearDataProvider,
            'activeModel' => $activeModel,
            'activeGroup'=>$activeGroup,
            'eventRelation'=>$eventRelation,
            'itemProvider' => $itemProvider,
            'conflict'=>$conflict,
            'type2'=>$type2,
            'item'=>$item,
            'return'=>$return
        ]);
    }

    public function actionManageGearConnected($event_id)
    {
        Yii::$app->response->format = Response::FORMAT_JSON;
        $request = Yii::$app->request;
        $response = [
            'success'=>1,
            'error'=>''
        ];
        $gsi= OuterGearModel::findOne($request->post('gear_id'));
        $quantity = $request->post('quantity');
        $event = Event::find()->where(['id'=>$event_id])->one();
                $oldQuantity=0;
                $egm = EventOuterGearModel::findOne(['outer_gear_model_id'=>$gsi->id, 'event_id'=>$event->id]);
                if ($egm)
                    $oldQuantity = $egm->quantity;
                else{
                    $params = ['event_id'=>$event_id, 'outer_gear_model_id' => $gsi->id];
                    $egm = new EventOuterGearModel($params);
                }
                $egm->quantity = $oldQuantity+$quantity;
                if ($egm->save() == false)
                {
                    $error = current($egm->getFirstErrors());
                    $response['responses'][] = [
                        'success'=>0,
                        'error'=>$error,
                        'name' => $gsi->name,
                        'quantity'=>$quantity,
                        'id'=>$gsi->id,
                        'total'=>$egm->quantity
                    ];
                }else{
                    $response['responses'][] = [
                        'success'=>1,
                        'error'=>'',
                        'name' => $gsi->name,
                        'quantity'=>$quantity,
                        'id'=>$gsi->id,
                        'total'=>$egm->quantity
                        ];


                }
        return $response;
    }


    protected function _setDataProviders($activeModel,$c, $s, $s2, $q=null, $from_date=null, $to_date=null)
    {
        //FIXME: Rozwiązać razem z menu kategorii
        $sub = $s2==null ? $s : $s2;
        $sub = $sub==null ? $c : $sub;
        $categoryIds = [];
        $ids = [];
        $favorite = 0;
        if ($c=='favorite')
        {
            $c=1;
            $favorite = 1;
        }
        $tmpCat = GearCategory::findOne($sub);

        if ($tmpCat !== null)
        {
            $ids = $tmpCat->children()->column();
        }

        $categoryIds = array_merge([$sub], $ids);

        //Model
        if ($favorite)
        {
            $gearQuery = OuterGearModel::find()->joinWith('outerGearFavorite')
                ->andFilterWhere([
                    'active'=>1
                ])->orderBy(['outer_gear_favorite.position'=>SORT_ASC]);
            $this->_gearDataProvider = new ActiveDataProvider([
            'query'=>$gearQuery,
            'pagination'=>false,
            'sort'=>[
                'defaultOrder' => ['sort_order'=>SORT_ASC],
            ]
        ]);
        }else{
            if ($q)
            {
                $gearQuery = OuterGearModel::find()
                ->andFilterWhere([
                    'active'=>1
                ])
                ->andFilterWhere(['like', 'name', $q]);
            }else{
                $gearQuery = OuterGearModel::find()
                ->andFilterWhere([
                    'category_id'=>$categoryIds,
                    'active'=>1
                ]);
            }
            $this->_gearDataProvider = new ActiveDataProvider([
            'query'=>$gearQuery,
            'pagination'=>false,
            'sort'=>[
                'defaultOrder' => ['sort_order'=>SORT_ASC],
            ]
        ]);
        }

        

        
    }

    protected function _loadModel($id,$type)
    {
        $className = $this->_getClassName($type);
        $model = $className::findOne($id);
        if ($model===null)
        {
            throw new NotFoundHttpException(Yii::t('app', 'Brak modelu'));
        }
        return $model;
    }

    protected function _getClassName($type)
    {
        switch ($type)
        {
            case 'event':
                $className = Event::className();
                if ($this->title != Yii::t('app', "Wydarzenie")." ") {
                    $this->title .= Yii::t('app', 'Wydarzenie').' ';
                }
                $this->returnRoute = '/event/view';
                break;
            case 'rent':
                $className = Rent::className();
                $this->title .= Yii::t('app', 'Wypożyczenie').' ';
                $this->returnRoute = '/rent/view';
                break;
            case 'offer':
                $className = Offer::className();
                $this->title .= Yii::t('app', 'Oferta'). ' ';
                $this->returnRoute = '/offer/default/view';
                break;
            default:
                throw new BadRequestHttpException(Yii::t('app', 'Błędne żadanie'));
                break;
        }
        return $className;
    }

    public function actionSave($event_id=null, $gear_id, $rent_id=null)
    {
        Yii::$app->response->format = Response::FORMAT_JSON;
        if ($event_id)
            $eog = EventOuterGear::find()->where(['outer_gear_id'=>$gear_id])->andWhere(['event_id'=>$event_id])->one();
        else
            $eog = RentOuterGear::find()->where(['outer_gear_id'=>$gear_id])->andWhere(['rent_id'=>$rent_id])->one();
        $params = Yii::$app->request->post('EventOuterGear');
        if ($params)
            $params = $params[Yii::$app->request->post('editableIndex')];
        else
            $params = Yii::$app->request->post();
        if (isset($params['reception_time']))
        {
            $eog->reception_time = $params['reception_time'];
            $eog->save();
            $output = ['output'=>substr($eog->reception_time,0,10), 'message'=>''];
            return $output;
            exit;
        }
        if (isset($params['return_time']))
        {
            $eog->return_time = $params['return_time'];
            $eog->save();
            $output = ['output'=>substr($eog->return_time,0,10), 'message'=>''];
            return $output;
            exit;
        }
        if (isset($params['user_id']))
        {
            $eog->user_id = $params['user_id'];
            $eog->save();
            $output = ['output'=>$eog->user->displayLabel, 'message'=>''];
            return $output;
            exit;
        }
        if (isset($params['description']))
        {
            $eog->description = $params['description'];
            $eog->save();
            $output = ['output'=>$eog->description, 'message'=>''];
            return $output;
            exit;
        }
        exit;
    }

    public function actionManageOuterGear($id, $type, $outer=null)
    {
        
        
        if ($type=='event'){
            $eog = Yii::$app->request->post('EventOuterGear');
            Event::assignOuterGear2($id, $eog);
            if ($outer)
            {
                $quantity = Yii::$app->request->post('EventOuterGear')['quantity'];
                $eogm = EventOuterGearModel::find()->where(['outer_gear_model_id'=>$outer])->andWhere(['event_id'=>$id])->one();
                $gear_id = $eogm->outerGearModel->getEventOuterGearIds();
                $missing = $eogm->quantity;
                $model = Event::findOne($id);
                $gears = $model->getEventOuterGears()->where(['IN', 'outer_gear_id', $gear_id])->all();
                            foreach ($gears as $g)
                            {
                                $missing-=$g->quantity;
                            }
                if ($quantity>=$missing)
                {
                    $eogm->quantity=$eogm->quantity-$missing;
                }else{
                    $eogm->quantity=$eogm->quantity-$quantity;
                }
                if ($eogm->quantity>0)
                    $eogm->save();
                else
                    $eogm->delete();


            }
                if (Yii::$app->request->post('EventOuterGear')['price'])
                {
                    $og = OuterGear::findOne($eog['outer_gear_id']);
                    $og->price = $eog['price'];
                    $og->save();
                }
        }else{
            $eog = Yii::$app->request->post('RentOuterGear');
            Rent::assignOuterGear2($id, $eog);
        }
        exit;
    }

    /**
     * @param $id integer Event id
     * @param $type string event/rent
     * @param $noItem integer Czy ilość
     */
    
    public function actionAssignOGear($id,$type, $noItem=0, $group=0, $model=0)
    {
           Yii::$app->response->format = Response::FORMAT_JSON;
        $response = [
            'success'=>1,
            'error'=>'',
        ];
        $className = $this->_getClassName($type);
        if ($noItem == 0)
        {
            $params = Yii::$app->request->post();
            if ($params['quantity']>0)
            {
                $params['add'] = 1;
            }
            else
            {
                $params['add'] = 0;
            }
            if (!isset($params['itemId']) && isset($params['itemid'])) {
                $params['itemId'] = $params['itemid'];
            }
            $quantity = ArrayHelper::getValue($_POST, 'quantity', 1);
            $items = [$params['itemId']];
            $response['gear'] =  OuterGear::find()->where(['id'=>$params['itemId']])->asArray()->one();

            foreach ($items as $itemId)
            {
                if ($params['add'] == 1)
                {
                    $className::assignOuterGear($id, $itemId, $quantity);
                }
                else
                {
                   $className::removeOuterGear($id, $itemId);
                    $eogm = EventOuterGearModel::findOne(['event_id'=>$id, 'outer_gear_model_id'=>$response['gear']['outer_gear_model_id']]);
                    $ids = ArrayHelper::map(OuterGear::find()->where(['outer_gear_model_id'=>$response['gear']['outer_gear_model_id']])->asArray()->all(), 'id', 'id');
                    $eogs = EventouterGear::find()->where(['event_id'=>$id, 'outer_gear_id'=>$ids])->all();
                    $sum = 0;
                    foreach ($eogs as $eog)
                    {
                        $sum+=$eog->quantity;
                    }
                    if ($sum >= $eogm->quantity)
                    {
                        $eogm->resolved = 1;
                    }else{
                        $eogm->resolved = 0;
                    }
                    $eogm->save();
                }
            }

        }
        return $response;     
    }


    public function actionAssignOuterGear($id,$type, $noItem=0, $group=0, $model=0, $type2=null, $item=null)
    {
        Yii::$app->response->format = Response::FORMAT_JSON;
        $response = [
            'success'=>1,
            'error'=>'',
        ];
        $className = $this->_getClassName($type);
        if ($noItem == 0)
        {
            $params = Yii::$app->request->post();
            if ($params['quantity']>0)
            {
                $params['add'] = 1;
            }
            else
            {
                $params['add'] = 0;
            }
            if (!isset($params['itemId']) && isset($params['itemid'])) {
                $params['itemId'] = $params['itemid'];
            }
            $quantity = ArrayHelper::getValue($_POST, 'quantity', 1);
            $items = [$params['itemId']];
            $response['gear'] =  OuterGearModel::find()->where(['id'=>$params['itemId']])->asArray()->one();

            foreach ($items as $itemId)
            {
                if ($params['add'] == 1)
                {
                    
                    if ($type == 'event')
                    {
                            $className::assignOuterGearModel($id, $itemId, $quantity);
                            $eogm = EventOuterGearModel::findOne(['event_id'=>$id, 'outer_gear_model_id'=>$itemId]);
                            $ids = ArrayHelper::map(OuterGear::find()->where(['outer_gear_model_id'=>$itemId])->asArray()->all(), 'id', 'id');
                            $eogs = EventouterGear::find()->where(['event_id'=>$id, 'outer_gear_id'=>$ids])->all();
                            $sum = 0;
                            foreach ($eogs as $eog)
                            {
                                $sum+=$eog->quantity;
                            }
                            if ($sum >= $eogm->quantity)
                            {
                                $eogm->resolved = 1;
                            }else{
                                $eogm->resolved = 0;
                            }
                            $eogm->save();
                    }
                    if ($type == 'rent')
                    {
                            $className::assignOuterGearModel($id, $itemId, $quantity);
                            $eogm = RentOuterGearModel::findOne(['rent_id'=>$id, 'outer_gear_model_id'=>$itemId]);
                            $ids = ArrayHelper::map(OuterGear::find()->where(['outer_gear_model_id'=>$itemId])->asArray()->all(), 'id', 'id');
                            $eogs = RentOuterGear::find()->where(['rent_id'=>$id, 'outer_gear_id'=>$ids])->all();
                            $sum = 0;
                            foreach ($eogs as $eog)
                            {
                                $sum+=$eog->quantity;
                            }
                            if ($sum >= $eogm->quantity)
                            {
                                $eogm->resolved = 1;
                            }else{
                                $eogm->resolved = 0;
                            }
                            $eogm->save();
                    }
                    if ($type == 'offer')
                    {
                        $className::assignOuterGearModel($id, $itemId, $quantity, null, $type2, $item);
                    }
                }
                else
                {
                    if ($type == 'offer')
                    {
                        $className::removeOuterGearModel($id, $itemId, $type2, $item);
                    }else{
                        $className::removeOuterGearModel($id, $itemId);
                    }
                   
                }
            }

        }
        return $response;
    }

    public function actionStoreOrder()
    {
        $data = Yii::$app->request->post('data', null);
        if ($data !== null)
        $models = OuterGear::findAll($data);
        foreach ($models as $model)
        {
            $model->sort_order = array_search($model->id, $data);
            $model->update(false);
        }
    }

}
