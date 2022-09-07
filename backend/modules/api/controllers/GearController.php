<?php


namespace backend\modules\api\controllers;


use common\models\BarCode;
use common\models\RfidLog;
use common\models\GearItemsNoItemsRfid;
use common\models\GearGroup;
use common\models\GearItem;
use common\models\GearCategory;
use common\models\Gear;
use common\models\GearService;
use common\models\MobileQrScan;
use common\models\OuterGear;
use Yii;
use yii\web\BadRequestHttpException;
use yii\web\MethodNotAllowedHttpException;
use yii\web\NotFoundHttpException;
use yii\web\UnprocessableEntityHttpException;

class GearController extends BaseController {
	public $modelClass = 'common\models\GearItem';

	
    public function actionRfid()
    {
        if (Yii::$app->request->isPost) {
            if (Yii::$app->request->post("rfid")!="")
            {
                date_default_timezone_set(Yii::$app->params['timeZone']);
                $rfid = Yii::$app->request->post("rfid");
                $rfidLog = new RfidLog();
                $rfidLog->tag = $rfid;
                $rfidLog->datetime = date("Y-m-d H:i:s");
                $rfidLog->reader = "mobile";
                $rfidLog->save();
                $return = self::findGearByRfid($rfid);
                return $return;
            }else{
                throw new MethodNotAllowedHttpException();
            }
        }else{
            throw new MethodNotAllowedHttpException();
        }
    }

    public function actionRfids()
    {
        if (Yii::$app->request->isPost) {
            if (Yii::$app->request->post("rfid")!="")
            {
                $rfids = json_decode(Yii::$app->request->post("rfid"));
                date_default_timezone_set(Yii::$app->params['timeZone']);
                $return = [];
                //$rfid = Yii::$app->request->post("rfid");
                foreach ($rfids as $rfid){
                $rfidLog = new RfidLog();
                $rfidLog->tag = $rfid;
                $rfidLog->datetime = date("Y-m-d H:i:s");
                $rfidLog->reader = "mobile";
                $rfidLog->save();
                $return[] = self::findGearByRfid($rfid);
                }
                return $return;
            }else{
                throw new MethodNotAllowedHttpException();
            }
        }else{
            throw new MethodNotAllowedHttpException();
        }
    }

public static function findGearByRfid($rfid_code) {
        if ($gear = GearItem::find()->where(['rfid_code' => $rfid_code])->one()) {
            return [ 'rfid'=>$rfid_code, 'gear'=>$gear->gear->name, 'type'=>'item', 'id'=>$gear->id, 'success'=>1, 'model_id'=>$gear->gear_id, 'number'=>$gear->number];
        }
        if ($gear = GearItemsNoItemsRfid::find()->where(['rfid_code' => $rfid_code])->one()) {
            return [ 'rfid'=>$rfid_code, 'gear'=>$gear->gearItem->gear->name, 'type'=>'gear', 'id'=>$gear->gearItem->gear_id, 'success'=>1, 'model_id'=>$gear->gearItem->gear_id, 'number'=>''];
        }
        if ($case = GearGroup::find()->where(['rfid_code' => $rfid_code])->one()) {
            
            foreach ($case->gearItems as $item)
            {
                $name = $item->gear->name;
                $gear_id = $item->gear_id;
            }
            return [ 'rfid'=>$rfid_code, 'gear'=>$name, 'type'=>'case', 'id'=>$case->id, 'success'=>1, 'model_id'=>$gear_id, 'number'=>$case->itemNumbers];
        }
        return [ 'rfid'=>$rfid_code, 'gear'=>null, 'type'=>'', 'id'=>null, 'success'=>0];
    }

