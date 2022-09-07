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
  if ($page=='company')
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
                    'content'=>$this->render('_tabCompany2', ['dataProvider2' => $dataProvider2]),
                    'active'=>true,
                ],
                [
                    'label'=>Yii::t('app', 'Listy zakupowe'),
                    'visible'=>true,
                    'url' => ['/purchase-list'],
                    'active'=>false,
                ],
];
else
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
                    'content'=>$this->render('_tabNoCompany2', ['dataProvider2' => $dataProvider2]),
                    'active'=>true,
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
                    'url' => ['/purchase-list'],
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