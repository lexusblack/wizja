<?php

namespace backend\modules\i18n\controllers;
use common\components\filters\AccessControl;
use common\models\Language;
use common\models\Message;
use Yii;
use backend\components\Controller;

/**
 * Default controller for the `i18n` module
 */
class DefaultController extends Controller
{
    public function behaviors()
    {
        return [
            'access' => [
                'class' => AccessControl::className(),
                'rules' => [
                    [
                        'allow'=>true,
                        'actions' => ['index'],
                        'roles' => ['settingsLanguage'],
                    ],
                    [
                        'allow' => true,
                        'actions' => ['refresh'],
                        'roles' => ['settingsLanguageRefresh'],

                    ],
                ],
            ],
        ];
    }

    public function init()
    {
        $this->layout = static::LAYOUT_PANEL;
        Yii::$app->view->params['active_tab'] = 9;
        parent::init();
    }
    /**
     * Renders the index view for the module
     * @return string
     */
    public function actionIndex()
    {
        return $this->render('index');
    }

    public function actionRefresh($debug=0)
    {
	    $path = \Yii::getAlias('@webroot/../../..');
//	    $path = Yii::$app->params['server.php.pathPrefix'].$path;
//	    var_dump($path); die;
	    $command = Yii::$app->params['server.php.command'];
        $commandText = $command.' '.$path.'/yii message '.$path.'/common/messages/config2.php';
        exec($commandText,$output, $retval);
        $msg = array_slice($output, -12);
        $msg = array_filter($msg);
        Yii::$app->session->setFlash('success', Yii::t('app', 'Nowe tłumaczenia załadowane'));
        if ($debug==0)
        {
	        return $this->redirect(['default/index']);
        }
        else
        {
	        return $this->render('refresh', ['msg' => $msg]);
        }
    }

    public function actionFill()
    {
        $languages = Language::getTranslationList();
        foreach ($languages as $language=>$name)
        {
            $data = include Yii::getAlias('@common/messages/'.$language.'/app.php');
            foreach ($data as $k=>$v)
            {
                $model = Message::find()
                    ->innerJoinWith('source')
                    ->where([
                        'message'=>$k,
                        'language'=>$language,
                    ])
                    ->one();

                if ($model!== null)
                {
                    $model->updateAttributes([
                        'translation'=>$v,
                    ]);

                }

            }
        }

    }
}
