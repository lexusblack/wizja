<?php

namespace common\models;

use common\helpers\ArrayHelper;
use Yii;
use \common\models\base\InvoiceSerie as BaseInvoiceSerie;
use yii\db\Expression;

/**
 * This is the model class for table "invoice_serie".
 */
class InvoiceSerie extends BaseInvoiceSerie
{

    const RESET_YEARLY = 'yearly';
    const RESET_MONTHLY = 'monthly';
    const RESET_DAILY = 'daily';


    public static function getListForType($type=null)
    {
        $models = InvoiceSerie::find()
            ->andFilterWhere(['type'=>$type])
            ->all();

        $list = ArrayHelper::map($models, 'id', 'name');

        return $list;
    }

    public static function resetNumberPeriodList()
    {
        $list = [
            'yearly'=>Yii::t('app', 'Co rok'),
            'monthly' => Yii::t('app', 'Co miesiąc'),
            'daily' => Yii::t('app', 'Codziennie'),
        ];
        return $list;
    }

    public function getTypeLabel()
    {
        $list = Invoice::getTypeList();
        $index = $this->type;
        return ArrayHelper::getValue($list, $index, UNDEFINDED_STRING);
    }

    public function getResetNumberPeriodLabel()
    {
        $list = static::resetNumberPeriodList();
        $index = $this->reset_number_period;
        return ArrayHelper::getValue($list, $index, UNDEFINDED_STRING);
    }


    /**
     * @param $model Invoice;
     * @return string Number
     */
    public function getFullNumber($model)
    {
        $date = \DateTime::createFromFormat('Y-m-d', $model->year.'-'.$model->month.'-'.$model->day);
        $pattern = $this->pattern;
        $search = [
            '@\[numer\]@',
            '@\[dzień\]@',
            '@\[miesiąc\]@',
            '@\[rok\]@',
            '@\[dzień_roku\]@',
            '@\[rok\:format_dwucyfrowy\]@',
        ];
        $replace = [
            $model->number,
            $model->day,
            $model->month,
            $model->year,
            $date->format('z'),
            $date->format('y'),
        ];

        $number = preg_replace($search, $replace, $pattern);



        return $number;
    }

    /**
     * @param $model Invoice;
     * @return string Number
     */
    public static function getDefaultFullNumber($model)
    {
        $serie = static::getDefaultSerie();
        return $serie->getFullNumber($model);
    }

    public static function getDefaultSerie()
    {
        $model = new static();
        $model->pattern = '[rok]/[miesiąc]/[dzień]/[numer]';
        $model->reset_number_period = static::RESET_YEARLY;
        $model->start_number = 1;
        return $model;
    }

    /**
     * @param $model Invoice;
     * @return int
     */
    public static function getNextNumber($model)
    {
        if (empty($model->series_id) == true)
        {
            $model->series_id=null;
        }

        $query = Invoice::find()->select(new Expression('MAX(number)'))
            ->where([
                'type'=>$model->type,
                'series_id'=>$model->series_id,
            ]);

        $series = $model->series;
        if ($series === null)
        {
            $series = static::getDefaultSerie();
        }

        switch ($series->reset_number_period)
        {
            case static::RESET_DAILY;
                $query->andWhere(['day'=>$model->day]);
            case static::RESET_MONTHLY;
                $query->andWhere(['month'=>$model->month]);
            case static::RESET_YEARLY;
                $query->andWhere(['year'=>$model->year]);
                break;
        }

        $number = (int)$query->scalar();
        if ($number == 0)
        {
            $number = $series->start_number;
        }
        else
        {
            $number++;
        }



        return $number;

    }
}
