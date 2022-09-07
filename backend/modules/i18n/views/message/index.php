<?php

use yii\helpers\Html;
use common\components\grid\GridView;

/* @var $this yii\web\View */
/* @var $searchModel common\models\MessageSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = Yii::t('app', 'Wiadomości');
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="message-index">
    <div class="panel panel-default">
    <?= GridView::widget([
        'dataProvider' => $dataProvider,
        'filterModel' => $searchModel,
        'columns' => [
            [
                'attribute'=>'message',
                'value'=>'source.message',
                'label' => Yii::t('app', 'Fraza')
            ],
            [
                'attribute'=>'language',
                'filter'=>\common\models\Language::getTranslationList(),
            ],
            [
                'class'=>\kartik\grid\EditableColumn::className(),
                'attribute'=>'translation',
                'editableOptions' => [
                    'asPopover' => false,
                    'formOptions' => [
                        'action' => ['edit'],
                    ]
                ]
            ],
        ],
    ]); ?>
    </div>
</div>