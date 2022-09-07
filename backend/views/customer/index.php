<?php

use yii\helpers\Html;
use common\components\grid\GridView;
use yii\bootstrap\Modal;

/* @var $this yii\web\View */
/* @var $searchModel common\models\CustomerSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = Yii::t('app', 'Klienci');
$this->params['breadcrumbs'][] = $this->title;
$user = Yii::$app->user;
Modal::begin([
    'id' => 'offer-notes',
    'header' => Yii::t('app', 'Notatki'),
    'class' => 'modal',
        'size' => 'modal-lg',
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
<div class="customer-index">

    <p>
        <?php if ($user->can('clientClientsAdd')) { ?>
            <?=Html::a('<i class="fa fa-plus"></i> ' . Yii::t('app', 'Dodaj'), ['create'], ['class' => 'btn btn-success']); ?>
            <?=Html::a('<i class="fa fa-download"></i> ' . Yii::t('app', 'Import'), ['import'], ['class' => 'btn btn-success']); ?>
        <?php }
        ?>
    </p>
    <div class="panel_mid_blocks">
        <div class="panel_block">
    <?= GridView::widget([
        'dataProvider' => $dataProvider,
        'tableOptions' => [
            'class' => 'kv-grid-table table table-condensed kv-table-wrap'
        ],
        'filterModel' => $searchModel,
        'columns' => [
            ['class' => 'yii\grid\SerialColumn'],

            [
                'filter'=>false,
                'attribute'=>'logo',
                'value'=>function($model, $key, $index, $grid)
                {
                    return Html::img($model->getLogoUrl(), ['style'=>'width:100px']);
                },
                'format' => 'html',
            ],
            [
                'attribute'=>'name',
                'label'=>Yii::t('app', 'Nazwa firmy'),
                'format'=>'html',
                'value'=>function($model)
                {
                    $content = Html::a($model->name, ['view', 'id' => $model->id]);
                    if ($model->customerNotes)
                    {
                        $content .= Html::a(' <span class="label label-info"><i class="fa fa-comments"></i>'.count($model->customerNotes).'</span>', ['show-notes', 'id'=>$model->id], ['class'=>'show-notes']);
                    }
                    $content .= Html::a(' <span class="label"><i class="fa fa-plus"></i> '.Yii::t('app', 'Notatka').'</span>', ['add-note', 'id'=>$model->id], ['class'=>'show-notes']);
                    return $content;
                },
            ],
            'country',
            'address',
            'city',
            'phone',
            'email:email',
             [
                 'attribute' => 'groups',
                 'format'=>'html',
                 'value' => function($model)
                 {
                    $content = "";
                    foreach ($model->customerTypes as $t)
                    {
                        $content .=$t->name."<br/>";
                    }
                    return $content;
                 },
                 'filter' => \common\models\CustomerType::getList(),
                 'filterType' => GridView::FILTER_SELECT2,
                    'filterWidgetOptions' => [
                        //                    'data'=>\common\models\Event::getList(),
                        'options' => [
                            'placeholder' => Yii::t('app', 'Wybierz...'),
                        ],
                        'pluginOptions' => [
                            'allowClear'=>true,
                            'multiple'=>true,
                            'dropdownAutoWidth' => true,
                        ],
                    ],

             ],
            [
                'attribute' => 'customer',
                'filter' => \kartik\helpers\Enum::boolList(),
                'format' => 'boolean',
            ],
            [
                'attribute' => 'supplier',
                'filter' => \kartik\helpers\Enum::boolList(),
                'format' => 'boolean',
            ],
            [
                'attribute' => 'last_date',
                'label' => Yii::t('app', 'Ostatnia aktywność'),
                'value' => function($model){
                    if ($model->last_date)
                    {                     
                        return substr($model->last_date, 0, 10);
                    }else{
                        return "-";
                    }
                }
            ],
            [
                'attribute' => 'next_date',
                'format'=>'raw',
                'label' => Yii::t('app', 'Następna aktywność'),
                'value' => function($model){
                    if ($model->next_date)
                    {
                        
                          if (date('Y-m-d')>$model->next_date)
                          {
                              $return ='<span class="label label-danger"><i class="fa fa-calendar"></i> '.substr($model->next_date, 0, 10).'</span> ';
                          }else{
                              if (date("Y-m-d", time() + 60 * 60 * 48)>=$model->next_date)
                              {
                                $return ='<span class="label label-warning"><i class="fa fa-calendar"></i> '.substr($model->next_date, 0, 10).'</span> ';
                              }else{
                                $return ='<span class="label"><i class="fa fa-calendar"></i> '.substr($model->next_date, 0, 10).'</span> ';
                              }
                              
                          }                      
                        return $return;
                    }else{
                        return "-";
                    }
                }
            ],
            [
                'class' => 'yii\grid\ActionColumn',
                'visibleButtons' => [
                        'view' => $user->can('clientClientsSee'),
                        'update' => $user->can('clientClientsEdit'),
                        'delete' => $user->can('clientClientsDelete'),
                ]
            ],
        ],
    ]); ?>
</div>
    </div>
</div>

<?php

$this->registerJs('
    $(".show-notes").click(function(e){
        $("#offer-notes").find(".modalContent").empty();
        e.preventDefault();
        $("#offer-notes").modal("show").find(".modalContent").load($(this).attr("href"));
    });
    $(".show-notes").on("contextmenu",function(){
       return false;
    });
');