    public function actionScan($id) {
		$q = $id;
		$no_items = false;

		if (Yii::$app->request->isPost) {
			// rozszyfrowujemy barcody i qrcody
			$user_id = Yii::$app->request->getBodyParam('user_id');
			if (!$user_id) {
				throw new BadRequestHttpException(Yii::t('app', 'Brak parametru user_id'));
			}
			if (strlen($id) == 13) {
				$id = (int)substr($q, 4, 9);
				$gear = null;

				// mamy do czynienia z casem (gear_group)
				if (substr($q, 0, 2) == BarCode::ITEMS_GROUP) {
					$gear = GearGroup::find()->where(['id'=>$id])->one();
					$type = MobileQrScan::TYPE_CASE;
					$type2 = 'group';
				}

				// mamy do czynienia ze sprzetem z naszego magazynu (gear)
				else if (substr($q, 0, 2) == BarCode::SINGEL_PRODUCT) {
					if (substr($q, 2, 2) == BarCode::OUR_WAREHOUSE) {
						$gear = GearItem::find()->where(['id'=>$id])->one();
						$type = MobileQrScan::TYPE_GEAR_OUR;
						$type2 = 'item';
                        if ($gear->gear->no_items==1)
                        {
                            $gear = $gear->gear;
                            $type2 = 'gear';
                            $type = MobileQrScan::TYPE_GEAR_OUR;
                        }
					}
				}
					// mamy do czynienia ze sprzetem z zewnetrznego magazynu (outer_gear)
					else {				
						if (substr($q, 0, 2) == BarCode::MODEL) {
                        if (substr($q, 2, 2) == BarCode::OUR_WAREHOUSE) {
                                    $gear = Gear::find()->where(['id'=>$id])->andWhere(['active'=>1])->one();
                                    $gear_id = $gear->id;
                                    $type = MobileQrScan::TYPE_GEAR_OUR;
                                    $type2 = 'gear';
                                    }
                                } 
				}

				if ((!(substr($q, 0, 2) == BarCode::ITEMS_GROUP))&&(!substr($q, 0, 2) == BarCode::MODEL)) {
					if (substr($q, 0, 2) == BarCode::SINGEL_PRODUCT) {
						if (!(substr($q, 2, 2) == BarCode::OUR_WAREHOUSE) && !(substr($q, 2, 2) == BarCode::OUTER_WAREHOUSE)) {
							throw new BadRequestHttpException(Yii::t('app', 'Niepoprawny kod'));
						}
					}
					else {
						throw new BadRequestHttpException( Yii::t( 'app', 'Niepoprawny kod' ) );
					}
				}

				if ($gear) {
					date_default_timezone_set(Yii::$app->params['timeZone']);
					$scan = new MobileQrScan();
					$scan->user_id = $user_id;
					$scan->type = $type;
					$scan->gear_id = $gear->id;
					$scan->created_at = date("Y-m-d H:i:s");
					if ($scan->save()) {
						if ($type2=='gear')
						{
							return ['gear'=>$this->returnGear($gear)];
						}
						if ($type2=='group')
						{
							return ['group'=>$this->returnGroup($gear)];
						}
						if ($type2=='item')
						{
							return ['item'=>$gear];
						}
						
					}
					throw new UnprocessableEntityHttpException(Yii::t('app', 'Nie udało się zapisać skanowania, proszę spróbować jeszcze raz'));
				}
				throw new NotFoundHttpException(Yii::t('app', 'Nie znaleziono sprzętu o tym kodzie'));
			}
			throw new BadRequestHttpException(Yii::t('app', 'Niepoprawna długość kodu'));
		}
		throw new MethodNotAllowedHttpException();
	}

	public function actionScanNoItems($id) {
		if (Yii::$app->request->isPost) {
			$quantity = Yii::$app->request->getBodyParam('quantity');
			if (!$quantity) {
				throw new BadRequestHttpException(Yii::t('app', 'Brak parametru quantity'));
			}
			if (!is_numeric($quantity) || $quantity <= 0) {
				throw new BadRequestHttpException(Yii::t('app', 'Quantity musi być liczbą dodatnią'));
			}
			for ($i = 0; $i < $quantity; $i++) {
				date_default_timezone_set(Yii::$app->params['timeZone']);
				$scan = new MobileQrScan();
				$scan->user_id = Yii::$app->user->id;
				$scan->type = MobileQrScan::TYPE_GEAR_OUR;
				$scan->gear_id = $id;
				$scan->created_at = date("Y-m-d H:i:s");
				$scan->save();
			}
			return ['status' => 200, 'message' => Yii::t('app', 'Zeskanowano urządzenie')];
		}
		throw new MethodNotAllowedHttpException();
	}

