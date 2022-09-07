<?php
namespace backend\models;

use Yii;
use yii\base\Model;
/**
 * Login form
 */
class MaskCreator extends Model
{
    public $width;
    public $height;
    public $cols;
    public $rows;
    public $color;


    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            // username and password are both required
            [['width', 'height', 'cols', 'rows', 'color'], 'required'],
            [['width', 'height', 'cols', 'rows', 'color'], 'integer'],
        ];
    }

	
	public function attributeLabels()
	{
		return [
			'width'=>Yii::t('app', 'Szerokość modułu [px]'),
			'height'=>Yii::t('app', 'Wysokość modułu [px]'),
			'cols'=>Yii::t('app', 'Liczba kolumn'),
            'rows'=>Yii::t('app', 'Liczba wierszy'),
            'color'=>Yii::t('app', 'Kolor tła'),
		];
	}
}
