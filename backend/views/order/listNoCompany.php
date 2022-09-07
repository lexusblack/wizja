<?php

use common\components\grid\GridView;
use kartik\helpers\Enum;
use yii\bootstrap\Html;
use kartik\tabs\TabsX;

/* @var $this yii\web\View */
/* @var $searchModel common\models\OuterGearSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = Yii::t('app', 'Wypożyczenia i konflikty');
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="order-index">
        <div class="tabs-container">
            <?php 
            //!!!: Zmiana zakładek -> zmiana indexu w js (google maps)
            $tabItems = [
                [
                    'label'=>Yii::t('app', 'Konflikty').'<span class="badge badge-danger pull-right">'.$conflictsCount.'</span>',
                    'url' => ['/order/list'],
                ],
                [
                    'label'=>Yii::t('app', 'Sprzęt bez wybranej wypożyczalni').'<span class="badge badge-warning pull-right">'.$noCompanyCount.'</span>',
                    'content'=>$this->render('_tabNoCompany', ['dataProvider2' => $dataProvider,'searchModel2' => $searchModel, ]),
                    'options'=> [
                        'id'=>'tab-no-company',
                    ],
                    'active'=>true,
                ],
                [
                    'label'=>Yii::t('app', 'Sprzęt do zamówienia'),
                    'url' => ['/order/list', 'page'=>'withCompany'],
                    
                ],
                [
                    'label'=>Yii::t('app', 'Zamówienia'),
                    'url' => ['/order/list', 'page'=>'orders'],
                ]
                ];
                echo TabsX::widget([
                    'items'=>$tabItems,
                    'encodeLabels'=>false,
                    'enableStickyTabs'=>true,
                ]);
                ?>
        </div>
</div>