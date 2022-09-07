<?php
use yii\bootstrap\Modal;
use yii\bootstrap\Html;
$c = Yii::$app->getRequest()->getQueryParam('c');
$s = Yii::$app->getRequest()->getQueryParam('s');
$activeModel = Yii::$app->getRequest()->getQueryParam('activeModel');

Modal::begin([
    'id' => 'barcodes-generator',
    'header' => Yii::t('app', 'Wygeneruj etykiety z kodami'),
    'class' => 'modal',
    'options' => [
        'tabindex' => false,
    ],
    'clientOptions' => [
    'keyboard'=> false,
        'backdrop'=> 'static'
    ]
]);
echo "<div class='modalContent'></div>";
Modal::end();
?>

<div class="row">
    <div class="tools warehouse-tools col-md-12">

        <?php
            echo Html::a(Yii::t('app', 'Kalendarz'), ['/gear/calendar'], ['class'=>'btn btn-primary btn-xs calendar-button-all'])." ";
            echo Html::a(Yii::t('app', 'Zestawienie'), ['/gear/wizja-all'], ['class'=>'btn btn-primary btn-xs wizja-button-all'])." ";
            echo Html::a(Yii::t('app', 'Inwentaryzacja'), ['stocktakings'], ['class'=>'btn btn-primary btn-xs'])." ";
            if (Yii::$app->user->can('gearOurWarehouseCreateCase')) {
                echo Html::a(Yii::t('app', 'Utwórz case'), ['group-create'], ['class'=>'btn btn-success btn-xs group-create'])." ";
            }
            if (Yii::$app->user->can('gearSetCreate')) {
                echo Html::a(Yii::t('app', 'Utwórz zestaw'), ['gear-set/create'], ['class'=>'btn btn-success btn-xs gear-create'])." "; 
            }
            
            if (Yii::$app->user->can('gearCreate')) {
                echo Html::a(Yii::t('app', 'Dodaj model'), ['gear/create'], ['class' => 'btn btn-success btn-xs gear-create'])." "; 
            }
            if (Yii::$app->user->can('gearOurWarehouseAddFromGearBase')) {
                echo Html::a(Yii::t('app', 'Dodaj z bazy sprzętu'), ['gear-model/index'], ['class'=>'btn btn-success btn-xs gear-create'])." "; 
            } 
            if ($s)
            {
                $cat = $s;
            }else{
                $cat = $c;
            }
            echo Html::a(Yii::t('app', 'Eksport do .xls'), ['warehouse/excel', 'id'=>$cat], ['class'=>'btn btn-success btn-xs gear-create', 'target'=>'_blank'])." "; 
            echo Html::a(Yii::t('app', 'Naklejki z kodami'), ['warehouse/get-codes', 'c'=>$c, 's'=>$s], ['class'=>'btn btn-success btn-xs barcodes-generator', 'target'=>'_blank'])." "; 
             
            if (Yii::$app->session->get('gear-photos')==1)
            {
                echo Html::a(Yii::t('app', 'Ukryj zdjęcia'), [Yii::$app->controller->action->id, 'photos'=>2, 'c'=>$c, 's'=>$s, 'activeModel'=>$activeModel], ['class'=>'btn btn-success btn-xs gear-create'])." "; 
             
            }else{
                echo Html::a(Yii::t('app', 'Pokaż zdjęcia'), [Yii::$app->controller->action->id, 'photos'=>1, 'c'=>$c, 's'=>$s, 'activeModel'=>$activeModel], ['class'=>'btn btn-success btn-xs gear-create'])." ";                 
            }
            ?>
    </div>
</div>

<?php
$this->registerJs('
$(".barcodes-generator").click(function(e){
        e.preventDefault();
        $("#barcodes-generator").modal("show").find(".modalContent").load($(this).attr("href"));
});
$("a.group-create").on("click", function(e){
    e.preventDefault();
    var n = $(".grid-view-items").length;
    if (n==0)
    {
         alert("'.Yii::t('app', 'Brak zaznaczonych egzemplarzy').'");
        return;       
    }
    var ids = $(".grid-view-items").yiiGridView("getSelectedRows");
    if(ids.length==0) 
    {
        alert("'.Yii::t('app', 'Brak zaznaczonych egzemplarzy').'");
        return;
    }
    var params = $.param({"id[]":ids}, true);
    var url = this.href + "?" + params;
    //window.location = url;
    
    var container = $(this).closest("[data-pjax-container]");
    $.get(url, {}, function(response){
        window.location.href = "/admin/gear-group/update?id="+response;
    });

    
    return false;
});

',\yii\web\View::POS_END, 'group-create-click');


