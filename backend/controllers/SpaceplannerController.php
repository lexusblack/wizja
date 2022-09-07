<?php

namespace backend\controllers;

use Yii;
use common\models\Spaceplanner;
use common\models\SpaceplannerSearch;
use backend\components\Controller;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;
use common\models\Settings;

/**
 * SpaceplannerController implements the CRUD actions for Spaceplanner model.
 */
class SpaceplannerController extends Controller
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
        ];
    }

    public function beforeAction($action) {
        $this->enableCsrfValidation = false;
        return parent::beforeAction($action);
    }

    public function actionIndex()
    {
        $this->layout = 'spaceplanner-panel';
        $settings = Settings::find()->indexBy('key')->where(['section'=>'main'])->all();
        if ($settings['companyLogo'])
            $logo = $settings['companyLogo']->value;
        else
            $logo = '/themes/e4e/img/newem.png';
        return $this->render('index', [
                'logo' => $logo,
            ]);
    }

    /**
     * Lists all Spaceplanner models.
     * @return mixed
     */
    public function actionAll()
    {
        $models = Spaceplanner::find()->where(['user_id'=>Yii::$app->user->id])->all();
        if ($models)
        {
            $ids = [];
            foreach ($models as $model)
            {
                $create_time = \DateTime::createFromFormat('Y-m-d H:i:s', $model->create_time);
                $update_time = \DateTime::createFromFormat('Y-m-d H:i:s', $model->update_time);
                $ids[] = ['id'=>$model->id, 'name'=>$model->name, 'description'=>$model->description, 'creationDate'=>$create_time->getTimestamp(), 'lastUseDate'=>$update_time->getTimestamp()];
            }
            echo json_encode($ids);
        }else{
            echo json_encode(array('error' => Yii::t('app', 'Brak zapisanych projektów.')));
        }
        exit;
    }

    public function actionLoad()
    {
        $id = Yii::$app->request->post('id');
        if (!$id)
        {
            echo json_encode(array('error' => Yii::t('app', 'Coś poszło nie tak')));
            exit;
        }

        $model = $this->findModel($id);
        if ($model)
        {
            $return = ['id'=>$model->id, 'name'=>$model->name, 'description'=>$model->description, 'snapshot'=>json_decode($model->snapshot)];
            echo json_encode($return);
        }else{
            echo json_encode(array('error' => Yii::t('app', 'Coś poszło nie tak')));
        }
        exit;
    }

    public function actionSave()
    {
        
        if (Yii::$app->request->post('id'))
        {
            $model = $this->findModel(Yii::$app->request->post('id'));
        }else{
            $model = new Spaceplanner();
        }
        $model->name = Yii::$app->request->post('name');
        $model->description = Yii::$app->request->post('description');
        $model->snapshot= json_encode(Yii::$app->request->post('snapshot'));
        if ($model->save())
        {
            echo json_encode(array('id' => $model->id));
        }else{
            echo json_encode(array('error' => Yii::t('app', 'Coś poszło nie tak')));
        }
        exit;
    }
    public function actionUpdate()
    {
        
        if (Yii::$app->request->post('id'))
        {
            $model = $this->findModel(Yii::$app->request->post('id'));
        }else{
            echo json_encode(array('error' => Yii::t('app', 'Coś poszło nie tak')));
            exit;
        }
        $model->name = Yii::$app->request->post('name');
        $model->description = Yii::$app->request->post('description');
        if ($model->save())
        {
            echo json_encode(array('success' => true));
        }else{
            echo json_encode(array('error' => Yii::t('app', 'Coś poszło nie tak')));
        }
        exit;
    }



    /**
     * Deletes an existing Spaceplanner model.
     * If deletion is successful, the browser will be redirected to the 'index' page.
     * @param integer $id
     * @return mixed
     */
    public function actionDelete()
    {
        if (Yii::$app->request->post('id'))
        {
            $this->findModel(Yii::$app->request->post('id'))->deleteWithRelated();
            echo json_encode(array('success' => true));
        }else{
            echo json_encode(array('error' => Yii::t('app', 'Coś poszło nie tak')));
        } 
        exit;      

    }

    
    /**
     * Finds the Spaceplanner model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     * @param integer $id
     * @return Spaceplanner the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function findModel($id)
    {
        if (($model = Spaceplanner::findOne($id)) !== null) {
            return $model;
        } else {
            throw new NotFoundHttpException(Yii::t('app', 'Strona nie istnieje.'));
        }
    }
}
