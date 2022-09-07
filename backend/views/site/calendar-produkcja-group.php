<?php
/* @var $this yii\web\View */
use yii\helpers\Url;
use yii\bootstrap\Html;
use yii\bootstrap\Modal;
use kartik\tabs\TabsX;


$this->title = Yii::t('app', 'Kalendarz produkcji');
$this->params['breadcrumbs'][] = $this->title;

        
    ?>

  <?php
$tabItems = [
                [
                    'label'=>Yii::t('app', 'Kalendarz'),
                    'content'=>$this->render('calendar-produkcja',  ['events'=>$events, 'eventsArray'=>$eventsArray, 'colors'=>$colors, 'model'=>$model, 'projects'=>$projects]),
                    'visible'=>true,
                    'options'=> [
                        'id'=>'tab-calendar',
                    ],
                    'active'=>true,
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
                    'url' => ['/purchase-list'],
                    'visible'=>true,
                    'active'=>false,
                ],
];
  echo TabsX::widget([
                'items'=>$tabItems,
                'id'=>'calendarTabs',
                'encodeLabels'=>false,
                'enableStickyTabs'=>false,
            ]);

            ?>