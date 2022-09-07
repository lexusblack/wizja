<?php
namespace backend\actions;

use Yii;
use yii\behaviors\SluggableBehavior;
use yii\db\Expression;
use yii\helpers\Inflector;
use yii\helpers\Json;
use yii\web\JsonResponseFormatter;
use yii\web\Response;
use yii\web\UploadedFile;
use Zelenin\yii\behaviors\Service\Slugifier;
use yii\web\HttpException;
 use yii\imagine\Image;  
 use Imagine\Image\Box; 

class UploadAction extends \yii\base\Action
{


	public $fileName = 'file';
	public $upload = '';

	public $afterUploadHandler = null;
	public $afterUploadData = null;

	protected $uploadDir = '';
	protected $uploadSrc = '';

	public function init()
	{
		parent::init();
		Yii::$app->response->format = Response::FORMAT_JSON;
		$this->uploadDir = Yii::getAlias('@uploadroot' . $this->upload . '/');
		$this->uploadSrc = Yii::getAlias('@uploads' . $this->upload . '/');
		if (!file_exists($this->uploadDir))
		{
			mkdir($this->uploadDir);
		}
	}

	public function setUpload($upload)
	{
		$this->upload = $upload;

		$this->uploadDir = Yii::getAlias('@uploadroot' . $this->upload . '/');
		$this->uploadSrc = Yii::getAlias('@uploads' . $this->upload . '/');
	}

	public function run()
	{
		$file = UploadedFile::getInstanceByName($this->fileName);
		if ($file->hasError) {
			throw new HttpException(500, 'Upload error');
		}


        $baseName = Inflector::slug($file->baseName);
        $fileName = $baseName.'.'.$file->extension;
        $i = 1;
		while (file_exists($this->uploadDir . $fileName)) {
			$fileName = $baseName . '-' . $i . '.' . $file->extension;
			$i++;
		}
		$file->saveAs($this->uploadDir . $fileName);

		$response = [
			'filename' => $fileName,
			'name' => $baseName,
			'extension' => $file->extension,
			'type' => $file->type,
		];

		if ((($file->extension == 'jpg')||($file->extension== 'png'))&&(($this->upload=='/gear')||($this->upload=='/outer-gear')||($this->upload=='/gear-item')||($this->upload=='/user')))
		{
			$this->smallImage($fileName);
		}

		if (isset($this->afterUploadHandler)) {
			$data = [
				'data' => $this->afterUploadData,
				'file' => $file,
				'dirName' => $this->uploadDir,
				'src' => $this->uploadSrc,
				'filename' => $fileName,
				'params' => Yii::$app->request->post(),
				'response'=>$response,
			];

			if ($result = call_user_func($this->afterUploadHandler, $data)) {
				$response['afterUpload'] = $result;
			}
		}

		return $response;
	}

	public function smallImage($fileName)
	{
		Image::thumbnail($this->uploadDir . $fileName, 500, 500)
                ->resize(new Box(500,500))
                ->save($this->uploadDir . $fileName, 
                        ['quality' => 70]);
	}
}
