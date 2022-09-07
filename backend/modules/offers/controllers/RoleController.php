<?php
namespace backend\modules\offers\controllers;

use backend\components\Controller;
use common\models\Offer;
use common\models\OfferRole;
use common\models\OfferRoleSchema;
use common\models\OfferRoleSchemaItem;
use common\models\UserEventRole;
use yii\web\NotFoundHttpException;
use kartik\form\ActiveForm;
use yii\web\Response;
use yii\helpers\ArrayHelper;


class RoleController extends Controller
{
    public $layout = '@backend/themes/e4e/layouts/main-panel';
    public $enableCsrfValidation = false;

    public function actionAddForm($time_type, $offer_id)
    {
        $role_ids = ArrayHelper::map(OfferRole::find()->where(['offer_id'=>$offer_id])->andWhere(['time_type'=>$time_type])->asArray()->all(), 'role_id', 'role_id');
        if ($role_ids)
            $x = UserEventRole::find()->where(['active'=>1])->andWhere(['NOT IN', 'id', $role_ids])->one();
        else
            $x = UserEventRole::find()->where(['active'=>1])->one();
        $offer = Offer::findOne($offer_id);
        $role = new OfferRole();
        $role->time_type = $time_type;
        $role->offer_id = $offer_id;
        $role->role_id = $x->id;
        $price = \common\models\RolePrice::find()->where(['role_id'=>$x->id])->andWhere(['currency'=>$offer->priceGroup->currency])->orderBy(['default'=>SORT_DESC])->one();
        if ($price)
        {
            $role->role_price_id = $price->id;
            $role->price =$price->price;
            $role->cost = $price->cost;
            $role->cost_hour = $price->cost_hour;
            $role->duration = 1;
            $role->unit = $price->unit;
        }else{
            if ($x->default_salary_customer==1)
            {
                $role->duration = 1;
                $role->price = $x->salary_customer;
            }else{
                $role->duration = $offer->getPeriodTime($time_type);
                $role->price = $x->salary_customer_hours;
            }
            $price = \common\models\RolePrice::find()->where(['role_id'=>$x->id])->one();
            if ($price)
            {
                $role->role_price_id = $price->id;
                $role->price =$price->price;
                $role->cost = $price->cost;
                $role->cost_hour = $price->cost_hour;
                $role->duration = 1;
                $role->unit = $price->unit;
            }
        }

        $role->type = $x->default_salary_customer;
        $role->quantity = 1;
        
        $role->save();
        return $this->renderAjax('add-form', [
            'offer_id' => $offer_id,
            'time_type'=>$time_type,
            'role'=>$role,
            'model'=>$role->offer
        ]);        
    }

    public function actionSave2($offer_id)
    {
        \Yii::$app->response->format = Response::FORMAT_JSON;
        $id = \Yii::$app->request->post('id');
        $group_id = \Yii::$app->request->post('group_id');
        $model = OfferRole::find()->where(['id'=>$id])->one();
        $price = \common\models\RolePrice::find()->where(['id'=>$group_id])->one();
        if ($price)
        {
            $model->role_price_id = $price->id;
            $model->price =$price->price;
            $model->cost = $price->cost;
            $model->cost_hour = $price->cost_hour;
            $model->unit = $price->unit;
            $model->save();
        }
        return $model;
    }

    public function actionSave($old=false, $new_group=false)
    {
        $or = \Yii::$app->request->post('OfferRole');
        \Yii::$app->response->format = Response::FORMAT_JSON;
        $model= OfferRole::find()->where(['id'=>$or['id']])->one();
            if (!$model)
                $model = new OfferRole();
            if ($model->load(\Yii::$app->request->post()))
            {
                if (($new_group)||($old))
                {
                    if ($old)
                    {
                        $price = \common\models\RolePrice::find()->where(['role_id'=>$model->role_id])->andWhere(['currency'=>$model->offer->priceGroup->currency])->orderBy(['default'=>SORT_DESC])->one();
                        if ($price)
                        {
                            $model->role_price_id = $price->id;
                            $model->price =$price->price;
                            $model->cost = $price->cost;
                            $model->cost_hour = $price->cost_hour;
                            $model->unit = $price->unit;
                        }else{
                            $x = UserEventRole::findOne($model->role_id);
                                    if ($x->default_salary_customer==1)
                                    {
                                        $model->duration = 1;
                                        $model->price = $x->salary_customer;
                                    }else{
                                        $model->duration = $model->offer->getPeriodTime($model->type);
                                        $model->price = $x->salary_customer_hours;
                                    }
                        }
                        $model->save();
                        return $this->renderAjax('add-form', [
                            'offer_id' => $model->offer_id,
                            'time_type'=>$model->type,
                            'role'=>$model,
                            'model'=>$model->offer
                        ]); 
                    }else{
                        $price = \common\models\RolePrice::find()->where(['id'=>$model->role_price_id])->one();
                        if ($price)
                        {
                            $model->role_price_id = $price->id;
                            $model->price =$price->price;
                            $model->cost = $price->cost;
                            $model->cost_hour = $price->cost_hour;
                            $model->unit = $price->unit;
                        }
                    }

                }
                $model->save();
            }
        return $model;
        exit;
    }

