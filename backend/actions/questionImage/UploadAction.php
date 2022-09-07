<?php
namespace backend\actions\questionImage;

use common\models\Question;
use Yii;
use common\models\QuestionImage;
use yii\db\Expression;
use yii\web\UploadedFile;



class UploadAction extends \yii\base\Action
{

	
	public function run()
	{
		$model = new QuestionImage();


        if ($model->load(Yii::$app->request->post())) {

            $model->file = UploadedFile::getInstance($model, 'file');
			$model->filename = md5(time().rand()).'.'.$model->file->extension;

			if ($model->file && $model->validate())
			{
				$model->objectUUID = new Expression('UUID()');

            	$fileName = $model->filename;
				$filePath = \Yii::getAlias('@uploadroot').'/' . $fileName;
                if($model->file->saveAs($filePath) && $model->save(false))
				{
					$model->refresh();
					$parent = Question::findOne($model->questionId);
					$parent->imageUUID = $model->objectUUID;
					$parent->hasImage = 1;
					if($parent->save() == false)
					{
						print_r($parent->errors);
						print_r($parent->attributes);
					}


				}

            }
			else
			{
				print_r($model->errors);
				print_r($model->attributes);
			}
        }
		
	}
}
