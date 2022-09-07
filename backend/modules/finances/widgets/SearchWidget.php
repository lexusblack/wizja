<?php
namespace backend\modules\finances\widgets;

use Yii;
use backend\modules\finances\models\FinanceSearch;
use yii\base\Widget;

class SearchWidget extends Widget
{
    public $model;

    public function init()
    {
        if (empty($this->model->qOptions) == true)
        {
            $this->model->qOptions = array_keys(static::getOptionList());
        }
        parent::init();
    }

    public function run()
    {
        $model = $this->model;
        $params = \Yii::$app->request->post();
        if ($model->load($params))
        {

        }
        return $this->render('search', [
            'model'=>$model,
        ]);
    }

    public static function getOptionList()
    {
        $list = [

//            NIP, Kontrahent, Wydarzenie, numer faktury, data faktury, nazwa towaru
            'nip'=>Yii::t('app', 'NIP'),
            'customer'=>Yii::t('app', 'Kontrahent'),
            'event'=>Yii::t('app', 'Wydarzenie'),
            'number'=>Yii::t('app', 'Numer faktury'),
            'date'=>Yii::t('app', 'Data faktury'),
            'name'=>Yii::t('app', 'Nazwa towaru'),
        ];
        return $list;
    }
}