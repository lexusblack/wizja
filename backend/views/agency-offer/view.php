<?php

use yii\bootstrap\Html;
use yii\helpers\Url;
use yii\bootstrap\Modal;

use yii\widgets\DetailView;
use kartik\grid\GridView;
use kartik\form\ActiveForm;
$formatter = Yii::$app->formatter;
$user = Yii::$app->user;
/* @var $this yii\web\View */
/* @var $model common\models\AgencyOffer */

$this->title = $model->name;
$this->params['breadcrumbs'][] = ['label' => 'Oferty', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
Modal::begin([
    'id' => 'new-service-category',
    'header' => Yii::t('app', 'Dodaj grupę usług'),
    'class' => 'modal',
    'options' => [
        'tabindex' => false,
    ],
]);
echo "<div class='modalContent'></div>";
Modal::end();
Modal::begin([
    'id' => 'edit-service-category',
    'header' => Yii::t('app', 'Edytuj grupę usług'),
    'class' => 'modal',
    'options' => [
        'tabindex' => false,
    ],
]);
echo "<div class='modalContent'></div>";
Modal::end();


$this->registerJs('
    $(".add-service").click(function(e){
        e.preventDefault();
        $("#new-service").modal("show").find(".modalContent").load($(this).attr("href"));
        var data = [];
        $.ajax({
            data: data,
            type: "POST",
            url: $(this).attr("href"),
            success: function (data) {
            addNewRow(data);
        },
        });
    });
');

$this->registerJs('
    $(".add-service-category").click(function(e){
        e.preventDefault();
        $("#new-service-category").modal("show").find(".modalContent").load($(this).attr("href"));
    });
');

$this->registerJs('
    $(".edit-service-category").click(function(e){
        e.preventDefault();
        $("#edit-service-category").modal("show").find(".modalContent").load($(this).attr("href"));
    });
');

?>
<div class="agency-offer-view">
    <div class="row">
        <div class="col-xs-12">
        <div class="ibox float-e-margins">
                <div class="ibox-title">
                <h5><?=Yii::t('app', 'Nazwa projektu: ').$model->name?></h5>
                                    <div class="ibox-tools">
                                    <?php if ($model->event_id){
                                        echo Html::a(Yii::t('app', 'Zobacz event'), ['/event/view', 'id'=>$model->event_id], ['class'=>'btn btn-success btn-sm'])." ";
                                        }else{
                                           echo Html::a(Yii::t('app', 'Stwórz event'), ['/agency-offer/create-event', 'id'=>$model->id], ['class'=>'btn btn-success btn-sm'])." "; 
                                            } ?>
                                    <?= Html::a(Yii::t('app', 'Wyśli E-mailem'), ['/agency-offer/send-mail', 'id'=>$model->id], ['class'=>'btn btn-success btn-sm'])." "; ?>
                                    <?php
                                    echo Html::a(Yii::t('app', 'PDF'), ['/agency-offer/pdf', 'id'=>$model->id], ['class'=>'btn btn-sm btn-default download-pdf', 'target' => '_blank'])." ";
                                    echo Html::a(Yii::t('app', 'XLS'), ['/agency-offer/excel', 'id'=>$model->id], ['class'=>'btn btn-sm btn-default download-pdf', 'target' => '_blank'])." ";
                                    ?>
                                    <?= Html::a('Edytuj', ['update', 'id' => $model->id], ['class' => 'btn btn-primary btn-sm']) ?>
                                    <?= Html::a('Usuń', ['delete', 'id' => $model->id], [
                                        'class' => 'btn btn-danger btn-sm',
                                        'data' => [
                                            'confirm' => 'Are you sure you want to delete this item?',
                                            'method' => 'post',
                                        ],
                                    ])
                                    ?>
                                    <?php if ($user->can('menuOffersViewDuplicate')){ echo Html::a('<i class="fa fa-copy"></i>', ['/agency-offer/duplicate', 'id' => $model->id], ['class'=>'btn btn-warning btn-circle']);} ?>
                                    <a class="collapse-link">
                                        <i class="fa fa-chevron-up"></i>
                                    </a>
                            </div>
                </div>
                <div class="ibox-content">
                    <div class="row">
                        <div class="col-sm-6">
                        <br>
                        <div class="logo"><?= isset($settings['companyLogo']) ? Html::img(\Yii::getAlias('@uploads' . '/settings/').$settings['companyLogo']->value,['style'=>'max-height:200px; max-width:80%']) : '';?></div>

                        </div>
                        <div class="col-sm-6">
                        <table class="table">
                            <tr>
                                <td><?= Yii::t('app', 'Nazwa') ?>:</td>
                                <td><?= $model->name ?></td>
                            </tr>
                            <tr>
                                <td><?= Yii::t('app', 'Numer') ?>:</td>
                                <td><?= $model->id ?></td>
                            </tr>
                            <tr>
                                <td><?= Yii::t('app', 'Termin') ?>:</td>
                                <td><?= substr($model->event_start, 0,11) ?> <?= Yii::t('app', 'do') ?> <?= substr($model->event_end, 0, 11) ?></td>
                            </tr>
                            <tr>
                                <td><?= Yii::t('app', 'Data oferty') ?>:</td>
                                <td><?= $model->offer_date ?></td>
                            </tr>
                        </table>

                        </div>
                    </div>
                    <div class="row">
                        <div class="col-xs-6">
                        <?php if (isset($model->customer)){ ?>
                        <div class="upf"><b><?= Yii::t('app', 'Zamawiający') ?>:</b></div>
                        <h3><br>
                            <?= $model->customer->name ?>
                        </h3>
                        <p><?= $model->customer->zip ?> <?= $model->customer->city ?></p>
                        <p><?= Yii::t('app', 'NIP') ?>: <?= $model->customer->nip ?></p>
                        <p><?= Yii::t('app', 'mobile') ?>: <?= $model->customer->address ?></p>
                        <p><?= Yii::t('app', 'e-mail') ?>: <?= $model->customer->email ?></p>
                        <?php } ?>
                        </div>
                        <div class="col-xs-6">
                        <table class="table">
                        <tr>
                            <td><?= Yii::t('app', 'Kierownik projektu') ?>:</td>
                            <td><?php if ($model->manager) { echo $model->manager->first_name ." " . $model->manager->last_name; } ?></td>
                        </tr>
                        <tr>
                            <td><?= Yii::t('app', 'tel') ?>:</td>
                            <td><?php if ($model->manager) { echo $model->manager->phone; } ?></td>
                        </tr>
                        <tr>
                            <td><?= Yii::t('app', 'e-mail') ?>:</td>
                            <td><?php if ($model->manager) { $model->manager->email; } ?></td>
                        </tr>
                    </table>
                        </div>
                    </div>
                </div>

        </div>
    </div>
    <div class="row">
    <div class="col-xs-12">
        <?= Html::a('<i class="fa fa-plus"></i> '.Yii::t('app', 'Dodaj grupę usług'), ['/agency-offer/create-category', 'id'=>$model->id], ['class'=>'btn btn-primary add-service-category'])." "; ?> 
        </div>           
    </div>
    <div class="row">
        <div class="col-sm-12">
        <?php 
        $form = ActiveForm::begin([
            'id'=>'offer-form',
        ]); ?>
        <ul class="todo-list ui-sortable" id="list">
        <?php
        $total_summ_of_services = 0;
        $total_summ_of_services_provision = 0;
        $total_profit_of_services=0; ?>
            <?php foreach($offerForm->serviceCategories as $category): 
                $summ_of_one_cat = 0;
                $profit_of_one_cat = 0;
                $class="";
                if ($category['provizion'])
                {
                    $class="provision";
                }
            ?>
            <li class="checklist-item" draggable="true" id="bigitem-<?=$category['id']?>">
            <div class="ibox float-e-margins">
                <div class="ibox-title navy-bg  <?=$class?>"  style="background-color:<?=$category['color']?>">
                            <h5><?=$category['name']?></h5>
                                    <div class="ibox-tools white">
                                    <?= Html::a('<i class="fa fa-plus"></i> '.Yii::t('app', 'Dodaj'), ['/agency-offer/service-create', 'id'=>$model->id, 'category'=>$category['id']], ['class'=>'white-button add-service']); ?>
                                    <?= Html::a('<i class="fa fa-pencil"></i> ', ['/agency-offer/service-category-update', 'id'=>$category['id']], ['class'=>'white-button edit-service-category']); ?>
                                    <?= Html::a(Html::icon('trash'), ['/agency-offer/category-delete', 'id' => $category['id']], [
                                            'class'=>'delete-category'
                                        ])
                                        ?>
                                    <a class="collapse-link">
                                        <i class="fa fa-chevron-up"></i>
                                    </a>
                            </div>
                        </div>
                    <div class="ibox-content">
                        <div class="row">

                        <div class="col-xs-3"><?= Yii::t('app', 'Nazwa') ?></div>
                        <div class="col-xs-1"><?= Yii::t('app', 'Ilość') ?></div>
                        <div class="col-xs-1"><?= Yii::t('app', 'Cena') ?></div>
                        <div class="col-xs-1"><?= Yii::t('app', 'Cena dla klienta') ?></div>
                        <div class="col-xs-2"><?= Yii::t('app', 'Razem netto') ?></div>
                        <div class="col-xs-1"><?= Yii::t('app', 'Zysk netto') ?></div>
                        <div class="col-xs-2"><?= Yii::t('app', 'Uwagi') ?></div>
                        <div class="col-xs-1"></div>
                        </div>
                        <ul class="todo-list small-list ui-sortable" id="list-<?=$category['id']?>">
                        <?php foreach ($category['items'] as $service): 
                            $baseIndex = 'services[' . $service['id'] . ']';
                        ?>
                        
                            <li class="checklist-item" draggable="true" id="item-<?=$service['id']?>">
                                <div class="row">
                                <div class="col-xs-3"><?= $form->field($offerForm, $baseIndex . '[name]')->textInput()->label(false) ?></div>

                                <div class="col-xs-1"><?= $form->field($offerForm, $baseIndex . '[count]')->textInput()->label(false) ?></div>
                                <div class="col-xs-1"><?= $form->field($offerForm, $baseIndex . '[price]')->textInput()->label(false); ?></div>
                                <div class="col-xs-1"><?= $form->field($offerForm, $baseIndex . '[client_price]')->textInput()->label(false); ?></div>
                                <div class="col-xs-2"><?= $form->field($offerForm, $baseIndex . '[total_price]')->textInput(['disabled'=>true, 'class'=>'client-price'])->label(false); ?></div>
                                <div class="col-xs-1"><?= $form->field($offerForm, $baseIndex . '[total_profit]')->textInput(['disabled'=>true, 'class'=>'profit'])->label(false); ?></div>

                                <div class="col-xs-2"><?= $form->field($offerForm, $baseIndex . '[info]')->textInput()->label(false); ?></div>
                                <div class="col-xs-1"><?= Html::a(Html::icon('trash'), ['/agency-offer/service-delete', 'id' => $service['id']], [
                'class' => 'btn btn-danger  delete-item',
                
            ])
            ?>
                                    </div>
                            </div>
                            </li>
                        <?php $summ_of_one_cat+=$service['total_price']; 
                                $profit_of_one_cat+=$service['total_profit'];
                            ?>
                        <?php endforeach; ?>
                        </ul>
                        <div class="warning row">
                        <div class="col-xs-6"><b><u><?= Yii::t('app', 'Łącznie') ?> <?=$category['name']?></u></b></div>
                        <div class="col-xs-1"><?=Yii::t('app', 'Cena')?></div>
                        <div class="col-xs-2 total-category-price"><?=$formatter->asCurrency($summ_of_one_cat)?></div>
                        <div class="col-xs-1"><?=Yii::t('app', 'Zysk')?></div>
                        <div class="col-xs-2 total-category-profit"><?=$formatter->asCurrency($profit_of_one_cat)?></div>
                    </div>
                    <?php 
                    $total_summ_of_services+=  $summ_of_one_cat;   
                    $total_profit_of_services+=$profit_of_one_cat;
                    if ($category['provizion'])
                    {
                        $total_summ_of_services_provision+=$summ_of_one_cat;
                    }
                    

                    ?> 
                    </div>
            </div>
            </li>
        <?php endforeach; 
        ?>
            
        </ul>
        <?php ActiveForm::end(); ?>
    </div>
    </div>
    <div class="row">
    <div class="col-xs-12">
        <?= Html::a('<i class="fa fa-plus"></i> '.Yii::t('app', 'Dodaj grupę usług'), ['/agency-offer/create-category', 'id'=>$model->id], ['class'=>'btn btn-primary add-service-category'])." "; ?> 
        </div>           
    </div>
    <?php
    $sum_netto = $total_summ_of_services;
    $provision = $total_summ_of_services_provision*$model->provision/100;
    $vat = ($sum_netto+$provision)*0.23;
    $sum_brutto = $sum_netto+$provision+$vat;
    ?>
    <div class="row">
    <div class="col-xs-12">
             <div class="ibox float-e-margins">
                <div class="ibox-title  navy-bg">
                            <h5><?=Yii::t('app', 'Podsumowanie')?></h5>
                                    <div class="ibox-tools white">
                                    <a class="collapse-link">
                                        <i class="fa fa-chevron-up"></i>
                                    </a>
                            </div>
                        </div>
                    <div class="ibox-content"> 
                    <div class="row">
                        <div class="col-xs-6">
                        
                        </div>
                        <div class="col-xs-4">
                            <?=Yii::t('app', 'Suma netto:')?>
                        </div>
                        <div class="col-xs-2 sum-netto">
                        <?= $formatter->asCurrency($sum_netto)?>
                        </div>                           
                    </div>
                    <div class="row">
                        <div class="col-xs-6">
                        
                        </div>
                        <div class="col-xs-4">
                            <?=Yii::t('app', 'Wynagrodzenie agencji [%]:')?>
                            <input type="text" id="offer-provision" name="provision" value="<?=$model->provision?>">
                        </div>
                            
                        <div class="col-xs-2 provision-netto">
                        <?= $formatter->asCurrency($provision)?>
                        </div>                           
                    </div>
                    <div class="row">
                        <div class="col-xs-6">
                        
                        </div>
                        <div class="col-xs-4">
                            <?=Yii::t('app', 'Podatek VAT:')?>
                        </div>
                        <div class="col-xs-2 vat">
                            <?= $formatter->asCurrency($vat)?>
                        </div>                           
                    </div>
                    <div class="row">
                        <div class="col-xs-6">
                        
                        </div>
                        <div class="col-xs-4">
                            <?=Yii::t('app', 'Suma brutto:')?>
                        </div>
                        <div class="col-xs-2 sum-brutto">
                        <?= $formatter->asCurrency($sum_brutto)?>
                        </div>                           
                    </div>
                    </div>
            </div>      
        </div>           
    </div>
</div>




    <?php

$this->registerJs("
$( function() {
    $( '#list').sortable({
    update: function (event, ui) {
        var data = $(this).sortable('serialize');
        $.ajax({
            data: data,
            type: 'POST',
            url: '".Url::to(['/agency-offer/category-order'])."'
        });
    }
});
    $( '#list').disableSelection();
  } );
  $('.change-visible').on('click', function(e){
    e.preventDefault();
    data=[];
    $.post($(this).attr('href'), data, function(response){
        changeRow(response);
    });
  });
  $('.delete-item').on('click', function(e){
    e.preventDefault();
    deleteItem($(this));
  });
  $('.delete-category').on('click', function(e){
    e.preventDefault();
    data=[];
    deleteCategory($(this));
  });
  $('#offer-form input[type=text]').on ('change', function(){
    changeRow($(this));
  });
  $('#offer-provision').on ('change', function(){
    countPrices();
    changeProvision($(this).val());
  });
    ");

foreach($offerForm->serviceCategories as $category):
$this->registerJs("
$( function() {
    $( '#list-".$category['id']."').sortable({
    update: function (event, ui) {
        var data = $(this).sortable('serialize');
        $.ajax({
            data: data,
            type: 'POST',
            url: '".Url::to(['/agency-offer/service-order'])."?id=".$category['id']."'
        });
    }
});
    $( '#list-".$category['id']."').disableSelection();
  } );



    ");

endforeach;
?>
<script src="https://unpkg.com/sweetalert/dist/sweetalert.min.js"></script>
<script type="text/javascript">
var currency = '<?=substr($formatter->asCurrency(0), 0,3)?>';
var currency = "";
    function addNewRow(data)
    {

        $("#list-"+data.category_id).append('<li class="checklist-item ui-sortable-handle" draggable="true" id="item-'+data.id+'"><div class="row"><div class="col-xs-3"><div class="form-group field-agencyofferform-services-'+data.id+'-name"><input type="text" id="agencyofferform-services-'+data.id+'-name" class="form-control" name="AgencyOfferForm[services]['+data.id+'][name]" value=""><div class="help-block"></div></div></div><div class="col-xs-1"><div class="form-group field-agencyofferform-services-'+data.id+'-count"><input type="text" id="agencyofferform-services-'+data.id+'-count" class="form-control" name="AgencyOfferForm[services]['+data.id+'][count]" value=1><div class="help-block"></div></div></div><div class="col-xs-1"><div class="form-group field-agencyofferform-services-'+data.id+'-price"><input type="text" id="agencyofferform-services-'+data.id+'-price" class="form-control" name="AgencyOfferForm[services]['+data.id+'][price]"><div class="help-block"></div></div></div><div class="col-xs-1"><div class="form-group field-agencyofferform-services-'+data.id+'-client_price"><input type="text" id="agencyofferform-services-'+data.id+'-client_price" class="form-control" name="AgencyOfferForm[services]['+data.id+'][client_price]"><div class="help-block"></div></div></div><div class="col-xs-2"><div class="form-group field-agencyofferform-services-'+data.id+'-total_price"><input type="text" id="agencyofferform-services-'+data.id+'-total_price" class="form-control client-price" name="AgencyOfferForm[services]['+data.id+'][total_price]" disabled=""><div class="help-block"></div></div></div><div class="col-xs-1"><div class="form-group field-agencyofferform-services-'+data.id+'-total_profit"><input type="text" id="agencyofferform-services-'+data.id+'-total_profit" class="form-control profit" name="AgencyOfferForm[services]['+data.id+'][total_profit]" disabled=""><div class="help-block"></div></div></div><div class="col-xs-2"><div class="form-group field-agencyofferform-services-'+data.id+'-info"><input type="text" id="agencyofferform-services-'+data.id+'-info" class="form-control" name="AgencyOfferForm[services]['+data.id+'][info]"><div class="help-block"></div></div></div><div class="col-xs-1"><a class="btn btn-danger  delete-item" href="/admin/agency-offer/service-delete?id='+data.id+'"><span class="glyphicon glyphicon-trash"></span></a></div></div></li>');
        $("#item-"+data.id).find('.delete-item').on('click', function(e){e.preventDefault(); deleteItem($(this));})
        $("#item-"+data.id).find('input').on('change', function(){changeRow($(this));})
    }

    function deleteItem(item)
    {
        swal({
            title: "Czy Na pewno chcesz usunąć?",
            icon:"warning",
          buttons: {
            cancel: "Nie",
            yes: {
              text: "Tak",
              value: "yes",
            },
          },
        })
        .then((value) => {
          switch (value) {
         
            case "yes":
                    data=[];
                    $.post(item.attr('href'), data, function(response){
                        row = $('#item-'+response.id);
                        row.remove();
                        countPrices();
                    });
              break;       
          }
        });
    }
    function deleteCategory(item)
    {
        swal({
            title: "Czy Na pewno chcesz usunąć grupę z wszystkimi pozycjami?",
            icon:"warning",
          buttons: {
            cancel: "Nie",
            yes: {
              text: "Tak",
              value: "yes",
            },
          },
        })
        .then((value) => {
          switch (value) {
         
            case "yes":
                    data=[];
                    $.post(item.attr('href'), data, function(response){
                        row = $('#bigitem-'+response.id);
                        row.remove();
                        countPrices();
                    });
              break;       
          }
        });        
    }

    function changeRow(item)
    {
        data = [];
        data = item.parent().parent().parent().find("input[type=text]").serialize();
        $.post('<?=Url::to(['/agency-offer/edit-service'])."?id=".$model['id']?>', data, function(response){
            $("#agencyofferform-services-"+response.id+"-count").val(response.count);
            $("#agencyofferform-services-"+response.id+"-name").val(response.name);
            $("#agencyofferform-services-"+response.id+"-price").val(response.price);
            $("#agencyofferform-services-"+response.id+"-total_price").val(response.total_price);
            $("#agencyofferform-services-"+response.id+"-client_price").val(response.client_price);
            $("#agencyofferform-services-"+response.id+"-info").val(response.info);
            $("#agencyofferform-services-"+response.id+"-total_profit").val(response.total_profit);
            countPrices();
        });    
           
    }
    function countPrices()
    {
        
        var $total_sum=0;
        var $total_sum_provision=0;
        $('#list').find(".ibox").each(function()
        {
            var $sum = 0;
            var $profit = 0;
            $total_price =$(this).find('.total-category-price');
            $total_profit =  $(this).find('.total-category-profit');
            $provision = $(this).find('.ibox-title');
            $(this).find('.client-price').each(function(){
                if (parseFloat($(this).val()))
                    $sum +=parseFloat($(this).val());
            });
            $(this).find('.profit').each(function(){
                if (parseFloat($(this).val()))
                    $profit +=parseFloat($(this).val());
            });
            $total_price.empty().append(currency+" "+$sum.toFixed(2));
            $total_profit.empty().append(currency+" "+$profit.toFixed(2));
            $total_sum +=$sum;
            if ($provision.hasClass('provision')){
                $total_sum_provision+=$sum;
            }
        });
        $(".sum-netto").empty().append(currency+" "+$total_sum.toFixed(2));
        $provision = $("#offer-provision").val();
        if (!parseFloat($provision))
                    $provision=0;
        $total_provision = $total_sum_provision*$provision/100;
        $(".provision-netto").empty().append(currency+" "+$total_provision.toFixed(2));
        $vat = ($total_sum+$total_provision)*0.23;
        $(".vat").empty().append(currency+" "+$vat.toFixed(2));
        $sum_brutto = ($total_sum+$total_provision)*1.23;
        $(".sum-brutto").empty().append(currency+" "+$sum_brutto.toFixed(2));
    }

    function updateCategoryRow(data)
    {
        var $row = $("#bigitem-"+data.id).find(".ibox-title");
        $row.find('h5').empty().append(data.name);
        $row.css("background-color", data.color);
        if (data.provizion=="1")
        {
            $row.addClass('provision');
        }else{
            $row.removeClass('provision');
        }
        countPrices();
    }

    function changeProvision(val)
    {
        data = [];
        $.post('<?=Url::to(['/agency-offer/edit-offer'])."?id=".$model['id']?>&provision='+val, data, function(response){
        });
    }

    function addNewCategoryRow(data)
    {

        $("#list").append('<li class="checklist-item" draggable="true" id="bigitem-'+data.id+'"><div class="ibox float-e-margins"><div class="ibox-title navy-bg"><h5>'+data.name+'</h5><div class="ibox-tools white"><a class="white-button add-service" href="/admin/agency-offer/service-create?id=<?=$model->id?>&category='+data.id+'"><i class="fa fa-plus"></i> Dodaj</a><a class="white-button edit-service-category" href="/admin/agency-offer/service-category-update?id='+data.id+'"><i class="fa fa-pencil"></i> </a><a class="delete-category" href="/admin/agency-offer/category-delete?id='+data.id+'"><span class="glyphicon glyphicon-trash"></span></a><a class="collapse-link"><i class="fa fa-chevron-up"></i></a></div></div><div class="ibox-content">                        <div class="row"><div class="col-xs-3"><?= Yii::t('app', 'Nazwa') ?></div><div class="col-xs-1"><?= Yii::t('app', 'Ilość') ?></div><div class="col-xs-1"><?= Yii::t('app', 'Cena') ?></div><div class="col-xs-1"><?= Yii::t('app', 'Cena dla klienta') ?></div><div class="col-xs-2"><?= Yii::t('app', 'Razem netto') ?></div><div class="col-xs-1"><?= Yii::t('app', 'Zysk netto') ?></div><div class="col-xs-2"><?= Yii::t('app', 'Uwagi') ?></div><div class="col-xs-1"></div></div><ul class="todo-list small-list ui-sortable" id="list-'+data.id+'"></ul><div class="warning row"><div class="col-xs-6"><b><u><?= Yii::t('app', 'Łącznie') ?> '+data.name+'</u></b></div><div class="col-xs-1"><?=Yii::t('app', 'Cena')?></div><div class="col-xs-2 total-category-price">0</div><div class="col-xs-1"><?=Yii::t('app', 'Zysk')?></div><div class="col-xs-2 total-category-profit">0</div></div></div>');
        $("#bigitem-"+data.id).find(".add-service").click(function(e){
            e.preventDefault();
            $("#new-service").modal("show").find(".modalContent").load($(this).attr("href"));
            var data = [];
            $.ajax({
                data: data,
                type: "POST",
                url: $(this).attr("href"),
                success: function (data) {
                addNewRow(data);
            },
            });
        });
        $("#bigitem-"+data.id).find(".edit-service-category").click(function(e){
            e.preventDefault();
            $("#edit-service-category").modal("show").find(".modalContent").load($(this).attr("href"));
        });
         $("#bigitem-"+data.id).find('.delete-category').on('click', function(e){
            e.preventDefault();
            data=[];
            deleteCategory($(this));
          })
         $("#bigitem-"+data.id).find(".ibox-title").css("background-color", data.color);
         $('html,body').animate({
          scrollTop: $("#bigitem-"+data.id).offset().top
        }, 1000)
    }
</script>