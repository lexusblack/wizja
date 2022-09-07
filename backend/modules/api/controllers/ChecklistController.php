<?php


namespace backend\modules\api\controllers;

use Yii;
use common\models\Checklist;
use common\models\Todolist;
use yii\web\BadRequestHttpException;
use yii\web\ForbiddenHttpException;
use yii\web\MethodNotAllowedHttpException;
use yii\web\NotFoundHttpException;
use yii\web\UnprocessableEntityHttpException;

class ChecklistController extends BaseController {

    public $modelClass = '\common\models\Todolist';


    public function actionList()
    {
        $lists = Todolist::find()->where(['user_id'=>Yii::$app->user->id])->all();
        $checklists = [];
        $tmp = ['id'=>0, 'name'=>Yii::t('app','Ogólne'), 'user_id'=>Yii::$app->user->id, 'create_time'=>null, 'update_time'=>null];
        $items = Checklist::find()->where(['user_id'=>Yii::$app->user->identity->id])->andWhere(['is','todolist_id', null])->orderBy(['priority'=>SORT_ASC])->all();
        $tmp['items'] = [];

        foreach($items as $item)
        {
            $tmp['items'][] = $item->toArray();
        }
        $checklists[] = $tmp;
        foreach ($lists as $list)
        {
                $tmp = $list->toArray();
                $tmp['items'] = [];
                foreach ($list->checklists as $item)
                {
                    $tmp['items'][] = $item->toArray();
                }
                $checklists[] = $tmp;
        }

        return $checklists;
    }

    public function actionDone()
    {
        $id = Yii::$app->request->post('id');
        $done = Yii::$app->request->post('done');
        $check = Checklist::findOne($id);
        if ($check)
        {
            $check->done = $done;
            if ($check->save())
            {
                    return [
                            'name' => Yii::t('app', 'Checklista'),
                            'message' => Yii::t('app', 'Zmieniono status'),
                            'code' => 0,
                            'status' => 200
                        ];
            }else{
                throw new NotFoundHttpException(Yii::t('app', 'Błąd zapisu'));
            }
        }else{
             throw new NotFoundHttpException(Yii::t('app', 'Błędne ID'));
        }
    }
    public function actionAdd()
    {
        
        $list = new Checklist;
        $id = Yii::$app->request->post('id');
        if ($id!=0)
            $list->todolist_id = $id;
        else $id = null;
        $list->name = Yii::$app->request->post('name');
        $list->deadline = Yii::$app->request->post('time');
        $list->user_id = Yii::$app->user->identity->id;
        $list->done = 0;
        $list->priority = Checklist::find()->where(['id'=>$id])->count()+1;
        if ($list->save())
        {
                        return [
                            'name' => Yii::t('app', 'Checklista'),
                            'message' => Yii::t('app', 'Dodano zadanie'),
                            'code' => 0,
                            'status' => 200
                        ];
        }else{
            throw new NotFoundHttpException(Yii::t('app', 'Błąd zapisu'));
        }
        throw new NotFoundHttpException(Yii::t('app', 'Błąd'));
    }

    public function actionUpdateItem()
    {
        $id = Yii::$app->request->post('id');
        $list = Checklist::findOne($id);
        $list->name = Yii::$app->request->post('name');
        $list->deadline = Yii::$app->request->post('time');
        if ($list->save())
        {
                        return [
                            'name' => Yii::t('app', 'Checklista'),
                            'message' => Yii::t('app', 'Zedytowano zadanie'),
                            'code' => 0,
                            'status' => 200
                        ];
        }else{
            throw new NotFoundHttpException(Yii::t('app', 'Błąd zapisu'));
        }
        throw new NotFoundHttpException(Yii::t('app', 'Błąd'));
    }

    public function actionOrder()
    {
        $i = 0;
        $items = json_decode(Yii::$app->request->post('items'));

        foreach ($items as $id) {
            $model = Checklist::findOne($id);
            $model->priority = $i;
            $model->save();
            $i++;
        }
        return [
                            'name' => Yii::t('app', 'Checklista'),
                            'message' => Yii::t('app', 'Zmieniono kolejność'),
                            'code' => 0,
                            'status' => 200
                        ];
    }

