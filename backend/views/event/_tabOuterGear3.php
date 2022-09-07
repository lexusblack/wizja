<?php
use common\models\EventOuterGear;
use common\models\OutcomesGearOuter;
use yii\bootstrap\Html;
use common\components\grid\GridView;
use kartik\editable\Editable;
use common\helpers\Url;

/* @var $model \common\models\Event; */
$user = Yii::$app->user;
use yii\bootstrap\Modal;
Modal::begin([
    'header' =>"<h4 class='modal-title'>". Yii::t('app', 'Rozwiąż konflikt')."</h4>",
    'id' => 'conflict_resolve_modal',
    'class'=>'inmodal inmodal',
    'size' => 'modal-lg',
    'options' => [
        'tabindex' => false,
    ],
    'clientOptions' => [
    'keyboard'=> false,
        'backdrop'=> 'static'
    ]
]);
echo "<div class=\"modalContent\"></div>";
Modal::end();
?>
<div class="panel-body">
<div class="row">
    <div class="col-md-12" style="padding:0">
    <div class="panel_mid_blocks">
        <div class="panel_block">
        <h5><?php echo Yii::t('app', 'Konflikty - brak sprzętu w magazynie'); ?></h5>
        <?php
            echo GridView::widget([
                'dataProvider'=>$model->getConflicts(),
                'tableOptions' => [
                    'class' => 'kv-grid-table table table-condensed kv-table-wrap'
                ],
                'afterRow' => function($model, $key, $index, $grid)
                {
                    $content = "<div class='conflict-calendar' style='height:400px'></div>";
                    return Html::tag('tr',Html::tag('td', $content, ['colspan'=>8, 'style'=>"padding:0; background-color:white;"]), ['class'=>'event-task-details']);
                },
                'columns' => [
                    [
                        'class'=>\yii\grid\SerialColumn::className(),
                    ],
                    [
                        'value'=>function($model)
                        {
                            return Html::a('<i class="fa fa-calendar"></i>', ['/event/conflict-calendar', 'conflict_id'=>$model->id], ['class'=>"show-calendar btn btn-xs btn-default"]);
                        },
                        'format'=>'html'
                    ],
                    [
                        'attribute'=>'photo',
                        'value'=>function ($model, $key, $index, $column)
                        {
                            /* @var $model \common\models\OuterGear */
                            if ($model->gear->photo == null)
                            {
                                return '-';
                            }
                            return Html::a(Html::img($model->gear->getPhotoUrl(), ['width'=>50]), ['outer-gear-model/view', 'id'=>$model->id]);
                        },
                        'format'=>'html',
                    ],
                    [
                        'attribute'=>'gear_id',
                        'label'=>Yii::t('app', 'Nazwa'),
                        'value'=>function ($model, $key, $index, $column)
                        {
                            return Html::a($model->gear->name, ['gear/view', 'id'=>$model->gear_id]);
                        },
                        'format'=>'html',
                    ],
                    [
                        'label' => Yii::t('app', 'Grupa sprzętowa'),
                        'value' => function($model){
                            if (isset($model->packlistGear))
                                return $model->packlistGear->packlist->name;
                            else
                                return "-";
                        }
                    ],
                    [
                        'label' => Yii::t('app', 'Brakujących'),
                        'format'=>'html',
                        'attribute'=>'quantity'
                    ],
                    [
                        'label' => Yii::t('app', 'Zarezerwowanych'),
                        'format'=>'html',
                        'attribute'=>'added'
                    ],
                    [
                        'label' => Yii::t('app', 'Status'),
                        'class'=>\kartik\grid\EditableColumn::className(),
                        'format' => 'html',
                        'editableOptions' => function ($model, $key, $index) {
                            return [
                                'inputType' => Editable::INPUT_SELECT2,
                                'name'=>'resolved',
                                'formOptions' => [
                                        'action'=>['/event/resolve-conflict', 'id'=>$model->id],
                                    ],
                                    'options' => [
                                        'data'=>[0=>Yii::t('app', 'Nierozwiązany'), 1=>Yii::t('app', 'Rozwiązany')],
                                        'options'=> [
                                            'multiple'=>false,
                                        ]
                                    ]
                            ];
                        },
                        'value' => function ($model){
                            if ($model->resolved)
                                return Yii::t('app', 'Rozwiązany');
                            else
                                return Yii::t('app', 'Nierozwiązany');
                        }
                    ],
                    [
                        'format'=>'raw',
                        'value'=>function($model){ 
                            $category = $model->gear->category;
                            $categories = $category->parents()->all();
                            if (count($categories) > 1) {
                                $category_ = $categories[1];
                            }
                            return Html::a(Yii::t('app', 'Rozwiąż'), '#', ['class'=>'btn btn-primary btn-xs', 'onclick'=>'openResolveModal('.$model->id.', '.$category->id.'); return false;'])." ".Html::a(Yii::t('app', 'Usuń'), '#', ['class'=>'btn btn-danger btn-xs', 'onclick'=>'openDeleteModal('.$model->id.'); return false;']);;

                        }
                    ]
                ],
            ])
        ?>
    </div>
