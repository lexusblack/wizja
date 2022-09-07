<?php

namespace backend\controllers;

use Yii;
use common\models\EventSearch;
use common\models\OuterGear;
use common\models\OuterGearSearch;
use backend\components\Controller;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;
use yii\helpers\Url;

/**
 * OuterGearController implements the CRUD actions for OuterGear model.
 */
class OuterGearController extends Controller
{

    public function actions()
    {
        return [

            'upload'=> [
                'class'=>\backend\actions\UploadAction::className(),
                'upload'=>'/outer-gear'

            ]
        ];
    }

    public $enableCsrfValidation = false;

    /**
     * Lists all OuterGear models.
     * @return mixed
     */
    public function actionIndex()
    {
        $searchModel = new OuterGearSearch();
        $dataProvider = $searchModel->search(Yii::$app->request->queryParams);
        $dataProvider->query->andWhere(['active'=>1]);
        Url::remember();
        return $this->render('index', [
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
        ]);
    }

    /**
     * Displays a single OuterGear model.
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
     * Creates a new OuterGear model.
     * If creation is successful, the browser will be redirected to the 'view' page.
     * @return mixed
     */
    public function actionCreate($outerGearModelId = null)
    {
        
            $model = new OuterGear();
            $model->outer_gear_model_id = $outerGearModelId;
        

        if ($model->load(Yii::$app->request->post()) && $model->save())
        {
            Yii::$app->session->setFlash('success', Yii::t('app', 'Zapisano!'));
            return $this->goBack();
        }
        else
        {
            return $this->render('create', [
                'model' => $model,
            ]);
        }
    }

    /**
     * Updates an existing OuterGear model.
     * If update is successful, the browser will be redirected to the 'view' page.
     * @param integer $id
     * @return mixed
     */
    public function actionUpdate($id)
    {
        $model = $this->findModel($id);

        if ($model->load(Yii::$app->request->post()) && $model->save())
        {
            Yii::$app->session->setFlash('success', Yii::t('app', 'Zapisano!'));
            if (Url::previous('outer-warehouse'))
            {
                return $this->redirect(Url::previous('outer-warehouse'));
            }
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
     * Deletes an existing OuterGear model.
     * If deletion is successful, the browser will be redirected to the 'index' page.
     * @param integer $id
     * @return mixed
     */
    public function actionDelete($id)
    {
        $model = $this->findModel($id);
        $model->active = 0;
        $model->save();

        return $this->goBack();
        //return $this->redirect(['/outer-warehouse/index']);
    }

    public function actionHistory($id)
    {
        /* @var $dataProvider ActiveDataProvider */
        $searchModel = new EventSearch();
        $params = Yii::$app->request->queryParams;
        $dataProvider = $searchModel->search($params);

        $model = $this->findModel($id);
        $ids = $model->getEvents()->column();

        $dataProvider->query->andWhere(['id'=>$ids]);
        $dataProvider->sort->defaultOrder = ['event_start'=>SORT_DESC];

        return $this->render('history', [
            'model' => $model,
            'dataProvider'=>$dataProvider,
            'searchModel' => $searchModel
        ]);
    }

    /**
     * Finds the OuterGear model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     * @param integer $id
     * @return OuterGear the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function findModel($id)
    {
        if (($model = OuterGear::findOne($id)) !== null) {
            return $model;
        } else {
            throw new NotFoundHttpException(Yii::t('app', 'Strona nie istnieje.'));
        }
    }

    public function actionCopyToWarehouse($gear_id)
    {
        $model = new \backend\models\CopyToWarehouse();
        $outerGear = $this->findModel($gear_id);
        $model->category_id = $outerGear->category_id;


        if ($model->load(Yii::$app->request->post()) && $model->validate())
        {
            $gear = $model->copyToGear($gear_id);
            if($gear->save()) {
                // if($model->copy_gear_with_items) {
                //     $model->gear_id = $gear->id;
                //     $outerGearItems = \common\models\OuterGearItem::find()->where(['outer_gear_id' => $gear_id])->all();
                //     foreach ($outerGearItems as $key => $outerGearItem) {
                //         $model->copyToGearItem(null,$outerGearItem);
                //     }
                // }
                Yii::$app->session->setFlash('success', Yii::t('app', 'Zapisano!'));
                return $this->redirect(['/warehouse']);
            }
        }
        else
        {
            return $this->render('copy-to-warehouse', [
                'model' => $model,
                'outerGear' => $outerGear,
            ]);
        }
    }
}
