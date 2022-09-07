<?php
namespace frontend\modules\api\controllers;

use common\models\QuestionImage;
use Yii;
use frontend\modules\api\components\BaseController;
use yii\web\UploadedFile;
use yii\db\Expression;

class QuestionImageController extends BaseController
{
    public $modelClass = 'common\models\QuestionImage';

    public function actionUpload()
    {

        $model = new QuestionImage();

        $model->file = UploadedFile::getInstanceByName('userfile');
        $model->filename = md5(time().mt_rand()).'.'.$model->file->extension;


        if ($model->file && $model->validate())
        {
            $model->objectUUID = new Expression('UUID()');

            $fileName = $model->filename;
            $filePath = \Yii::getAlias('@uploadroot').'/' . $fileName;
            if($model->file->saveAs($filePath) && $model->save(false))
            {
                $model->refresh();
            }

        }
        else
        {
            Yii::info($model->errors, 'api');
            return $model->errors;
        }

        return $model;
    }

}