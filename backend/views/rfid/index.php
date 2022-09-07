<?php


use kartik\tabs\TabsX;

$tabItems = [
    [
        'label'=>Yii::t('app', 'Egzemplarze'),
        'url' => ['rfid/index'],
        'active' => $activeModels,
    ],
    [
        'label'=>Yii::t('app', 'Modele bez egzemplarzy'),
        'url' => ['rfid/index?tab=nomodels'],
        'active' => $activeNoModels,
    ],
    [
        'label'=>Yii::t('app', 'Case'),
        'url' => ['rfid/index?tab=case'],
        'active' => $activeCase
    ],
    [
        'label'=>Yii::t('app', 'Ostatni zeskanowany kod RFID'),
        'url' => ['rfid/index?tab=lastcode'],
        'active' => $activeLastCode
    ]
];


echo TabsX::widget([
    'items'=>$tabItems,
    'encodeLabels'=>false,
    'enableStickyTabs'=>true,
    'options' => [
        'id' => 'tabs'
    ]
]);

echo $this->render($viewText, [
    'gearItemSearchModel' => $gearItemSearchModel,
    'gearItemDataProvider' => $gearItemDataProvider,
    'casesDataProvider' => $casesDataProvider,
    'casesSearchModel' => $casesSearchModel,
    'gearItemNoItemsDataProvider' => $gearItemNoItemsDataProvider,
    'lastRfidCode' => $lastRfidCode
]);

$this->registerCss("
    #tabs { margin-bottom: 40px; }
");

$this->registerJs('

$(document).ready(function() { 
   
   $(".pagination a").click(function(e){
        if (window.location.hash) {
            window.location.href = $(this).attr("href") + window.location.hash;
            e.preventDefault();
            return false;
        }
   });
   
});

');

?>
