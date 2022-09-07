<?php


namespace backend\modules\api\controllers;

use common\helpers\ArrayHelper;
use common\models\Checklist;
use common\models\form\Dashboard;
use common\models\Task;
use Yii;
use yii\web\BadRequestHttpException;
use yii\web\ForbiddenHttpException;
use yii\web\MethodNotAllowedHttpException;
use yii\web\NotFoundHttpException;
use yii\web\UnprocessableEntityHttpException;

class DashboardController extends BaseController {

    public $modelClass = '\common\models\form\Dashboard';

    public function actionNotifications() {
        if (Yii::$app->request->isGet) {
            return Dashboard::getUserMessages();
        }
        throw new MethodNotAllowedHttpException();
    }

    public function actionTasks($id = null, $name = null) {
        if (Yii::$app->request->isGet) {
            if ($id == null) {
            	$tasks = Yii::$app->user->getIdentity()->getTasksQuery()->all();
            	if ($tasks) {
            		$tasks = ArrayHelper::toArray($tasks);
            		foreach ($tasks as $id => $task) {
			            $tasks[$id]['contentNoHtml'] = strip_tags($task['content']);
		            }
	            }
                return $tasks;
            }
            else {
                if ($task = Task::findOne($id)) {
	                if ($task->isMine()) {
	                    $data = ArrayHelper::toArray($task);
	                    $data['contentNoHtml'] = strip_tags($task->content);
                        return $data;
                    }
                    throw new ForbiddenHttpException(Yii::t('app', "Nie masz uprawnień do tego zadania"));
                }
            }
            throw new NotFoundHttpException(Yii::t('app', "Nie znaleziono zadania"));
        }

        if (Yii::$app->request->isPost) {
            if ($id) {
                if ($task = Task::findOne($id)) {
                    if ($task->isMine()) {
                        if ($name == 'comment' && $comment = Yii::$app->request->post('comment')) {
                            if ($task->addComment($comment)) {
                                return [
                                    'status' => 202,
                                ];
                            }
                            throw new UnprocessableEntityHttpException();
                        }
                        if ($name == 'status') {
	                        $status = Yii::$app->request->getBodyParam('status');
	                        if ($status == 10 || $status == null || $status == 0) {
		                        if ( $task->setStatus( $status ) ) {
			                        return [
				                        'status' => 202,
			                        ];
		                        }
		                        throw new UnprocessableEntityHttpException();
	                        }
	                        throw new BadRequestHttpException(Yii::t('app', "Błędna wartość parametru status"));
                        }
                        throw new BadRequestHttpException(Yii::t('app', "Brak wymaganych parametrów: comment lub status"));
                    }
                    throw new ForbiddenHttpException(Yii::t('app', "Nie masz uprawnień do tego zadania"));
                }
                throw new NotFoundHttpException(Yii::t('app', "Nie znaleziono zadania"));
            }
            throw new BadRequestHttpException(Yii::t('app', "Brak wymaganych parametrów: id"));
        }
        throw new MethodNotAllowedHttpException();
    }


    public function actionEvents($type) {
        if (Yii::$app->request->isGet) {
            $dashboard = new Dashboard();
            if ($type == 'today') {
                return $dashboard->getTodayEvents();
            }
            if ($type == 'department') {
                return $dashboard->getDepartmentEvents();
            }
            if ($type == 'upcoming') {
                return $dashboard->getUpcomingEvents();
            }
            throw new UnprocessableEntityHttpException();
        }
        throw new MethodNotAllowedHttpException();
    }

	private function loadChecklist() {
    	return [
    		'Checklist' => [
			    'name' => Yii::$app->request->post("name"),
			    'done' => Yii::$app->request->post("done"),
			    'deadline' => Yii::$app->request->post("deadline"),
			    'priority' => Yii::$app->request->post("priority"),
		    ]
	    ];
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
 * post: /admin/api/dashboard/tasks/{id}/status // && $_POST['status'] - zmienia status na: status = 0 => nowy, status = 10 => zrobiony // return status 200 / error
 * post: /admin/api/dashboard/tasks/{id}/comment //  && $_POST['comment'] - dodaje komentarz // return status 200 / error
 *
 * Checklista:
 * get: /admin/api/dashboard/checklist -> pobieramy listę checklist użytkownika
 * get: /admin/api/dashboard/checklist/{id} -> pobieramy checkliste o podanym id
 * post: /admin/api/dashboard/checklist/{id}/status -> zmienia status
 * post /admin/api/dashboard/checklist/{id}/delete -> usunięcie
 * post: /admin/api/dashboard/checklist/{id} -> update. W body param. checklist = {"name":"Kupić ogórki","deadline":"2017-12-07 11:50:33"}  return {status: 200, model: zaktualizowany_obiekt}
 * post: /admin/api/dashboard/checklist -> dodanie. W body parametry: id,name,deadline,done return {status: 200, model: dodany_obiekt}
 *
 * Zdjęcie użytkownika:
 * get: /admin/api/users/{id}/photo return string/error
 *
 * Logowanie:
 * post: /admin/api/user/login
 *
 * Qr i Bar cody
 * post: /admin/api/gear/{code} && $_POST['user_id']
 * jeżeli zeskanowane urządzenie to egzemplarz bez egzemplarza, to skanowanie nie zapisuje się,
 * za to czeka na informację ile sztuk zostało zeskanowanych pod adresem:
 * post: /admin/api/gear/scan-no-items/{id_urzadzenia} i w body parametr quantity, czyli liczba zeskanowanych egzemplarzy
 *
 */