    public function actionAddToList()
    {
        $list_id = Yii::$app->request->post('list_id');
        $task_id = Yii::$app->request->post('id');
        $task = Checklist::findOne($task_id);
        if ($list_id)
            $task->todolist_id = $list_id;
        else
            $task->todolist_id = null;
        if ($task->save())
        {
                        return [
                            'name' => Yii::t('app', 'Checklista'),
                            'message' => Yii::t('app', 'Dodano do innej listy'),
                            'code' => 0,
                            'status' => 200
                        ];
        }else{
            throw new NotFoundHttpException(Yii::t('app', 'Błąd zapisu'));
        }
        throw new NotFoundHttpException(Yii::t('app', 'Błąd'));   
    }

    public function actionDeleteItem()
    {
        $id = Yii::$app->request->post('id');
        $task = Checklist::findOne($id);
        if ($task->delete())
        {
                    return [
                            'name' => Yii::t('app', 'Checklista'),
                            'message' => Yii::t('app', 'Usunięto zadanie'),
                            'code' => 0,
                            'status' => 200
                        ];
        }else{
            throw new NotFoundHttpException(Yii::t('app', 'Błąd usunięcia'));
        }
        throw new NotFoundHttpException(Yii::t('app', 'Błąd'));  
    }

    public function actionDeleteList()
    {
        $id = Yii::$app->request->post('id');
        $task = Todolist::findOne($id);
        if ($task->delete())
        {
                    return [
                            'name' => Yii::t('app', 'Checklista'),
                            'message' => Yii::t('app', 'Usunięto listę'),
                            'code' => 0,
                            'status' => 200
                        ];
        }else{
            throw new NotFoundHttpException(Yii::t('app', 'Błąd usunięcia'));
        }
        throw new NotFoundHttpException(Yii::t('app', 'Błąd'));
    }

    public function actionCreateList()
    {
        $list = new Todolist;
        $list->name = Yii::$app->request->post('name');
        $list->user_id = Yii::$app->user->identity->id;
        if ($list->save())
        {
                        return [
                            'name' => Yii::t('app', 'Checklista'),
                            'message' => Yii::t('app', 'Dodano listę'),
                            'code' => 0,
                            'status' => 200
                        ];
        }else{
            throw new NotFoundHttpException(Yii::t('app', 'Błąd zapisu'));
        }
        throw new NotFoundHttpException(Yii::t('app', 'Błąd'));
    }

    public function actionUpdateList()
    {
        $id = Yii::$app->request->post('id');
        $task = Todolist::findOne($id);
        $list->name = Yii::$app->request->post('name');
        if ($list->save())
        {
                        return [
                            'name' => Yii::t('app', 'Checklista'),
                            'message' => Yii::t('app', 'Edytowano listę'),
                            'code' => 0,
                            'status' => 200
                        ];
        }else{
            throw new NotFoundHttpException(Yii::t('app', 'Błąd zapisu'));
        }
        throw new NotFoundHttpException(Yii::t('app', 'Błąd'));
    }
}


/**
 *
 * Dzisiejsze wydarzenia:
 * get: /admin/api/dashboard/events?type=today // return array/error
 *
 * Najbliższe wydarzenia:
 * get: /admin/api/dashboard/events?type=upcoming // return array/error
 *
 * Wydarzenia działu:
 * get: /admin/api/dashboard/events?type=department // return array/error
 *
 * Powiadomienia:
 * get: /admin/api/dashboard/notifications // return array/error
 *
 * Zadania:
 * get: /admin/api/dashboard/tasks // return array/error
 * put: /admin/api/dashboard/tasks/{id}/status // && $_POST['status'] - zmienia status na: status = 0 => nowy, status = 10 => zrobiony // return status 200 / error
 * post: /admin/api/dashboard/tasks/{id}/comment //  && $_POST['comment'] - dodaje komentarz // return status 200 / error
 *
 * Checklista:
 * get: /admin/api/dashboard/checklist return array/error
 * put: /admin/api/dashboard/checklist/{id}/status // zmienia status // return status 200 / error
 *
 */