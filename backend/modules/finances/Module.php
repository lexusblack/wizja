<?php
namespace backend\modules\finances;

use common\helpers\ArrayHelper;
use common\models\Expense;
use common\models\GearItem;
use common\models\Invoice;
use common\models\VatRate;
use Yii;

/**
 * finances module definition class
 */
class Module extends \yii\base\Module
{
    /**
     * @inheritdoc
     */
    public $controllerNamespace = 'backend\modules\finances\controllers';

    /**
     * @inheritdoc
     */
    public function init()
    {
        parent::init();

        // custom initialization code goes here
    }

    public static function getInvoiceSubitems()
    {
        $items = [];
        foreach (Invoice::getTypeList() as $k=>$v)
        {
            $items[] = [
                'label' => $v,
                'url'=>['invoice/create', 'type'=>$k],
                'options'=>[
                    'data-select'=>(in_array($k, [Invoice::TYPE_CORRECTION_ITEMS, Invoice::TYPE_CORRECTION_DATA]) ? 1 : 0),
                ]
            ];
        }
        return $items;
    }

    public static function getExpenseSubitems()
    {
        $items = [];
        foreach (Expense::getTypeList() as $k=>$v)
        {
            $items[] = [
                'label' => $v,
                'url'=>['expense/create', 'type'=>$k],
            ];
        }
        return $items;
    }

    public static function getCurrencyList()
    {
        $list = [
            'PLN' => Yii::t('app', 'złoty'),
            'EUR' => Yii::t('app', 'euro'),
            'USD' => Yii::t('app', 'dolar amerykański'),
            'SEK' => Yii::t('app', 'Korona szwedzka')
        ];
        return $list;
    }

    public static function getVatList()
    {
        $models = VatRate::find()
            ->orderBy(['value'=>SORT_DESC])
            ->all();
        $list = [];
        foreach ($models as $model)
        {
            $list[$model->value] = \Yii::$app->formatter->asPercent($model->value/100);
        }

        return $list;
    }

    public static function getVatList2()
    {
        $models = VatRate::find()
            ->orderBy(['value'=>SORT_DESC])
            ->all();
        $list = [];
        foreach ($models as $model)
        {
            $list[number_format($model->value, 3)] = \Yii::$app->formatter->asPercent($model->value/100);
        }

        return $list;
    }

    public static function getLanguageList()
    {
        $list = [
            'pl'=> Yii::t('app', 'Polski'),
        ];
        return $list;
    }

    public static function listItems()
    {
        $data = GearItem::getList();
        $out = [];
        foreach ($data as $id => $model) {
            $out['gi_' . $model->id] = $model->name . ' [' . $model->number . ']';
        }

        return $out;

    }
}
