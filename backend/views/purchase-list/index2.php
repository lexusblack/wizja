<?php

/* @var $this yii\web\View */
/* @var $searchModel common\models\PurchaseListSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

use yii\helpers\Html;
use kartik\tabs\TabsX;

$this->title = Yii::t('app', 'Listy zakupowe');
$this->params['breadcrumbs'][] = $this->title;
?>
  <?php
$tabItems = [
                [
                    'label'=>Yii::t('app', 'Kalendarz'),
                    'visible'=>true,
                    'url' => ['/site/calendar-produkcja'],
                    'active'=>false,
                ],
                                [
                    'label'=>Yii::t('app', 'Zakupy bez wybranego dostawcy')." ".\common\models\PurchaseList::getNoCompanyLabel(),
                    'visible'=>true,
                    'url' => ['/order/purchase', 'page'=>'no-company'],
                    'active'=>false,
                ],
                                [
                    'label'=>Yii::t('app', 'Zakupy z wybranym dostawcą')." ".\common\models\PurchaseList::getCompanyLabel(),
                    'visible'=>true,
                    'url' => ['/order/purchase', 'page'=>'company'],
                    'active'=>false,
                ],
                [
                    'label'=>Yii::t('app', 'Listy zakupowe'),
                    'visible'=>true,
                    'content'=>$this->render('index', [
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
        ]),
                  'active'=>true,  
                ],
];
  echo TabsX::widget([
                'items'=>$tabItems,
                'id'=>'calendarTabs',
                'encodeLabels'=>false,
                'enableStickyTabs'=>false,
            ]);

            ?>
