<?php
use backend\modules\offers\models\OfferExtraItem;
use common\helpers\ArrayHelper;
use yii\bootstrap\Html;
use yii\helpers\Url;
use kartik\grid\GridView;
use kartik\editable\Editable;
use yii\bootstrap\Modal;

/* @var $model \common\models\Event; */
$user = Yii::$app->user;
Modal::begin([
    'id' => 'offer-status-edit',
    'header' => Yii::t('app', 'Edytuj status'),
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
<div class="panel-body">
<h3><?php echo Yii::t('app', 'Oferty'); ?></h3>
<div class="row">
    <div class="col-md-12">
        <?php
        if ($user->can('eventsEventEditEyeOfferAdd')) {
            echo Html::a(Yii::t('app', 'Dodaj nową'), ['/offer/default/create', 'event_id' => $model->id], ['class' => 'btn btn-success']);
           echo Html::a(Yii::t('app', 'Stwórz z eventu'), ['/offer/default/create-from-event', 'event_id' => $model->id], ['class' => 'btn btn-success']);
        }
        if ($user->can('eventsEventEditEyeOfferImport')) {
            echo Html::a(Yii::t('app', 'Importuj z ofert'), ['/offer/default/assign-to-event', 'event_id' => $model->id], ['class' => 'btn btn-success']);
        }
        if ($user->can('eventsEventEditEyeOfferGear')) {
            $offers = $model->getPlanningOffers();
            if (isset($offers['error']) && $offers['error']) { 
                echo Html::a(Yii::t('app', 'Dodaj do sprzętówki'), '#', ['class' => 'btn btn-default', 'title'=>Yii::t('app', 'Brak zaakceptowanej oferty'), 'onclick'=>'alert("'.Yii::t('app', 'Brak zaakceptowanej oferty').'"); return false;']);    
            }else{
                  echo Html::a(Yii::t('app', 'Dodaj do sprzętówki'), ['/warehouse/assign-gear-item-to-offer', 'event_id' => $model->id], ['class' => 'btn btn-success']);          
            }

        } ?>
    </div>
</div>
<div class="row">
    <div class="col-md-12">
        <?php
            $assignedOffers = $model->getAssignedOffers(); 
            $offers = $assignedOffers->getModels();
            $gcat = \common\models\GearCategory::getMainList(true);
            $columns = [
        [
            'label' => Yii::t('app', 'Duplkuj'),
            'format' => 'html',
            'value' => function ($model) {
                return Html::a('<i class="fa fa-copy"></i>', ['/offer/default/duplicate', 'id' => $model['id']], ['class'=>'btn btn-warning btn-circle']) ;                  
            },
            'visible' => $user->can('menuOffersViewDuplicate')
        ],
                [
                    'attribute'=>'name',
                    'value' => function($model, $key, $index, $column) use ($user)
                    {
                        if ($user->can('menuOffersEdit'))
                            return Html::a( $model->name, Url::to(['/offer/default/view', 'id'=>$model->id]));
                        else
                            return Html::a( $model->name, Url::to(['/offer/default/pdf2', 'id'=>$model->id]));
                    },
                    'format'=>'html',
                ],
                'offer_date',
                [
                    'label'=>Yii::t('app', 'Przygotował'),
                    'attribute'=>'manager_id',
                    'value' => function($model, $key, $index, $column)
                    {
                        $list = \common\models\User::getList();
                        if ($model->manager_id == null) {
                            return Yii::t('app', 'Nikt');
                        }
                        return $list[$model->manager_id];
                    },
                ]];

            if ($user->can('menuOffersEdit'))
            {
                $columns[] =
                [
                    'attribute'=>'status',
                    'format' => 'raw',
                    'value' => function($model, $key, $index, $column)
                    {
                        
                        return  $model->getStatusButton("offer-status-button");
                    },
                ];
            }else{
                $columns[] =
                [
                    'attribute'=>'status',
                    'format' => 'raw',
                    'value' => function($model, $key, $index, $column)
                    {
                        
                        return  $model->getStatusButton();
                    },
                ];
            }
            if ($user->can('menuOffersEdit')){
            foreach ($gcat as $key => $cat) {
                $columns[] = [
                    'label'=>$cat->name,
                    'value' => function($model, $key, $index, $column) use ($cat)
                    {
                        $vals = $model->getOfferValues();
                        return isset($vals[$cat->name]) ? Yii::$app->formatter->asCurrency($vals[$cat->name]) : Yii::$app->formatter->asCurrency(0);
                    },
                ];
            }

            $columns[] = [
                'label' => Yii::t('app', 'Transport'),
                'value' => function ($model){
                    $vals = $model->getOfferValues();
                    return isset($vals[Yii::t('app', 'Transport')]) ? Yii::$app->formatter->asCurrency($vals[Yii::t('app', 'Transport')]) : Yii::$app->formatter->asCurrency(0);
                }
            ];
            $columns[] = [
                'label' => Yii::t('app', 'Obsługa'),
                'value' => function ($model) {
                    $vals = $model->getOfferValues();
                    return isset($vals[Yii::t('app', 'Obsługa')]) ? Yii::$app->formatter->asCurrency($vals[Yii::t('app', 'Obsługa')]) : Yii::$app->formatter->asCurrency(0);
                }
            ];
            $columns[] = [
                'label' => Yii::t('app', 'Inne'),
                'value' => function ($model) {
                    $vals = $model->getOfferValues();
                    return isset($vals[Yii::t('app', 'Inne')]) ? Yii::$app->formatter->asCurrency($vals[Yii::t('app', 'Inne')]) : Yii::$app->formatter->asCurrency(0);
                }
            ];
            $columns[] = [
                'label'=>Yii::t('app', 'Suma'),
                'value' => function ($model) {
                    $vals = $model->getOfferValues();
                    return isset($vals[Yii::t('app', 'Suma')]) ? Yii::$app->formatter->asCurrency($vals[Yii::t('app', 'Suma')]) : Yii::$app->formatter->asCurrency(0);
                }
            ];
            }

            if ($user->can('eventsEventEditEyeOfferDelete')) {
                $columns[] = [
                    'value' => function($model) {
                        return Html::a(Html::icon('remove'), ['/offer/default/offer-event', 'event_id'=>$model->event_id], [ 'class'=>'btn btn-danger btn-sm delete-from-event','data' => ['id' => $model->id]]);
                    },
                    'format' => 'raw',
                ];
            }

            ?>

    <div class="panel_mid_blocks">
        <div class="panel_block">
<?php
            echo GridView::widget([
	            'layout' => "{items}\n{pager}",
                'dataProvider'=>$assignedOffers,
                'tableOptions' => [
                    'class' => 'kv-grid-table table table-condensed kv-table-wrap'
                ],
                'columns' => $columns,
            ]);
        ?>
    </div>
</div>
    </div>
</div>
</div>

<?php $this->registerCss('
    .offer-status-button{
        cursor:pointer;
    }
');

$this->registerJs('
    $(".offer-status-button").click(function(e){
        e.preventDefault();
        $("#offer-status-edit").modal("show").find(".modalContent").load("'.Url::to(['/offer/default/change-statut']).'?id="+$(this).data("offerid"));
    })
');


?>

<?php $this->registerJs('

    $(".sub_block").on("click",function(){
        var _this = $(this),
        icon = _this.find("i"),
        box = _this.closest("tr").next(".offer-gear-details");
        if(_this.hasClass("active")){
            icon.removeClass("glyphicon-arrow-up").addClass("glyphicon-arrow-down");
            _this.removeClass("active");
            box.hide(300);
        } else {
            icon.removeClass("glyphicon-arrow-down").addClass("glyphicon-arrow-up");
            _this.addClass("active");
            box.show(300);
        }

        return false;
    });

    $(".delete-from-event").on("click",function(){
        if (confirm("'.Yii::t('app', 'Po usunięciu wszystkie przypisane do oferty egzemplarzy będą też usunięty').'")) {
            var _this = $(this),
            data = {
                itemId: _this.data("id"),
                add: 0
            };
            $.post(_this.attr("href"), data, function(response){
                location.reload();
            });
        } 

        return false;
    });
');?>

<script type="text/javascript">
    function loadTabsAfterChange()
    {
        <?php if ($user->can('eventsEventEditEyeCrew'))
        { ?>
            $("#tab-crew").empty().load("<?=Url::to(['event/crew-tab', 'id'=>$model->id])?>");
        <?php } ?>
        <?php if ($user->can('eventsEventEditEyeVehicles'))
        { ?>
            $("#tab-vehicle").empty().load("<?=Url::to(['event/vehicle-tab', 'id'=>$model->id])?>");
        <?php } ?>
        <?php if ($user->can('eventsEventEditEyeFinance'))
        { ?>
            $("#tab-finances").empty().load("<?=Url::to(['event/finance-tab', 'id'=>$model->id])?>");
        <?php } ?> 
        $("#tab-offer").empty().load("<?=Url::to(['event/offer-tab', 'id'=>$model->id])?>");       
    }
</script>