	public function returnGear($gear)
	{
		$start = date("Y-m-d");
		$end = date("Y-m-d");
		$tmp = $gear->attributes;
				$tmp['info'] = "";
                $tmp['packing'] = $gear->getPacking2();
				    if ($gear->no_items)
                    {
                        $tmp['all'] = $gear->quantity;
                    }
                    else
                    {
                        $tmp['all'] = $gear->getGearItems()->andWhere(['active'=>1])->count();
                        $tmp['items'] = [];
                        $tmp['cases'] = [];
                        $groups = [];
                        foreach ($gear->gearItems as $item)
                        {
                        	if (!$item->group_id)
                        		$tmp['items'][] = $item->toArray();
                        	else{
                                $tmp['items'][] = $item->toArray();
                        		$groups[] = $item->group_id;
                        	}
                        }
                        if ($groups)
                        {
                        	$groups = GearGroup::find()->where(['IN', 'id', $groups])->all();
                        	foreach ($groups as $group)
                        	{
                        		$tmpgroup = $group->attributes;
                        		$tmpgroup['items'] = [];
                        		$tmpgroup['gear_name'] = $group->gearItems[0]->gear->name;
                        		foreach ($group->gearItems as $item)
                        		{
                        			$tmpgroup['items'] [] = $item->toArray();
                        		}
                        		$tmp['cases'][] = $tmpgroup;

                        	}
                        }

                    }
                    if ($gear->no_items)
                    {
                        $serwisNumber = $gear->getNoItemSerwis();
                        $tmp['available'] = $gear->getAvailabe($start, $end)-$serwisNumber;
                    }
                    else
                    {
                        $serwisNumber = 0;
                        foreach ($gear->gearItems as $item) {
                            if ($item->active == 1 && $item->status === GearItem::STATUS_SERVICE) {
                                $serwisNumber++;
                            }
                        }

                        $tmp['available'] = ($gear->getAvailabe($start, $end)-$serwisNumber);
                    }
                    $tmp['service'] = $serwisNumber;
               	    $gearsBookings = $gear->getEvents($start, $end);
                    $tmp['bookings'] = [];
                    foreach ($gearsBookings['events'] as $g)
                    {
                        $tmp2 = [];
                        $tmp2['id'] = $g->packlist->event_id;
                        $tmp2['name'] = $g->packlist->event->name;
                        $tmp2['type'] = 'event';
                        $tmp2['quantity'] = $g->quantity;
                        $tmp2['start'] = $g->start_time;
						$tmp2['end'] = $g->end_time;
                        $tmp['bookings'][] = $tmp2;
                    }
                    foreach ($gearsBookings['rents'] as $g)
                    {
                        $tmp2 = [];
                        $tmp2['id'] = $g->rent_id;
                        $tmp2['name'] = $g->rent->name;
                        $tmp2['type'] = 'rent';
                        $tmp2['quantity'] = $g->quantity;
                        $tmp2['start'] = $g->start_time;
						$tmp2['end'] = $g->end_time;
                        $tmp['bookings'][] = $tmp2;
                    }
                    return $tmp;
	}

	public function returnGroup($group)
	{
		                $tmpgroup = $group->attributes;
		                $tmpgroup['gear_name'] = $group->gearItems[0]->gear->name;
                        $tmpgroup['items'] = [];
                        foreach ($group->gearItems as $item)
                        {
                        	$tmpgroup['items'] [] = $item->toArray();
                        }
                        $tmp['cases'][] = $tmpgroup;
                        return $tmpgroup;
	}

    public function actionSendToService($id, $no_items=false)
    {
        if ($no_items)
        {
            $model = Gear::find()->where(['id'=>$id])->andWhere(['active'=>1])->one();
            if ($model)
            {
                    $id = $model->gearItems[0]->id;
                    $service = new GearService();
                    $service->gear_item_id = $id;
                    $service->description = Yii::$app->request->post("description");
                    $service->quantity = Yii::$app->request->post("quantity");
                    $service->status = Yii::$app->request->post("status");
                    $service->save();
                    return ['status' => 200, 'message' => Yii::t('app', 'Wysłano na serwis')];
            }else{
                throw new BadRequestHttpException(Yii::t('app', 'Niepoprawna wartość ID'));
            }
        }else{
            $model = GearItem::findOne($id);
            if ($model)
            {
                if ($model->status == \common\models\GearItem::STATUS_ACTIVE)
                {
                    $service = new GearService();
                    $service->gear_item_id = $model->id;
                    $service->description = Yii::$app->request->post("description");
                    $service->status = Yii::$app->request->post("status");
                    $service->save();
                    return ['status' => 200, 'message' => Yii::t('app', 'Wysłano na serwis')];
                }else{
                    throw new BadRequestHttpException(Yii::t('app', 'Sprzęt aktualnie w serwisie'));
                }
            }else{
                throw new BadRequestHttpException(Yii::t('app', 'Niepoprawna wartość ID'));
            }
        }
    }

