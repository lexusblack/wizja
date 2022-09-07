<?php
use common\helpers\ArrayHelper;

use common\models\Event;
use common\models\EventGearItem;
use common\models\EventOuterGear;
use common\models\Gear;
use common\models\GearItem;
use common\models\OutcomesGearOur;
use common\models\OutcomesGearOuter;
use common\models\OuterGear;

use kartik\icons\Icon;
use kartik\popover\PopoverX;
use yii\bootstrap\Html;
use common\components\grid\GridView;
use kartik\editable\Editable;
use kartik\form\ActiveForm;
use yii\bootstrap\Modal;
use yii\helpers\Url;
$user = Yii::$app->user;
$checkGearConflictsUrl = Url::to(['warehouse/gear-conflicts', 'event_id'=>$model->id]);
$eventGearConflictsUrl = Url::to(['warehouse/gear-conflicts-modal', 'event_id'=>$model->id]);
if ($sort=="cat")
{
    $grouped = true;
}else{
    $grouped = false;
}
?>

<div class="row">
    <div class="col-md-12">
        <div class="panel_mid_blocks">
            <div class="panel_block">
            <div class="row" style="padding-top:10px; margin-bottom:10px;">
            <div class="col-md-5">
                <?php 
                $dropList = [];
                if (!$packlist->blocked){
                $dropList["change_time"] = Yii::t('app', 'Zaznaczonym zmień czas rezerwacji ');
                $dropList["delete"] = Yii::t('app', 'Usuń zaznaczone');
                foreach ($model->packlists as $p)
                {
                    if (($p->id!=$packlist->id)&&(!$p->blocked))
                        $dropList["packlist_".$p->id] = Yii::t('app', 'Zaznaczone dodaj do ').$p->name;
                }
                }
                if (($user->can('eventEventEditEyeGearManage'))&&(((!$model->getBlocks('gear'))||(Yii::$app->user->can('eventEventBlockGear'))))&&(!$packlist->blocked)) {
                $pac = new \common\models\form\GearActionForm();
                $form = ActiveForm::begin(['id' => 'action-form'.$packlist->id, 'type'=>ActiveForm::TYPE_INLINE,]);

                echo   $form->field($pac, 'items')->hiddenInput()->label(false);

                echo $form->field($pac, 'action')->dropDownList($dropList)->label(Yii::t('app', "Z zaznaczonymi"));

                echo Html::submitButton(Yii::t('app', 'Wykonaj') , ['class' => 'btn btn-success action-form-submit']);

                ActiveForm::end();
            }
         ?></div>
         <div class="col-md-5">
         <?php if ($user->can('eventsEventEditEyeGearBlock')) { ?>
         <?php if (!$packlist->blocked){
            $log = \common\models\EventLog::find()->where(['event_id'=>$model->id])->andWhere(['like', 'content', 'została odblokowana'])->orderBy(['create_time'=>SORT_DESC])->one();
            if ($log)
                echo "Paklista odblokowana (".$log->user->displayLabel." ".$log->create_time.") <br/>";
            echo Html::a('<i class="fa fa-ban"></i> ' . Yii::t('app', 'Zablokuj'), ['block-packlist', 'id' => $model->id, 'packlist_id'=>$packlist->id, 'type'=>1], ['class' => 'btn btn-danger btn-sm block-button']);
         }else{
            $log = \common\models\EventLog::find()->where(['event_id'=>$model->id])->andWhere(['like', 'content', 'została zablokowana'])->orderBy(['create_time'=>SORT_DESC])->one();
            echo "Paklista zablokowana (".$log->user->displayLabel." ".$log->create_time.")  <br/>".Html::a('<i class="fa fa-ban"></i> ' . Yii::t('app', 'Odblokuj'), ['block-packlist', 'id' => $model->id, 'packlist_id'=>$packlist->id, 'type'=>0], ['class' => 'btn btn-info btn-sm block-button']);

            }?>
        <?php }else{
                if ($packlist->blocked){
                    $log = \common\models\EventLog::find()->where(['event_id'=>$model->id])->andWhere(['like', 'content', 'została zablokowana'])->orderBy(['create_time'=>SORT_DESC])->one();
                    echo "Paklista zablokowana (".$log->user->displayLabel." ".$log->create_time.")  <br/>";
                }
            } ?>
         <?= Html::a('<i class="fa fa-list"></i> ' . Yii::t('app', 'Packlista PDF'), ['packlist-pdf', 'id' => $model->id, 'packlist_id'=>$packlist->id, 'sort'=>$sort], ['class' => 'btn btn-success btn-sm', 'target'=>'_blank']);?>
         <?= Html::a('<i class="fa fa-euro"></i> ' . Yii::t('app', 'Packlista PDF'), ['packlist-pdf', 'id' => $model->id, 'packlist_id'=>$packlist->id, 'sort'=>$sort, 'money'=>true], ['class' => 'btn btn-primary btn-sm', 'target'=>'_blank']);?>

         <?php
         if (($user->can('eventEventEditEyeGearManage'))&&(((!$model->getBlocks('gear'))||(Yii::$app->user->can('eventEventBlockGear'))))&&(!$packlist->blocked)) {
            if ($packlist->start_time)
            {

                    echo " ".Html::a('<i class="fa fa-gears"></i> ' .Yii::t('app', 'Zarządzaj'), ['warehouse/assign', 'id' => $model->id, 'type' => 'event', 'packlist'=>$packlist->id], ['class' => 'btn btn-success btn-sm']);
            }else{

            echo " ".Html::a('<i class="fa fa-gears"></i> ' .Yii::t('app', 'Zarządzaj'), ['warehouse/assign', 'id' => $model->id, 'type' => 'event', 'packlist'=>$packlist->id], ['class' => 'btn btn-success btn-sm', 'disabled'=>'disabled', 'title'=>Yii::t('app', 'Grupa nie ma zdefiniowanego czasu pracy, dlatego nie jest możliwe zarządzanie sprzętem.')]);
            }

        } ?>

            <?php
                    if ($user->can('eventRentsMagazin')) {
            echo " ".Html::a('<i class="fa fa-arrow-right"></i> ' .Yii::t('app', 'Wydaj'), ['outcomes-warehouse/create', 'event' => $model->id, 'packlist_id'=>$packlist->id], ['class' => 'btn btn-primary btn-sm', 'target' => '_blank']);
        }
        
        
        if ($user->can('eventRentsMagazin')) {
            echo " ".Html::a('<i class="fa fa-arrow-left"></i> ' .Yii::t('app', 'Przyjmij'), ['incomes-warehouse/create', 'event' => $model->id, 'onlyEvent'=>1, 'packlist_id'=>$packlist->id], ['class' => 'btn btn-primary btn-sm', 'target' => '_blank']);
        } ?>
            </div>
            <div class="col-md-1">
                <?php 
                if ($user->can('gearWarehouseOutcomesView')) {
            foreach ($model->outcomesForEvents as $outcome) {
                if ($outcome->packlist_id==$packlist->id)
                echo ' ' . Html::a(Yii::t('app', 'Wydanie')." " . $outcome->outcome_id, ['outcomes-warehouse/view', 'id' => $outcome->outcome_id], ['class' => 'btn btn-warning btn-xs', 'target' => '_blank', 'style'=>"padding:1px; margin:1px; 3px"]);
            }
        } ?>
            </div>
            <div class="col-md-1"><?php 
        if ($user->can('gearWarehouseIncomesView')) {
            foreach ($model->incomesForEvents as $income) {
                if ($income->packlist_id==$packlist->id)
                echo ' ' . Html::a(Yii::t('app', 'Zwrot')." " . $income->income_id, ['incomes-warehouse/view', 'id' => $income->income_id], ['class' => 'btn btn-danger btn-xs', 'target' => '_blank', 'style'=>"padding:1px; margin:1px; 3px"]);
            }
        }
            ?></div>
                    

            </div>
            <h3><?=$packlist->name?> <?="[".substr($packlist->start_time, 0, 16)." - ".substr($packlist->end_time,0,16)."]"?>
        <?php if (Yii::$app->user->can('eventEventEditPencil')) { ?>
        <?= Html::a('<i class="fa fa-pencil"></i> ' . Yii::t('app', 'Edycja'), ['add-packlist', 'id' => $model->id, 'packlist_id'=>$packlist->id], ['class' => 'btn btn-primary add-packlist btn-xs']);?>
        <?php if (!$packlist->main){?>
        <?= Html::a('<i class="fa fa-trash"></i> ' . Yii::t('app', 'Usuń'), ['packlist-delete', 'id' => $model->id, 'packlist_id' => $packlist->id], ['class' => 'btn btn-danger btn-xs','data' => [
                'confirm' => Yii::t('app', 'Na pewno chcesz usunąć?'),
                'method' => 'post',
            ],]);?>
        <?php } ?>
            <?php } ?> 
            <?php if (($packlist->start_time<$model->event_start)||($packlist->end_time>$model->event_end)){ ?>
                <span class="label label-danger"><?=Yii::t('app', 'UWAGA! Czas pracy grupy nie pokrywa się z harmonogramem eventu')?></span>
            <?php } ?>
            </h3>
        <?php

        $columns = [
                [
                'class' => 'yii\grid\CheckboxColumn',
                'multiple'=>true, 
                'checkboxOptions' => function($model) {
                    return ['value' => $model->id];
                },
                ],
                ['attribute' => 'id', 'visible' => false],
                    [
                        'label' => Yii::t('app', 'Zdjęcie'),
                        'format' => 'html',
                        'value' => function ($model) {
                            /* @var $model \common\models\Gear */
                            if ($model->gear->photo == null) {
                                return "-";
                            }
                            else {
                                return Html::a(Html::img($model->gear->getPhotoUrl(), ['width'=>50]), ['gear/view', 'id'=>$model->gear->id]);
                            }
                        }
                    ],
                    [
                        'label' => Yii::t('app', 'Model'),
                        'format' => 'html',
                        'value' => function($model) {
                            return Html::a($model->gear->name, ['gear/view', 'id'=>$model->gear->id]);
                        }
                    ],
                    [
                        'label' => Yii::t('app', 'Sztuk'),
                        'format'=>'raw',
                        'attribute'=>'quantity',
                        'value' => function($model) use ($user, $packlist)
                        {
                            if (($user->can('eventEventEditEyeGearManage'))&&(((!$model->packlist->event->getBlocks('gear'))||(Yii::$app->user->can('eventEventBlockGear'))))&&(!$packlist->blocked)) {
                                $content = '';
                                    $this->beginBlock('form');

                                    $assignmentForm = new \common\models\form\GearAssignmentPacklist();
                                    $assignmentForm->itemId = $model->gear_id;
                                    $assignmentForm->quantity = $model->quantity;
                                    $assignmentForm->oldQuantity = $assignmentForm->quantity;
                                    $isAvailable = true;

                                    $form = ActiveForm::begin([
                                        'options' => [
                                            'class'=>'gear-assignment-form',
                                        ],
                                        'action' =>['assign-gear', 'id'=>$model->packlist->event_id, 'type'=>'event'],
                                        'type'=>ActiveForm::TYPE_INLINE,
                                        'formConfig' => [
                                            'showErrors' => true,
                                        ]
                                    ]);
                                    echo Html::activeHiddenInput($assignmentForm, 'itemId');
                                    echo Html::activeHiddenInput($assignmentForm, 'oldQuantity');
                                    if ($assignmentForm->quantity === null) {
                                        $assignmentForm->quantity = 1;
                                    }
                                    echo $form->field($assignmentForm, 'quantity')->textInput([
                                        'disabled'=> $isAvailable ? false : true,
                                        'style' => 'width: 62px;',
                                        'class'=>'gear-quantity',
                                        'data'=>['packlist'=>$model->packlist_id, 'start'=>$model->start_time, 'end'=>$model->end_time]
                                    ]);
                                    ActiveForm::end();
                                    $this->endBlock();

                                    $content .= $this->blocks['form'];
                            }else{
                                $content = $model->quantity;
                            }
                            $c = \common\models\EventConflict::find()->where(['packlist_gear_id'=>$model->id, 'resolved'=>0])->one();
                            if ($c)
                                $content .="<span class='label label-danger'>".Yii::t('app', 'konflikt: ').$c->quantity.Yii::t('app', 'szt.')."</span>";
                            return $content;
                            
                        }
                    ],
                    [
                        'label' => Yii::t('app', 'Pakowany'),
                        'format'=>'raw',
                        'value'=> function($model){
                            $result = "";
                            foreach ($model->gear->getPacking() as $case){
                                $result .= $case."</br>";
                            }
                            return $result;
                        }
                    ],
                    [
                        'label' => Yii::t('app', 'Czas pracy'),
                        'format'=>'raw',
                        'value' =>function($gear) use ($model, $packlist) {
                            $event_start = $model->getTimeStart();
                            $event_end = $model->getTimeEnd();
                            if ($gear->start_time == $event_start && $gear->end_time == $event_end) {
                                $display_value = Yii::t('app', 'Cały event');
                            }
                            else {
                                $display_value =  Yii::t('app', 'Od: ') .substr($gear->start_time, 0, 16) . "</br>" .Yii::t('app', 'Do: ') . substr($gear->end_time, 0, 16);
                            }


                                $widget = $display_value;
                            if ($gear->start_time != $packlist->start_time || $gear->end_time != $packlist->end_time) {
                                $widget ="<span class='label label-warning pull-right' title='".Yii::t('app', 'Inny czas pracy niż grupa')."'><i class='fa fa-clock-o'></i></span>".$widget;
                            }
                            return $widget;
                        }
                    ],

                    [
                        'label' => Yii::t('app', 'Grupy sprzętowe'),
                        'format' => 'raw',
                        'value' => function ($model) {
                            $total = 0;
                            $content = "";
                            if (isset($model->eventGear))
                            {
                                foreach ($model->eventGear->packlistGears as $p)
                                {
                                    $content .='<span class="label label-warning" style="background-color:'.$p->packlist->color.'; margin-bottom:3px;">'.$p->quantity.'</span> ';
                                    $c = \common\models\EventConflict::find()->where(['packlist_gear_id'=>$p->id, 'resolved'=>0])->one();
                                    if ($c)
                                        $content .="<span class='label label-danger' style='margin-bottom:3px;''>".Yii::t('app', 'konflikt: ').$c->quantity.Yii::t('app', 'szt.')."</span>";
                                    $content .= "</br>";
                                }
                            }

                            return $content;
                        },
                    ],
                   /* [
                        'label' => Yii::t('app', 'Komentarz'),
                        'attribute'=>'comment',
                        'class'=>\kartik\grid\EditableColumn::className(),
                        'editableOptions' => function ($gear, $key, $index) use ($model) {
                            return [
                            'name'=>'comment',
                            'inputType' => Editable::INPUT_TEXT,
                                'formOptions' => [
                                        'action'=>['/event/save-gear-comment', 'gear_id'=>$gear->id],
                                    ],
                                'options' => [
                                ],
                            'pluginEvents' =>   [ 
                                "editableSuccess"=>"function(event, val, form, data) { }",
                            ]
                            ];
                        },
                    ],*/
                    [
                        'label' => Yii::t('app', 'Komentarz'),
                        'attribute'=>'comment',
                        'format'=>'raw',
                        'value'=>function($model)
                        {
                            return "<span id='comment".$model->id."'>".$model->comment."</span> ".Html::a("Edytuj", ['/event/save-gear-comment', 'gear_id'=>$model->id], ['class'=>'comment-edit']);
                        }
                    ],
                    [
                        'label' => Yii::t('app', 'Objętość'),
                        'value' => function($eg) use ($model){
                                    $sum = 0;
                                    if ($eg->gear->no_items)
                                    {
                                        $sum =$eg->quantity*$eg->gear->volume;
                                    }else{
                                        $items = ArrayHelper::map(GearItem::find()->where(['gear_id'=>$eg->gear_id])->all(), 'id', 'id');
                                        $eventGearItems = EventGearItem::find()->where(['event_id'=>$model->id])->andWhere(['IN', 'gear_item_id', $items])->count();  
                                        $count =  $eg->quantity - $eventGearItems;
                                        if ($eventGearItems>0)
                                        {
                                            //liczymy dodane egzemplarze i case
                                            $ids = ArrayHelper::getColumn($model->getGearItems()->where(['IN', 'id', $items])->all(), 'group_id');
                                            $cases = \common\models\GearGroup::find()->where(['IN', 'id', $ids])->all();
                                            $gearNoCase = $model->getGearItems()->where(['group_id'=>null])->andWhere(['IN', 'id', $items])->all();
                                            $volumeCase = array_sum(ArrayHelper::getColumn($cases, 'calculatedVolume'));
                                            $volumeNoCase = array_sum(ArrayHelper::getColumn($gearNoCase, 'calculatedVolume'));
                                            $sum+=$volumeCase+$volumeNoCase;

                                        }
                                        if ($count>0)
                                        {
                                            //jesli sprzęt został dodany ilościowo to liczymy tak mniej więcej
                                            $sum =$eg->gear->countVolume2($count);
                                        }

                                    }
                                    return Yii::$app->formatter->asDecimal($sum,2);
                        }
                    ],
                                        [
                        'label' => Yii::t('app', 'Miejsce'),
                        'format'=>'raw',
                        'value'=> function($model){
                            return $model->gear->location;
                        }
                    ],
                    [
                        'format' =>'raw',
                        'value' => function ($gear) use ($model, $packlist){
                            $remove_link = "";
                            if ((Yii::$app->user->can('eventEventEditEyeGearDelete'))&&(((!$model->getBlocks('gear'))||(Yii::$app->user->can('eventEventBlockGear'))))&&(!$packlist->blocked)&&($gear->canBeDeleted())) {
                                $remove_link = Html::a(Html::icon('remove'), ['/warehouse/remove-gear',
                                    'id' => $model->id,
                                    'type' => $model->getClassType(),
                                    'noItem'=>1,],
                                     ['data' => ['itemId' => $gear->id,
                                    'name' => $gear->gear->name], 'class' => 'remove-assignment-button']);
                            }
                            return $remove_link;
                        }
                    ],
                    ];