    public function actionCopy($time_type, $offer_id, $schedule_from)
    {
        
        $schedule = \common\models\OfferSchedule::findOne($time_type);
        $schedule2 = \common\models\OfferSchedule::findOne($schedule_from);
        $models = OfferRole::find()->where(['time_type'=>$schedule2->id])->andWhere(['offer_id'=>$offer_id])->all();
        foreach ($models as $or)
        {
            $orsi = OfferRole::find()->where(['role_id'=>$or->role_id, 'offer_id'=>$offer_id, 'time_type'=>$time_type])->one();
            if (!$orsi)
                $orsi = new OfferRole();
            $orsi->role_id = $or->role_id;
            $orsi->quantity = $or->quantity;
            $orsi->duration = $or->duration;
            $orsi->cost = $or->cost;
            $orsi->cost_hour = $or->cost_hour;
            $orsi->description= $or->description;
            $orsi->role_price_id = $or->role_price_id;
            $orsi->time_type = $time_type;
            $orsi->price = $or->price;
            $orsi->unit = $or->unit;
            $orsi->offer_id = $offer_id;
            $orsi->save();
        }    
        return $this->redirect(['assign', 'id' => $offer_id]);   
    }

    public function actionLoadSchema($offer_id, $schema, $view = false)
    {
        $offer = Offer::findOne($offer_id);
        foreach ($offer->offerRoles as $or)
        {
            $or->delete();
        }       
        $models = OfferRoleSchemaItem::find()->where(['offer_role_schema_id'=>$schema])->all();
        foreach ($models as $or)
        {
            
            $schedule = \common\models\OfferSchedule::find()->where(['name'=>$or->time_type, 'offer_id'=>$offer_id])->one();
            if ($schedule)
            {
            $orsi = new OfferRole();
            $orsi->role_id = $or->role_id;
            $orsi->quantity = $or->quantity;
            $orsi->duration = $or->duration;
            $orsi->time_type = $schedule->id;
            $orsi->role_price_id = $or->role_price_id;
            if ($orsi->role_price_id)
            {
                $price = \common\models\RolePrice::find()->where(['id'=>$orsi->role_price_id])->one();
                    if ($price)
                    {
                        $orsi->role_price_id = $price->id;
                        $orsi->price =$price->price;
                        $orsi->cost = $price->cost;
                        $orsi->cost_hour = $price->cost_hour;
                        $orsi->unit = $price->unit;
                    }else{
                        $orsi->role_price_id = null;
                        $orsi->cost = $or->userEventRole->salary;
                        $orsi->cost_hour = $or->userEventRole->salary_hours;
                    }
            }else{
                    if ($or->type ==1){
                        $orsi->price = $or->userEventRole->salary_customer;
                        $orsi->cost = $or->userEventRole->salary;
                        $orsi->cost_hour = $or->userEventRole->salary_hours;
                    }
                    else{
                        $orsi->price = $or->userEventRole->salary_customer_hours;
                        $orsi->cost = $or->userEventRole->salary;
                        $orsi->cost_hour = $or->userEventRole->salary_hours;
                    }
                         
            }

            $orsi->offer_id = $offer_id;
            $orsi->type = $or->type;
            $orsi->save();                
            }

        }
        if ($view)
            return $this->redirect(['/offer/default/view', 'id' => $offer_id]);
        else
            return $this->redirect(['assign', 'id' => $offer_id]);
    }

    public function actionSaveSchema($offer_id, $name)
    {
        $model = OfferRoleSchema::find()->where(['name'=>$name])->andWhere(['user_id'=>\Yii::$app->user->id])->one();
        if ($model)
            $model->delete();
        $model = new OfferRoleSchema();
        $model->name = $name;
        $model->user_id = \Yii::$app->user->id;
        $model->save();
        $offer = Offer::findOne($offer_id);
        foreach ($offer->offerRoles as $or)
        {
            $schedule = \common\models\OfferSchedule::findOne($or->time_type);
            $orsi = new OfferRoleSchemaItem();
            $orsi->offer_role_schema_id = $model->id;
            $orsi->role_id = $or->role_id;
            $orsi->quantity = $or->quantity;
            $orsi->duration = $or->duration;
            $orsi->time_type = $schedule->name;
            $orsi->price = $or->price;
            $orsi->type = $or->type;
            $orsi->role_price_id = $or->role_price_id;
            $orsi->save();
        }
        exit;
    }

    public function actionAssign($id)
    {
        $model = Offer::findOne($id);
        $model->loadLinkedObjects();
        $oldRoles = $model->roleIds;
        $roles = UserEventRole::getRolesCustomerMoney();
        $rolesHour = UserEventRole::getRolesCustomerMoneyHour();
        $params = \Yii::$app->request->post();

        if ($model->load($params) && $model->validate(['skillIds']))
        {
            if ($model->roleIds == '')
            {
                $model->roleIds = [];
            }
            $toAdd = array_diff($model->roleIds,$oldRoles);
            $toDelete = array_diff($oldRoles, $model->roleIds);
            OfferRole::deleteAll([
                'offer_id'=>$model->id,
                'role_id'=>$toDelete,
            ]);

            foreach ($toAdd as $roleId)
            {
                $obj = new OfferRole([
                    'offer_id'=>$model->id,
                    'role_id'=>$roleId,
                ]);
                $obj->save();
            }

//            \Yii::$app->session->setFlash('success', 'Zapisano');
//            return $this->refresh();
        }

        return $this->render('assign', [
            'model' => $model,
            'roles' => $roles,
            'rolesHour' =>$rolesHour
        ]);
    }
}