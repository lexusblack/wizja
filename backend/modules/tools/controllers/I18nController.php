<?php
namespace backend\modules\tools\controllers;

use backend\components\Controller;
use backend\modules\tools\models\I18n;
use Yii;
use yii\helpers\VarDumper;


class I18nController extends Controller
{
    public $layout = '@backend/themes/e4e/layouts/main-panel';

    public function actionLoad($lang='en', $cat='app')
    {
        $path = \Yii::getAlias('@webroot/../../..');
        exec('php '.$path.'/yii message '.$path.'/common/messages/config.php',$output, $retval);
        $msg = array_slice($output, -12);
        $msg = array_filter($msg);

        $data = require_once(\Yii::getAlias('@common/messages/'.$lang.'/'.$cat.'.php'));
        $data = array_merge([Yii::t('app', 'Oryginalny tekst')=>Yii::t('app', 'Tłumaczenie').' '.strtoupper($lang)], $data);
        \Yii::$app->session->setFlash('info', Yii::t('app', 'Załadowane'));
        return $this->render('load', [
            'data'=>$data,
            'lang'=>$lang,
            'msg'=>$msg,
        ]);

    }

    public function actionSave($lang='en', $cat='app')
    {
        $model = new I18n();
        $model->lang = $lang;
        if ($model->load(\Yii::$app->request->post()) && $model->validate())
        {
            $texts = explode("\n",$model->text);
            unset($texts[0]);
            foreach ($texts as $k=>$text)
            {

                $text = preg_replace('@^([^\t]+)\t(.*)[\n\r\s]*$@', '\'$1\'=>\'$2\',', $text);
                $texts[$k] = preg_replace(['@\r@','@\r@'], '', $text);
            }
            $data = implode("\n",$texts);
            $data = "<?php\nreturn [\n$data\n];\n";

            $filename = \Yii::getAlias('@common/messages/'.$lang.'/'.$cat.'.php');
            file_put_contents($filename, $data);
            \Yii::$app->session->setFlash('success', Yii::t('app', 'Zapisane'));
//            return $this->refresh();
        }

        return $this->render('save', [
            'model'=>$model,

        ]);
    }

    public function actionCopy()
    {

    }
}