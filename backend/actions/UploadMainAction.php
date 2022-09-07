<?php
namespace backend\actions;

use Yii;


class UploadMainAction extends UploadAction
{

	public function init()
	{
		parent::init();
		$this->uploadDir = Yii::getAlias('@uploadrootAll' . $this->upload . '/');
		$this->uploadSrc = Yii::getAlias('@uploadsAll' . $this->upload . '/');
	}

	public function setUpload($upload)
	{
		$this->upload = $upload;

		$this->uploadDir = Yii::getAlias('@uploadrootAll' . $this->upload . '/');
		$this->uploadSrc = Yii::getAlias('@uploadsAll' . $this->upload . '/');
	}
}
