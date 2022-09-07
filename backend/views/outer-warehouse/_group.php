<?php
/* @var $this \yii\web\View */
use yii\grid\GridView;
use yii\bootstrap\Html;
use yii\helpers\Url;

$event = isset($event) ? $event : null;
?>

<?php if ($this->context->showGroups == true): ?>
    <div class="gear gear-groups">
<!--        <h3>Case</h3>-->
        <?= GridView::widget([
            'dataProvider' => $gearGroupDataProvider,
            'filterModel' => null,
            'options' => ['class'=>'warning'],
            'columns' => [
                [
                    'class' => 'yii\grid\CheckboxColumn',
                    'checkboxOptions' => function ($model, $key, $index, $column) use ($event) {
                        return [
                            'checked' => $model->getIsGroupAssigned($event),
                            'class'=>'checkbox-group'
                        ];
                    },
                    'visible'=>$checkbox,
                ],
                [
                    'content'=>function($model, $key, $index, $grid) use ($activeGroup)
                    {


                        $icon = $activeGroup==$model->id ? 'arrow-up' : 'arrow-down';
                        $id = $activeGroup==$model->id ?  null : $model->id;
                        return Html::a(Html::icon($icon), Url::current(['activeGroup'=>$id]));


                    },
                    'contentOptions'=>['class'=>'text-center'],
                ],
                [
                    'content'=>function()
                    {
                        return Html::img('@web/../img/case.jpg', ['style'=>'width:100px;']);
                    }
                ],
                [
                    'header' =>  Yii::t('app', 'Numery urządzeń'),
                    'value'=>'itemNumbers',
                ],
                [
                    'header' =>  Yii::t('app', 'Stan'),
                    //z użądzeń wyciągnąć stany.
                ],
                'location',
                'weight',
                'width',
                'height',
                'depth',
                [
                    'class' => 'yii\grid\ActionColumn',
                    'template' => '{update}{delete}',
                    'urlCreator' =>  function($action, $model, $key, $index)
                    {
                        $params = is_array($key) ? $key : ['id' => (string) $key];
                        $params[0] = 'gear-group/' . $action;

                        return Url::toRoute($params);
                    }
                ],

            ],
            'afterRow' => function($model, $key, $index, $grid) use ($gearGroupDataProvider, $activeGroup, $gearGroupItemDataProvider, $gearColumns, $checkbox)
            {
                $content = $this->render('_groupItems', [
                    'model'=>$model,
                    'activeGroup'=>$activeGroup,
                    'gearGroupItemDataProvider'=>$gearGroupItemDataProvider,
                    'checkbox'=>$checkbox,
                ]);


                return Html::tag('td', $content, ['colspan'=>sizeof($gearColumns)]);
            },
        ]); ?>
    </div>
<?php endif; ?>