if ($grouped){

            $columns[] = [
                        'label' => Yii::t('app', 'Sprzęt'),
                        'format' => 'raw',
                        'value' => function ($model) use ($packlist){
                            $category = $model->gear->category;
                            $categories = $category->parents()->all();
                            if (count($categories) > 1) {
                                $category_name = $categories[1]->name;
                                $category =  $categories[1];
                            }else{
                                $category_name = $category->name;
                            }
                            return '<input type="checkbox" data-category='.$category->id.' class="category-chackbox'.$packlist->id.'"> '.$category_name;
                        },
                        'group'=>true,
                        'groupedRow'=>true,
                        'groupOddCssClass'=>'grouped-category-row',
                        'groupEvenCssClass'=>'grouped-category-row',

                    ];
             $columns[] =     
                    [
                        'label' => Yii::t('app', 'Sprzęt 2'),
                        'format' => 'raw',
                        'value' => function ($model) use ($packlist) {
                            $category = $model->gear->category;
                            return '<input type="checkbox" data-category='.$category->id.' class="category-chackbox'.$packlist->id.'"> '.$category->name;
                        },
                        'group'=>true,
                        'groupedRow'=>true,
                        'groupOddCssClass'=>'grouped-category-row2',
                        'groupEvenCssClass'=>'grouped-category-row2',
                    ];
}
            echo GridView::widget([
                'dataProvider'=>$model->getAssignedGearModelPacklist($packlist->id, $sort),
                'id'=>'allGearTable'.$packlist->id,
                'tableOptions' => [

                    'class' => 'kv-grid-table table table-condensed kv-table-wrap'
                ],
                'rowOptions' => function ($model, $key, $index, $grid) use ($sort){
                    $category = $model->gear->category;
                            $categories = $category->parents()->all();
                            if (count($categories) > 1) {
                                $category_id = $categories[1]->id;
                            }else{
                                $category_id = $category->id;
                            }
                    return [
                        'data' => ['key' => $model->id, 'main-category'=>$category_id, 'sub-category'=>$category->id],
                    ];
                    
                },
                
                'columns' => $columns
                ]);

            $this->registerJs('
                $("#display-not-returned-gear").click(function(e){
                    e.preventDefault();
                    $("#not-returned-table").toggle();
                });
            
                $(".remove-assignment-button").on("click", function(e){
                    e.preventDefault();
                    
                    var quantityElement = $(this).parent().parent().parent().parent().parent().parent().prev().find(":nth-child(4)");
                    var quantity = parseInt(quantityElement.html());
                    quantityElement.html(quantity-1); 
                    $(this).parent().parent().remove();
                    if (quantity == 1 || $(this).data("name") == "_ILOSC_SZTUK_") {
                        if (quantityElement.parent().next().hasClass("sub_models")) {
                            quantityElement.parent().next().remove();
                        }
                        quantityElement.parent().prev().remove();
                        quantityElement.parent().remove();
                    }
                        
                    var $el = $(this);
                    $.post($el.prop("href"), $el.data(), function(response) {
                        if (response.success == 1)
                        {
                            $(".item-" + $el.data(\'itemid\')).each(function(){
                                $(this).remove();
                            });
                            $.post("'.$checkGearConflictsUrl.'&gear_id="+$el.data(\'itemid\'), $el.data(), function(response){
                            if (parseInt(response.conflicts)>0)
                            {
                                showConflictsToResolveModal($el.data(\'itemid\'), quantity);
                            }
                        });
                        }
                    });
                    return false;
                });
                
                $(".remove-assignment-group").click(function(e){
                    e.preventDefault();
                    
                    var quantity = $(this).data("itemsnumber");
                    var table = $(this).parent().parent().parent().parent();
                    var parentTable = table.parent().parent().prev();
                    var items = parentTable.children("td").eq(3);
                    var itemsNo = parseInt(items.html());
                    
                    $(this).parent().parent().remove();
                    items.html(itemsNo-quantity);

                    if ((itemsNo-quantity) <= 0) {
                        parentTable.next().remove();
                        parentTable.remove();
                    }
                    
                    $.post($(this).prop("href"), function(response) {
                        if (response.success == 1) {
                            console.log(response.success + "567");
                            $(this).parent().parent().remove();
                        }
                        else {
                            console.log(response.success + "453");
                        }
                    });
                });
                
                var counter = 0;
                $(".showGroup").click(function(e){
                    e.preventDefault();
                    counter++;
                    
                    if (counter % 2 == 0) {
                        $(this).find("i").attr("class", "glyphicon glyphicon-arrow-down");
                        $(this).parent().parent().next().find(":first-child").find(":first-child").slideUp();
                    }
                    else {
                        $(this).find("i").attr("class", "glyphicon glyphicon-arrow-up");
                        $(this).parent().parent().next().find(":first-child").find(":first-child").slideDown();
                    }
                });
                
                $(".table-bordered").each(function(){
                    $(this).removeClass("table-bordered");
                });
                $(".table-striped").each(function(){
                    $(this).removeClass("table-striped");
                });
            ');
        ?>
    </div>
</div>
    </div>
</div>
<div class="row">
    <div class="col-md-12">
    <div class="panel_mid_blocks">
        <div class="panel_block">
        <h5><?php echo Yii::t('app', 'Lista sprzętu zewnętrznego'); ?></h5>
        <?php
            echo GridView::widget([
                'dataProvider'=>$model->getAssignedOuterGears2($packlist->id),
                'id'=>'orderGear'.$packlist->id,
                'tableOptions' => [
                    'class' => 'kv-grid-table table table-condensed kv-table-wrap'
                ],
                                'rowOptions' => function ($model, $key, $index, $grid) {
                    return [
                        'data' => ['key' => $model->id],
                    ];
                },
                'columns' => [
                    [
                'class' => 'yii\grid\CheckboxColumn',
                'multiple'=>true,
                'checkboxOptions' => function($model, $key, $index, $widget) {
                    return ["value" => $model->id];
                },
                ],
                ['attribute' => 'id', 'visible' => false],
                    [
                        'class'=>\yii\grid\SerialColumn::className(),
                    ],
                    [
                        'attribute'=>'photo',
                        'value'=>function ($model, $key, $index, $column)
                        {
                            /* @var $model \common\models\OuterGear */
                            $og = $model->eventOuterGear->outerGear;
                            if ($og->getPhotoUrl() == null)
                            {
                                return '-';
                            }
                            return Html::a(Html::img($og->getPhotoUrl(), ['width'=>50]), ['outer-gear-model/view', 'id'=>$og->outer_gear_model_id]);
                        },
                        'format'=>'html',
                    ],
                    [
                        'attribute'=>'outer_gear_id',
                        'label'=>Yii::t('app', 'Nazwa'),
                        'value'=>function ($model, $key, $index, $column)
                        {
                            $og = $model->eventOuterGear->outerGear;
                            return Html::a($og->getName(), ['outer-gear-model/view', 'id'=>$og->outer_gear_model_id]);
                        },
                        'format'=>'html',
                    ],
                    [
                        'label' => Yii::t('app', 'Sztuk'),
                        'format'=>'html',
                        'value' => function($gear) use ($model) {
                            return $gear->quantity;
                        }
                    ],
                    [
                        'label' => Yii::t('app', 'Zamówienie'),
                        'format'=>'html',
                        'value' => function($gear) {
                            if ($gear->eventOuterGear->order_id)
                                return Html::a(Yii::t('app', 'Zamówienie nr').' '.$gear->eventOuterGear->order_id, ['/order/view', 'id' => $gear->eventOuterGear->order_id]);
                            else 
                                return "-";
                        }
                    ],
                    [
                        'label' => Yii::t('app', 'Firma'),
                        'value'=>function($gear)
                        {
                            return $gear->eventOuterGear->outerGear->company->displayLabel;
                        }
                    ],
                    [
                        'label' => Yii::t('app', 'Data odbioru'),
                        'class'=>\kartik\grid\EditableColumn::className(),
                        'editableOptions' => function ($gear, $key, $index) use ($model) {
                            return [
                            'name'=>'reception_time',
                            'inputType' => Editable::INPUT_DATE,
                                'formOptions' => [
                                        'action'=>['/outer-warehouse/save', 'gear_id'=>$gear->event_outer_gear],
                                    ],
                                'options' => [
                                'pluginOptions' => [
                                     'format' => 'yyyy-mm-dd'
                                     ]
                                ],
                            'pluginEvents' =>   [ 
                                "editableSuccess"=>"function(event, val, form, data) { }",
                            ]
                            ];
                        },
                        'format'=>'html',
                        'value' => function($gear) use ($model) {
                            $gear_no = $gear->eventOuterGear;
                            if ($gear_no->reception_time)
                                return substr($gear_no->reception_time,0,10);
                            else 
                                return "-";
                        }
                    ],
                    [
                        'label' => Yii::t('app', 'Data zwrotu'),
                        'class'=>\kartik\grid\EditableColumn::className(),
                        'editableOptions' => function ($gear, $key, $index) use ($model) {
                            return [
                            'name'=>'return_time',
                            'inputType' => Editable::INPUT_DATE,
                                'formOptions' => [
                                        'action'=>['/outer-warehouse/save', 'gear_id'=>$gear->event_outer_gear],
                                    ],
                                'options' => [
                                'pluginOptions' => [
                                     'format' => 'yyyy-mm-dd'
                                     ]
                                ],
                            'pluginEvents' =>   [ 
                                "editableSuccess"=>"function(event, val, form, data) { }",
                            ]
                            ];
                        },
                        'format'=>'html',
                        'value' => function($gear) use ($model) {
                            $gear_no = $gear->eventOuterGear;
                            if ($gear_no->reception_time)
                                return substr($gear_no->return_time,0,10);
                            else 
                                return "-";
                        }
                    ],
                    [
                        'label' => Yii::t('app', 'Uwagi'),
                        'class'=>\kartik\grid\EditableColumn::className(),
                        'editableOptions' => function ($gear, $key, $index) use ($model) {
                            return [
                            'name'=>'description',
                                'formOptions' => [
                                        'action'=>['/outer-warehouse/save', 'gear_id'=>$gear->event_outer_gear],
                                    ],
                                'options' => [

                                ],
                            'pluginEvents' =>   [ 
                                "editableSuccess"=>"function(event, val, form, data) { }",
                            ]
                            ];
                        },
                        'format'=>'html',
                        'value' => function($gear) use ($model) {
                            $gear_no = $gear->eventOuterGear;
                            if ($gear_no->description)
                                return $gear_no->description;
                            else 
                                return "-";
                        }
                    ],
                    [
                        'label' => Yii::t('app', 'Odpowiedzialny'),
                    'class'=>\kartik\grid\EditableColumn::className(),
                    'format' => 'html',
                    'editableOptions' => function ($gear, $key, $index) use ($model) {
                        return [
                            'inputType' => Editable::INPUT_SELECT2, 
                            'name'=>'user_id',
                            'formOptions' => [
                                    'action'=>['/outer-warehouse/save', 'gear_id'=>$gear->event_outer_gear],
                                ],
                                'options' => [
                                    'data'=>\common\models\User::getList(),
                                    'options'=> [
                                        'multiple'=>false,
                                    ]
                                ],
                        'pluginEvents' =>   [ 
                            "editableSuccess"=>"function(event, val, form, data) { }",
                        ]
                        ];
                    },
                        'value' => function($gear) use ($model) {
                            $gear_no = $gear->eventOuterGear;
                            if ($gear_no->user_id)
                                return $gear_no->user->displayLabel;
                            else 
                                return "-";
                        }
                    ],
                    [
                        'label' => Yii::t('app', 'Packlisty'),
                        'format' => 'html',
                        'value' => function($gear) use ($model) {
                            $total = 0;
                            $content = "";
                            $pack = \common\models\PacklistOuterGear::find()->joinWith(['packlist'])->where(['event_outer_gear'=>$gear->event_outer_gear])->andWhere(['packlist.event_id'=>$model->id])->all();
                            foreach ($pack as $p)
                            {
                                $content .='<span class="label label-warning" style="background-color:'.$p->packlist->color.'">'.$p->quantity.'</span> ';
                                $total +=$p->quantity;
                            }
                            return $content;
                        },
                    ],


                    [
                        'class'=>\common\components\ActionColumn::className(),
                        'template'=>'{remove-assignment}',
                        'controllerId'=>'outer-warehouse',
                        'buttons' => [
                            'remove-assignment' => function ($url, $item, $key) use ($model) {
                                $button = '';
                                if (Yii::$app->user->can('eventEventEditEyeOuterGearDelete'))
                                {

                                    $button =  Html::a(Html::icon('remove'), ['/outer-warehouse/remove-from-packlist', 'id'=>$item->id], [
                                        'data'=> [
                                        ],
                                        'class'=>'remove-assignment-button-outer'
                                    ]);
                                }
                                return $button;
                            }
                        ]
                    ]
                ],
            ])
        ?>
    </div>
</div>
    </div>
</div>
<div class="row">
    <div class="col-md-12">
    <div class="panel_mid_blocks">
        <div class="panel_block">

        <h5><?php echo Yii::t('app', 'Lista sprzętu dodatkowego'); ?></h5>
        <?php if ($user->can('eventEventEditEyeGearManage')) {
            echo Html::a(Yii::t('app', 'Dodaj'), ['event-extra-item/create', 'event_id' => $model->id, 'packlist_id'=>$packlist->id], ['class' => 'btn btn-success add-extra-item']);
        } ?>
        <?php
            echo GridView::widget([
                'dataProvider'=>$model->getAssignedExtraItemsPacklist($packlist->id),
                'id'=>'extraItem'.$packlist->id,
                'tableOptions' => [
                    'class' => 'kv-grid-table table table-condensed kv-table-wrap'
                ],
                'columns' => [
                [
                'class' => 'yii\grid\CheckboxColumn',
                'multiple'=>true,
                ],
                    [
                        'class'=>\yii\grid\SerialColumn::className(),
                    ],
                            ['attribute' => 'id', 'visible' => false],
        [
            'label' => Yii::t('app', 'Nazwa'),
            'value'=>function($model)
            {
                return $model->eventExtraItem->name;
            }
        ],
        'quantity',
        [
                'label' => 'Sekcja',
                'value' => function($model){
                    if ($model->eventExtraItem->gearCategory)
                    {return $model->eventExtraItem->gearCategory->name;}
                    else
                    {return NULL;}
                },
        ],
        [
            'label' => Yii::t('app', 'Waga'),
            'value'=>function($model)
            {
                return $model->quantity*$model->eventExtraItem->weight;
            }
        ],
        [
            'label' => Yii::t('app', 'Objętość'),
            'value'=>function($model)
            {
                return $model->quantity*$model->eventExtraItem->volume;
            }
        ],
                    [
                        'label' => Yii::t('app', 'Packlisty'),
                        'format' => 'html',
                        'value' => function ($model) {
                            $total = 0;
                            $content = "";
                            foreach ($model->eventExtraItem->packlistGears as $p)
                            {
                                $content .='<span class="label label-warning" style="background-color:'.$p->packlist->color.'">'.$p->quantity.'</span> ';
                                $total +=$p->quantity;
                            }
                            return $content;
                        },
                    ],
        [
                        'class'=>\common\components\ActionColumn::className(),
                        'template'=>'{update}{delete}',
                        'controllerId'=>'event-extra-item',
        ],
                ],
            ])
        ?>
    </div>
</div>
    </div>
</div>


<?php 
$modalPacklistUrl = Url::to(['event/get-packlist-modal', 'id'=>$model->id, 'packlist_id'=>$packlist->id]);
$modalChangeTimeUrl = Url::to(['warehouse/group-change-time', 'id'=>$model->id, 'packlist_id'=>$packlist->id]);
$groupDeleteUrl = Url::to(['warehouse/gear-group-delete', 'id'=>$model->id]);

$this->registerJs('
    $(".remove-assignment-button-outer").click(function(e){
        e.preventDefault();
        $.ajax({
                    url: $(this).attr("href"),
                    type: "post",
                    async: false,
                    data: {},
                    success: function(data) {
                        $(this).parent().parent().remove();
                    },
                    error: function(data){                         
                    }
    });
    });
    $(".comment-edit").click(function(e){
        e.preventDefault();
        $("#comment_modal").modal("show").find(".modalContent").empty().load($(this).attr("href"));
    });
$("#action-form'.$packlist->id.'").on("beforeSubmit", function(e){
    //robimy tutaj serializację zaznaczonych checkboxów w trzech tabelkach i wyświetlamy opcję do wpisania ilości
    var gears = $("#allGearTable'.$packlist->id.'").yiiGridView("getSelectedRows");
    var ogears = $("#orderGear'.$packlist->id.'").yiiGridView("getSelectedRows");
    var extra = $("#extraItem'.$packlist->id.'").yiiGridView("getSelectedRows");
    var modal = $("#packlist_modal");
    var packlist = $(this).find("#gearactionform-action").val();

    if (packlist.substr(0,8)=="packlist")
    {
        pack_id = packlist.substr(9, packlist.length);
        $.ajax({
                    url: "'.$modalPacklistUrl.'",
                    type: "post",
                    async: false,
                    data: {gears:gears, ogears:ogears, extra:extra, packlist_id:pack_id},
                    success: function(data) {
                        modal.modal("show").find(".modalContent").empty().append(data);
                    },
                    error: function(data) {
                            
                    }
                }); 
    }
    if (packlist =="delete")
    {
        $.ajax({
                    url: "'.$groupDeleteUrl.'",
                    type: "post",
                    async: false,
                    data: {gears:gears, ogears:ogears, extra:extra},
                    success: function(data) {
                        $("#tab-gear").empty();
                        $("#tab-gear").load("'.Url::to(['event/gear-tab', 'id'=>$model->id]).'");
                    },
                    error: function(data) {
                            
                    }
                }); 
    }
    if (packlist =="change_time")
    {
        $.ajax({
                    url: "'.$modalChangeTimeUrl.'",
                    type: "post",
                    async: false,
                    data: {gears:gears},
                    success: function(data) {
                        modal.modal("show").find(".modalContent").empty().append(data);
                    },
                    error: function(data) {
                            
                    }
                });
    }
    return false;
}).submit(function(e){e.preventDefault();});

$(".gear-assignment-form").submit(function(e){e.preventDefault();});

$(".category-chackbox'.$packlist->id.'").click(function(){
    var c = $(this).prop("checked");
    var category = $(this).data("category");
    $("#allGearTable'.$packlist->id.'").find("tr").each(function(){
        cat = $(this).data("main-category");
        cat2 = $(this).data("sub-category");
        if ((category==cat)||(category==cat2))
        {
            $(this).find(":checkbox").prop("checked", c);
        }
    });
});
    ');
