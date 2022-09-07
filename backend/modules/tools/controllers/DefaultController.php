<?php

namespace backend\modules\tools\controllers;

use common\models\Event;
use common\models\EventExpense;
use common\models\Invoice;
use yii\web\Controller;

/**
 * Default controller for the `tools` module
 */
class DefaultController extends Controller
{
    /**
     * Renders the index view for the module
     * @return string
     */
    public function actionIndex()
    {
        return $this->render('index');
    }

    public function actionFix()
    {
        $models = EventExpense::find()->all();
        foreach ($models as $model)
        {
            if ($model->department !== null)
            {
                $model->updateAttributes([
                    'section'=>$model->department->name,
                ]);

            }
        }
        return 'ok';
    }

    public function actionMail()
    {
        \Yii::$app->mailer->compose()

            ->setFrom('no-reply@events.newsystems.pl')
            ->setTo('przemyslaw.pasieka@softwebo.pl')
            ->setSubject('test')
            ->send();
    }

    public function actionProjectStatus()
    {
        $models = Event::find()->all();
        foreach ($models as $model)
        {
            $model->save();
        }
    }

    public function actionInvoiceOwner()
    {
    	$models = Invoice::find()->all();
    	foreach ($models as $model)
	    {
	    	if($model->event_id!=null)
		    {
		    	$model->owner_id = $model->event_id;
		    	$model->owner_class = Event::className();
		    	$model->owner_type = Invoice::OWNER_TYPE_EVENT;
		    	$model->save();
		    }
	    }
    }
}
