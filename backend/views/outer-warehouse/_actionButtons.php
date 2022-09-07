<?php
use yii\bootstrap\Html;
$user = Yii::$app->user;
?>
<div class="row">
    <div class="tools warehouse-tools col-md-12">
        <?php
        if ($user->can('')) {
            Html::a( Yii::t('app', 'Utwórz zestaw'), ['group-create'], ['class'=>'btn btn-success btn-xs group-create']);
        }
        if ($user->can('')) {
            echo Html::a( Yii::t('app', 'Dodaj model'), ['outer-gear/create'], ['class' => 'btn btn-success btn-xs gear-create']);
        } ?>

    </div>
</div>

<?php
$this->registerJs('
$("a.group-create").on("click", function(e){
    e.preventDefault();

    var ids = $(".grid-view-items").yiiGridView("getSelectedRows");
    if(ids.length==0) 
    {
        return;
    }
    var params = $.param({"id[]":ids}, true);
    var url = this.href + "?" + params;
    //window.location = url;
    
    var container = $(this).closest("[data-pjax-container]");
    $.get(url, {}, function(response){
        $.pjax.reload("#" + container.attr("id"), {
            push: false,
            replace: true,
        });
    });

    
    return false;
});

',\yii\web\View::POS_END, 'group-create-click');


