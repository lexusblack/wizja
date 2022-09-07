<?php
/* @var $this \yii\web\View */
/* @var $warehouse \common\models\form\WarehouseSearch; */

use yii\grid\GridView;
use yii\bootstrap\Html;
use yii\helpers\Url;

// Case list

$event = isset($event) ? $event : null;
?>

<?php if ($warehouse->showGroups == true): ?>
    <div class="gear gear-groups">
        <?= GridView::widget([
            'dataProvider' => $warehouse->gearGroupDataProvider,
            'dataColumnClass'=>\common\components\grid\NotNullDataColumn::className(),
            'layout'=>'{items}',
            'filterModel' => null,
            'options' => ['class'=>'warning'],
            'columns' => [
                [
                    'class' => 'yii\grid\CheckboxColumn',
                    'header' => '',
                    'checkboxOptions' => function ($model, $key, $index, $column) use ($event) {
                        $checked = false;
                        if (isset($_COOKIE['checkbox-group'][$model->id])) {
                            $checked = true;
                        }
                        return [
                            'checked' => $checked,
                            'class'=>'checkbox-group'
                        ];
                    },
                ],
                [
                    'content'=>function($model, $key, $index, $grid) use ($warehouse)
                    {

                        $activeGroup = $warehouse->activeGroup;
                        $icon = $activeGroup==$model->id ? 'arrow-up' : 'arrow-down';
                        $id = $activeGroup==$model->id ?  null : $model->id;
                        return Html::a(Html::icon($icon), Url::current(['activeGroup'=>$id]), ['class'=> "category-menu-link " . $icon]);


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
                    'header' => Yii::t('app', 'Numery urządzeń'),
                    'value'=>'itemNumbers',
                ],
                [
                    'header' => Yii::t('app', 'Stan'),
                    //z użądzeń wyciągnąć stany.
                ],
                [
                    'label'=>Yii::t('app', 'Ilość urządzeń'),
                    'content'=>function($model, $key, $index, $grid)
                    {
                        return $model->getItemsCount();
                    }

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
            'afterRow' => function($model, $key, $index, $grid) use ($warehouse, $gearColumns, $checkbox)
            {
                $content = $this->render('_groupItems', [
                    'model'=>$model,
                    'activeGroup'=>$warehouse->activeGroup,
                    'gearGroupItemDataProvider'=>$warehouse->getGearGroupItemDataProvider(),
                    'checkbox'=>$checkbox,
                ]);

                $content = Html::tag('div', $content, ['class'=>'wrapper']);

                return Html::tag('tr', Html::tag('td', $content, ['colspan'=>sizeof($gearColumns)]));
            },
        ]); ?>
    </div>
<?php endif; ?>