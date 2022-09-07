<?php

namespace backend\controllers;

use Yii;
use common\models\CrossRental;
use common\models\Gear;
use common\models\GearModel;
use common\models\CrossRentalSearch;
use backend\components\Controller;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;

/**
 * CrossRentalController implements the CRUD actions for CrossRental model.
 */
class CrossRentalController extends Controller
{
    public function behaviors()
    {
        return [];
    }

    public function actionUpdateData()
    {
        $models = CrossRental::find()->all();
        foreach ($models as $model)
        {
            $c = \common\models\Company::findOne(['code'=>$model->owner]);
            if ($c)
            {
                $model->latitude = $c->latitude;
                $model->longitude = $c->longitude;
                $model->save();
            }
        }
    }

    /**
     * Lists all CrossRental models.
     * @return mixed
     */
    public function actionIndex($c=null, $s=null, $s2=null)
    {
        $search = new CrossRentalSearch();
        //$search->attributes = Yii::$app->request->get();
        if ($s2)
            $search->category_id=$s2;
        else
            if ($s)
                $search->category_id=$s;
            else
                if ($c)
                    $search->category_id=$c;
        $category = $this->_getCategory([$c, $s, $s2]);
        $dataProvider = $search->search(Yii::$app->request->queryParams);

        return $this->render('index', [
            'searchModel' => $search,
            'dataProvider' => $dataProvider,
        ]);
    }

    protected function _getCategory($categories)
    {
        $category = 2;
        if ($categories[0])
            $category = $categories[0];
        if ($categories[1])
            $category = $categories[1];
        if ($categories[2])
            $category = $categories[2];
        $tmpCat = \common\models\CRCategory::findOne($category);
        return $tmpCat->name;
    }

    /**
     * Displays a single CrossRental model.
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

    public function actionDelete($gear_id)
    {
        $model = CrossRental::find()->where(['owner_gear_id'=>$gear_id])->andWhere(['owner'=>\Yii::$app->params['companyID']])->one();
        $model->delete();
        $this->redirect('/admin/warehouse');
    }
/*
    public function actionUpdate($id)
    {
        $model = CrossRental::find()->where(['id'=>$id])->one();
        if ($model->load(Yii::$app->request->post())) {
            $model->save();
            return $this->redirect(['admin']);
        } else {
            return $this->render('update', [
                'model' => $model,
            ]);
        }
    }
*/
    /**
     * Creates a new CrossRental model.
     * If creation is successful, the browser will be redirected to the 'view' page.
     * @return mixed
     */
    public function actionCreate($gear_id)
    {
        $model = CrossRental::find()->where(['owner_gear_id'=>$gear_id, 'owner'=>\Yii::$app->params['companyID']])->one();
        if (!$model)
            $model = new CrossRental();
        $model->owner_gear_id = $gear_id;
        $gear = Gear::findOne($gear_id);
        $model->owner = \Yii::$app->params['companyID'];
        $model->owner_name = Yii::$app->settings->get('companyName', 'main');
        $model->owner_city = Yii::$app->settings->get('companyCity', 'main');
        $model->owner_country = Yii::$app->settings->get('companyCountry', 'main');
        $model->owner_address = Yii::$app->settings->get('companyAddress', 'main');
        $model->owner_mail = Yii::$app->settings->get('crossRentalEmail', 'main');
        $model->owner_phone = Yii::$app->settings->get('crossRentalPhone', 'main');
        $model->quantity = $gear->numberOfItems();
        $model->price = 0;
        $company = \common\models\Company::find()->where(['code'=>\Yii::$app->params['companyID']])->one();
        if ($company)
        {
            $model->latitude = $company->latitude;
            $model->longitude = $company->longitude;
        }
        
        if ($model->load(Yii::$app->request->post())) {
            if (!$model->gear_model_id)
            {
                $gear_model = new GearModel();
                $gear_model->info = $gear->info;
                $gear_model->height = $gear->height;
                $gear_model->width = $gear->width;
                $gear_model->depth = $gear->depth;
                $gear_model->volume = $gear->volume;
                $gear_model->power_consumption = $gear->power_consumption;
                $gear_model->brightness = $gear->brightness;
                $gear_model->name = $gear->name;
                $gear_model->category_id = 1;
                $gear_model->type = 2;
                $gear_model->owner = \Yii::$app->params['companyID'];

                $gear_model->photo = $gear->photo;
                    $uploadDir = Yii::getAlias('@uploadrootAll/gear/');
                    $sourceDir = Yii::getAlias('@uploadroot/gear/');
                    $suorceFilename = $gear->photo;
                    $i=0;
                    $filename = explode(".", $gear->photo);
                    while (file_exists($uploadDir . $gear_model->photo)) {
                        $i++;
                        $gear_model->photo = $filename[0] . '-' . $i . '.' . $filename[1];             
                    }
                    $gear_model->save();
                    copy($sourceDir.$suorceFilename, $uploadDir.$gear_model->photo);
                $model->gear_model_id = $gear_model->id;
            }
            $model->category_id = $model->gearModel->category_id;
            $model->save();
            return $this->redirect(['/warehouse/index', 'c'=>$gear->category->getMainCategory()->id]);
        } else {
            return $this->render('create', [
                'model' => $model,
                'gear' => $gear
            ]);
        }
    }

    
    /**
     * Finds the CrossRental model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     * @param integer $id
     * @return CrossRental the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function findModel($id)
    {
        if (($model = CrossRental::findOne($id)) !== null) {
            return $model;
        } else {
            throw new NotFoundHttpException(Yii::t('app', 'Strona nie istnieje.'));
        }
    }
}