</div>
    </div>
</div>
</div>
<?php $spinner =  "<div class='sk-spinner sk-spinner-double-bounce'><div class='sk-double-bounce1'></div><div class='sk-double-bounce2'></div></div>"; ?>
<script src="https://unpkg.com/sweetalert/dist/sweetalert.min.js"></script>
<script type="text/javascript">
    function openResolveModal(id, category)
    {
        /*
        swal({
            title: "<?=Yii::t('app', 'Rowiąż konflikt')?>",
            icon:"success",
            text: "<?=Yii::t('app', 'Wybierz sposób rozwiązania konfliktu')?>",
          buttons: {
            cancel: "<?=Yii::t('app', 'Anuluj')?>",
            inner: {
              text: "<?=Yii::t('app', 'Wybierz zamiennik')?>",
              value: "inner",
            },
            outer: {
              text: "<?=Yii::t('app', 'Wypożycz')?>",
              value: "outer",
            }
          },
        })
        .then((value) => {
          switch (value) {
         
            case "inner":
              location.href = "<?=Url::to(['warehouse/assign', 'id'=>$model->id, 'type'=>'event']);?>&conflict="+id+"&c="+category
              break;
         
            case "outer":
              location.href = "<?=Url::to(['outer-warehouse/assign', 'id'=>$model->id, 'type'=>'event']);?>&conflict="+id+"&c="+category
              break;        
          }
        });
        */
        var modal = $("#conflict_resolve_modal");
        var $link="<?=Url::to(['event/conflict-modal']);?>?conflict="+id+"&c="+category;
        modal.modal("show");
        modal.find(".modalContent").empty();
        modal.find(".modalContent").append("<?=$spinner?>");
        modal.find(".modalContent").load($link); 
    }

    function openDeleteModal(id)
    {
        swal({
            title: "<?=Yii::t('app', 'Usuń konflikt')?>",
            icon:"success",
            text: "<?=Yii::t('app', 'Czy na pewno chcesz usunąć ten konflikt?')?>",
          buttons: {
            cancel: "<?=Yii::t('app', 'Anuluj')?>",
            yes: {
              text: "<?=Yii::t('app', 'Tak')?>",
              value: "yes",
            }
          },
        })
        .then((value) => {
          switch (value) {
         
            case "yes":
              $.ajax({
                    data: { id:id},
                    type: 'POST',
                    url: "/admin/event/delete-conflict?id="+id,
                    success:function(){ location.reload();}
                });
              break;      
          }
        });
    }
</script>
<?php
$this->registerJs('

$(".show-calendar").click(function(e)
{
    e.preventDefault();

    if ($(this).hasClass("opened"))
    {
        $(this).parent().parent().next().slideUp();
    }else{
        $(this).parent().parent().next().show();
        $(this).parent().parent().next().find(".conflict-calendar").empty().load($(this).attr("href"));
    }
    $(this).toggleClass("opened");

});
$(".show-calendar").on("contextmenu",function(){
       return false;
    });
');



$this->registerCss('

.row-all-gear-out {
    background-color: #449D44;
    color: white;
}
.row-all-gear-out a {
    color: white;
}
');