    public function actionChangeService($id)
    {
        $service = GearService::findOne($id);
        if ($service)
        {
            $service->info = Yii::$app->request->post("description");
            $service->status = Yii::$app->request->post("status");
            $service->save();
            return ['status' => 200, 'message' => Yii::t('app', 'Zedytowano serwis')];
        }else{
            throw new BadRequestHttpException(Yii::t('app', 'Niepoprawna wartość ID'));
        }
    }


	public function actionSearch()
	{
		if (Yii::$app->request->isPost) {
			$query = Gear::find()->where(['active'=>1]);
			if (Yii::$app->request->post("name")!="")
			{
				$query->andWhere(['like', 'name', Yii::$app->request->post("name")]);
			}
			if (Yii::$app->request->post("category")!="")
			{
				$category = Yii::$app->request->post("category");
				$tmpCat = GearCategory::findOne(Yii::$app->request->post("category"));
            	if ($tmpCat !== null)
            	{
                	$ids = $tmpCat->children()->column();
            	}
       			$ids = array_merge([$category], $ids);
       			$query->andWhere(['in', 'category_id', $ids]);
			}
			if (Yii::$app->request->post("start")!="")
			{
				$start = Yii::$app->request->post("start");
				$end = Yii::$app->request->post("end");
			}else{
				$start = date("Y-m-d");
				$end = date("Y-m-d");
			}
			$gears = $query->all();
			$gearsRet = [];
			foreach ($gears as $gear)			
			{
				$tmp = $gear->attributes;
				$tmp['info'] = "";
                $tmp['packing'] = $gear->getPacking2();
				    if ($gear->no_items)
                    {
                        $tmp['all'] = $gear->quantity;
                        $tmp['items'] = [];
                        $tmp['cases'] = [];
                    }
                    else
                    {
                        $tmp['all'] = $gear->getGearItems()->andWhere(['active'=>1])->count();
                        $tmp['items'] = [];
                        $tmp['cases'] = [];
                        $groups = [];
                        foreach ($gear->gearItems as $item)
                        {
                        	if (!$item->group_id)
                        		$tmp['items'][] = $item->toArray();
                        	else{
                        		$groups[] = $item->group_id;
                                $tmp['items'][] = $item->toArray();
                        	}
                        }
                        if ($groups)
                        {
                        	$groups = GearGroup::find()->where(['IN', 'id', $groups])->all();
                        	foreach ($groups as $group)
                        	{
                        		$tmpgroup = $group->attributes;
                        		$tmpgroup['items'] = [];
                        		$tmpgroup['gear_name'] = $group->gearItems[0]->gear->name;
                        		foreach ($group->gearItems as $item)
                        		{
                        			$tmpgroup['items'] [] = $item->toArray();
                        		}
                        		$tmp['cases'][] = $tmpgroup;

                        	}
                        }

                    }
                    $serwisNumber = 0;
                    if ($gear->no_items)
                    {
                        $serwisNumber = $gear->getNoItemSerwis();
                        $tmp['available'] = $gear->getAvailabe($start, $end)-$serwisNumber;
                    }
                    else
                    {
                        $serwisNumber = 0;
                        foreach ($gear->gearItems as $item) {
                            if ($item->active == 1 && $item->status === GearItem::STATUS_SERVICE) {
                                $serwisNumber++;
                            }
                        }

                        $tmp['available'] = ($gear->getAvailabe($start, $end)-$serwisNumber);
                    }
                    $tmp['service'] = $serwisNumber;
               	    $gearsBookings = $gear->getEvents($start, $end);
                    $tmp['bookings'] = [];
                    foreach ($gearsBookings['events'] as $g)
                    {
                        $tmp2 = [];
                        $tmp2['id'] = $g->packlist->event_id;
                        $tmp2['name'] = $g->packlist->event->name;
                        $tmp2['type'] = 'event';
                        $tmp2['quantity'] = $g->quantity;
                        $tmp2['start'] = $g->start_time;
						$tmp2['end'] = $g->end_time;
                        $tmp['bookings'][] = $tmp2;
                    }
                    foreach ($gearsBookings['rents'] as $g)
                    {
                        $tmp2 = [];
                        $tmp2['id'] = $g->rent_id;
                        $tmp2['name'] = $g->rent->name;
                        $tmp2['type'] = 'rent';
                        $tmp2['quantity'] = $g->quantity;
                        $tmp2['start'] = $g->start_time;
						$tmp2['end'] = $g->end_time;
                        $tmp['bookings'][] = $tmp2;
                    }
				
				$gearsRet[] = $tmp;
			}
			return $gearsRet;
		}
		throw new MethodNotAllowedHttpException();
	}
}