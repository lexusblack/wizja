<?php
namespace backend\models;

use Yii;
use yii\base\Model;



class ReportForm extends Model
{
    public $date_from;
    public $date_to;


    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['date_from', 'date_to'], 'safe'],
        ];
    }
    

	
	public function attributeLabels()
	{
		return [
			'filename'=>Yii::t('app', 'Plik do importu')
		];
	}
}
