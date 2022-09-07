<?php

namespace backend\controllers;

use Yii;
use common\models\HallGroup;
use yii\data\ActiveDataProvider;
use backend\components\Controller;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;

/**
 * HallGroupController implements the CRUD actions for HallGroup model.
 */
class HallGroupController extends Controller
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
                        'actions' => ['index', 'view', 'create', 'update', 'delete', 'add-hall-audience', 'add-hall-group-photo', 'add-hall-hall-group', 'upload', 'save-audience', 'book', 'check-avability', 'remove', 'calendar', 'calendar-array', 'book-edit', 'book-delete', 'book-dates', 'bookings', 'create-from', 'store-order'],
                        'roles' => ['@']
                    ],
                    [
                        'allow' => false
                    ]
                ]
            ]
        ];
    }

            public function actions()
    {
        return [

            'upload'=> [
                'class'=>\backend\actions\UploadAction::className(),
                'upload'=>'/hall'

            ]
        ];
    }

        public function actionStoreOrder($favorite=false)
    {
        $data = Yii::$app->request->post('data', null);
        if ($data !== null)
        {
            $models = HallGroup::findAll($data);

            foreach ($models as $model)
            {
                $model->position = array_search($model->id, $data)+1;
                $model->update(false);
            }
        }

    }

    public function actionCalendar()
    {
        $halls = HallGroup::find()->orderBy(['position'=>SORT_ASC])->asArray()->all();
        $array2 = [];
        foreach ($halls as $h)
        {
            $array2[] = ["id"=>$h['id'], "title"=>$h['name']];
        }
        return $this->render('calendar', ['halls'=>$array2]);  
    }

    public function actionCalendarArray($start, $end)
    {
        $array = [];
        $events = \common\models\EventHallGroup::find()->where(['<', 'start_time', substr($end, 0, 10)])->andWhere(['>', 'end_time', substr($start, 0, 10)])->all();
        foreach ($events as $e)
        {
            $tmp = ['title'=>$e->event->name, 'id'=>$e->id, 'event_id'=>$e->event_id, 'book_id'=>$e->id, 'resourceId'=>$e->hall_group_id, 'start'=>substr($e->start_time, 0, 10)."T".substr($e->start_time, 11, 8), 'end'=>substr($e->end_time, 0, 10)."T".substr($e->end_time, 11, 8), 'backgroundColor'=>$e->statut->color] ;
            $array[] = $tmp;
            $ids = [];
            foreach ($e->hallGroup->halls as $hall)
            {
                $ids[] = $hall->id;
            }
            $hgs = \common\models\HallHallGroup::find()->where(['hall_id'=>$ids])->all();
            foreach ($hgs as $h)
            {
                if ($h->hall_group_id!=$e->hall_group_id)
                {
                    $tmp = ['title'=>$e->event->name, 'id'=>"r".$e->id, 'book_id'=>$e->id, 'event_id'=>$e->event_id, 'resourceId'=>$h->hall_group_id, 'start'=>substr($e->start_time, 0, 10)."T".substr($e->start_time, 11, 8), 'end'=>substr($e->end_time, 0, 10)."T".substr($e->end_time, 11, 8), 'backgroundColor'=>"#aaa"] ;
                    $array[] = $tmp;
                }
            }
            }
        return json_encode($array);
    }

    public function actionBookings($hall_id, $start, $end)
    {
        $model = $this->findModel($hall_id);
        $content= "";
        foreach ($model->getEventsOverlapping($start, $end) as $e)
                    {
                        $content .= "<i class='fa fa-circle' style='color:".$e->statut->color."'></i> ".$e->event->name." ".substr($e->start_time, 0, 16)." - ".substr($e->end_time, 0, 16)."<br/>";
                    }
        echo $content;
        exit;
    }

    public function actionCheckAvability($event_id, $hall_id, $start, $end)
    {
        $model = $this->findModel($hall_id);
        $hge = new \common\models\EventHallGroup();
        $hge->event_id = $event_id;
        $hge->hall_group_id = $hall_id;
        $hge->start_time = $start;
        $hge->end_time = $end;
        $s = \common\models\HallGroupStatut::find()->where(['active'=>1])->orderBy(['position'=>SORT_DESC])->one();
        $hge->statut_id = $s->id;
        $hge->create_time = date("Y-m-d H:i:s");
        if ($hge->load(Yii::$app->request->post()) && $hge->save()) {
            exit;
        }else{
                  $events = $model->getEventsOverlapping($start, $end);
        return $this->renderAjax('book_confirm', ['hall'=>$model, 'events'=>$events, 'event_id'=>$event_id, 'model'=>$hge]);  
        }

    }

    public function actionBookDates($id)
    {
        $hge = \common\models\EventHallGroup::findOne($id);
        $post = Yii::$app->request->post();
        $start = str_replace("T"," ",$post['start']);
        $end = str_replace("T"," ",$post['end']);
        $hge->start_time = $start;
        $hge->end_time = $end;
        $hge->save();
        exit;
    }
    public function actionBookEdit($id)
    {
        $hge = \common\models\EventHallGroup::findOne($id);
        if ($hge->load(Yii::$app->request->post()) && $hge->save()) {
            exit;
        }else{
            $events = $hge->hallGroup->getEventsOverlapping($hge->start_time, $hge->end_time);
            return $this->renderAjax('book_edit', ['model'=>$hge, 'events'=>$events]);  
        }
    }


    public function actionBookDelete($id)
    {
        $ehg = \common\models\EventHallGroup::findOne($id);
        $ehg->delete();
        exit;
    }

    public function actionRemove($event_id, $hall_id)
    {
        $ehg = \common\models\EventHallGroup::find()->where(['event_id'=>$event_id])->andWhere(['hall_group_id'=>$hall_id])->one();
        $ehg->delete();
        exit;
    }

    public function actionBook($event_id)
    {
        $event = \common\models\Event::findOne($event_id);
        $hallDataProvider =  new ActiveDataProvider([
                'query' => HallGroup::find(),
                'sort'=> ['defaultOrder' => ['position'=>SORT_ASC]]
            ]); 
        return $this->render('book', [
            'event' => $event,
            'hallDataProvider'=>$hallDataProvider
        ]);

    }

    public function actionSaveAudience($id)
    {
        $audience = \common\models\HallAudience::find()->where(['hall_audience_type_id'=>Yii::$app->request->post('type_id2')])->andWhere(['hall_group_id'=>$id])->one();
        if (!$audience)
        {
            $audience = new \common\models\HallAudience();
            $audience->hall_audience_type_id = Yii::$app->request->post('type_id');
            $audience->hall_group_id = $id;
        }
        $audience->audience = Yii::$app->request->post('audience');
        $audience->save();
        exit;
    }

    /**
     * Lists all HallGroup models.
     * @return mixed
     */
    public function actionIndex()
    {
        $dataProvider = new ActiveDataProvider([
            'query' => HallGroup::find()->orderBy(['position'=>SORT_ASC]),
        ]);

        return $this->render('index', [
            'dataProvider' => $dataProvider,
        ]);
    }

    /**
     * Displays a single HallGroup model.
     * @param integer $id
     * @return mixed
     */
    public function actionView($id)
    {
        $model = $this->findModel($id);
        return $this->render('view', [
            'model' => $this->findModel($id)
        ]);
    }

    /**
     * Creates a new HallGroup model.
     * If creation is successful, the browser will be redirected to the 'view' page.
     * @return mixed
     */
    public function actionCreate()
    {
        $model = new HallGroup();
        $model->position = HallGroup::find()->count();
        if ($model->load(Yii::$app->request->post()) && $model->save()) {
            $model->linkObjects();
            return $this->redirect(['index']);
        } else {
            return $this->render('create', [
                'model' => $model,
            ]);
        }
    }
    public function actionCreateFrom($id)
    {
        $model = new HallGroup();
        $hall = \common\models\Hall::findOne($id);
        $model->attributes = $hall->attributes;
        $model->position = HallGroup::find()->count();

        $model->save();
        $hhg = new \common\models\HallHallGroup();
        $hhg->hall_id = $id;
        $hhg->hall_group_id = $model->id;
        $hhg->save();
            return $this->redirect(['index']);
    }
    /**
     * Updates an existing HallGroup model.
     * If update is successful, the browser will be redirected to the 'view' page.
     * @param integer $id
     * @return mixed
     */
    public function actionUpdate($id)
    {
        $model = $this->findModel($id);
        $model->loadLinkedObjects();
        if ($model->load(Yii::$app->request->post()) && $model->save()) {
            return $this->redirect(['view', 'id' => $model->id]);
        } else {
            return $this->render('update', [
                'model' => $model,
            ]);
        }
    }

    /**
     * Deletes an existing HallGroup model.
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
     * Finds the HallGroup model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     * @param integer $id
     * @return HallGroup the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function findModel($id)
    {
        if (($model = HallGroup::findOne($id)) !== null) {
            return $model;
        } else {
            throw new NotFoundHttpException('The requested page does not exist.');
        }
    }
    
    /**
    * Action to load a tabular form grid
    * for HallAudience
    * @author Yohanes Candrajaya <moo.tensai@gmail.com>
    * @author Jiwantoro Ndaru <jiwanndaru@gmail.com>
    *
    * @return mixed
    */
    public function actionAddHallAudience()
    {
        if (Yii::$app->request->isAjax) {
            $row = Yii::$app->request->post('HallAudience');
            if((Yii::$app->request->post('isNewRecord') && Yii::$app->request->post('_action') == 'load' && empty($row)) || Yii::$app->request->post('_action') == 'add')
                $row[] = [];
            return $this->renderAjax('_formHallAudience', ['row' => $row]);
        } else {
            throw new NotFoundHttpException('The requested page does not exist.');
        }
    }
    
    /**
    * Action to load a tabular form grid
    * for HallGroupPhoto
    * @author Yohanes Candrajaya <moo.tensai@gmail.com>
    * @author Jiwantoro Ndaru <jiwanndaru@gmail.com>
    *
    * @return mixed
    */
    public function actionAddHallGroupPhoto()
    {
        if (Yii::$app->request->isAjax) {
            $row = Yii::$app->request->post('HallGroupPhoto');
            if((Yii::$app->request->post('isNewRecord') && Yii::$app->request->post('_action') == 'load' && empty($row)) || Yii::$app->request->post('_action') == 'add')
                $row[] = [];
            return $this->renderAjax('_formHallGroupPhoto', ['row' => $row]);
        } else {
            throw new NotFoundHttpException('The requested page does not exist.');
        }
    }
    
    /**
    * Action to load a tabular form grid
    * for HallHallGroup
    * @author Yohanes Candrajaya <moo.tensai@gmail.com>
    * @author Jiwantoro Ndaru <jiwanndaru@gmail.com>
    *
    * @return mixed
    */
    public function actionAddHallHallGroup()
    {
        if (Yii::$app->request->isAjax) {
            $row = Yii::$app->request->post('HallHallGroup');
            if((Yii::$app->request->post('isNewRecord') && Yii::$app->request->post('_action') == 'load' && empty($row)) || Yii::$app->request->post('_action') == 'add')
                $row[] = [];
            return $this->renderAjax('_formHallHallGroup', ['row' => $row]);
        } else {
            throw new NotFoundHttpException('The requested page does not exist.');
        }
    }
}
