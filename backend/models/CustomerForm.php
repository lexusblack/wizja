<?php
namespace backend\models;

use Yii;
use yii\base\Model;
use yii\web\UploadedFile;



class CustomerForm extends Model
{
    public $filename;


    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['filename'], 'file', 'skipOnEmpty' => false, 'extensions' => 'xlsx'],
        ];
    }
    
    public function upload()
    {
        if ($this->validate()) {
            $this->filename->saveAs(Yii::getAlias('@uploadroot/xls/'.$this->filename->baseName . '.' . $this->filename->extension));
            return true;
        } else {
            return false;
        }
    }

	
	public function attributeLabels()
	{
		return [
			'filename'=>Yii::t('app', 'Plik do importu')
		];
	}
}
