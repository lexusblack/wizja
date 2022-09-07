<?php

use yii\helpers\Html;
use yii\widgets\DetailView;
use kartik\grid\GridView;

/* @var $this yii\web\View */
/* @var $model common\models\Chat */

$this->title = $model->name;
$this->params['breadcrumbs'][] = ['label' => Yii::t('app', 'Chat'), 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="chat-view">

    <div class="row">
        <div class="col-sm-9">
            <h2><?= 'Chat'.' '. Html::encode($this->title) ?></h2>
        </div>
        <div class="col-sm-3" style="margin-top: 15px">
            
            <?= Html::a(Yii::t('app', 'Edytuj'), ['update', 'id' => $model->id], ['class' => 'btn btn-primary']) ?>
            <?= Html::a(Yii::t('app', 'Usuń'), ['delete', 'id' => $model->id], [
                'class' => 'btn btn-danger',
                'data' => [
                    'confirm' => Yii::t('app', 'Czy na pewno usunąć ten model?'),
                    'method' => 'post',
                ],
            ])
            ?>
        </div>
    </div>

    <div class="row">
<?php 
    $gridColumn = [
        ['attribute' => 'id', 'visible' => false],
        'name',
        'last_message',
        [
            'attribute' => 'createBy.username',
            'label' => Yii::t('app', 'Stworzony przez'),
        ],
        [
            'attribute' => 'event.name',
            'label' => Yii::t('app', 'Wydarzenie'),
        ],
    ];
    echo DetailView::widget([
        'model' => $model,
        'attributes' => $gridColumn
    ]); 
?>
    </div>
    
    <div class="row">
<?php
if($providerChatMessage->totalCount){
    $gridColumnChatMessage = [
        ['class' => 'yii\grid\SerialColumn'],
            ['attribute' => 'id', 'visible' => false],
            [
                'attribute' => 'userFrom.username',
                'label' => Yii::t('app', 'Od użytkownika')
            ],
            [
                'attribute' => 'userTo.id',
                'label' => Yii::t('app', 'Do użytkownika')
            ],
                        'text:ntext',
            'read',
    ];
    echo Gridview::widget([
        'dataProvider' => $providerChatMessage,
        'pjax' => true,
        'pjaxSettings' => ['options' => ['id' => 'kv-pjax-container-chat-message']],
        'panel' => [
            'type' => GridView::TYPE_PRIMARY,
            'heading' => '<span class="glyphicon glyphicon-book"></span> ' . Html::encode(Yii::t('app', 'Wiadomość')),
        ],
        'export' => false,
        'columns' => $gridColumnChatMessage
    ]);
}
?>
    </div>
    
    <div class="row">
<?php
if($providerChatUser->totalCount){
    $gridColumnChatUser = [
        ['class' => 'yii\grid\SerialColumn'],
            ['attribute' => 'id', 'visible' => false],
            [
                'attribute' => 'user.username',
                'label' => Yii::t('app', 'Użytkownik')
            ],
                ];
    echo Gridview::widget([
        'dataProvider' => $providerChatUser,
        'pjax' => true,
        'pjaxSettings' => ['options' => ['id' => 'kv-pjax-container-chat-user']],
        'panel' => [
            'type' => GridView::TYPE_PRIMARY,
            'heading' => '<span class="glyphicon glyphicon-book"></span> ' . Html::encode(Yii::t('app', 'Użytkownik')),
        ],
        'export' => false,
        'columns' => $gridColumnChatUser
    ]);
}
?>
    </div>
</div>
