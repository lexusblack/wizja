<?php

use yii\helpers\Html;
use yii\widgets\DetailView;
use kartik\grid\GridView;

/* @var $this yii\web\View */
/* @var $model common\models\ChatMessage */

$this->title = $model->id;
$this->params['breadcrumbs'][] = ['label' => 'Chat Message', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="chat-message-view">

    <div class="row">
        <div class="col-sm-9">
            <h2><?= 'Chat Message'.' '. Html::encode($this->title) ?></h2>
        </div>
        <div class="col-sm-3" style="margin-top: 15px">
            
            <?= Html::a('Update', ['update', 'id' => $model->id], ['class' => 'btn btn-primary']) ?>
            <?= Html::a('Delete', ['delete', 'id' => $model->id], [
                'class' => 'btn btn-danger',
                'data' => [
                    'confirm' => 'Are you sure you want to delete this item?',
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
        [
            'attribute' => 'userFrom.username',
            'label' => 'User From',
        ],
        [
            'attribute' => 'userTo.id',
            'label' => 'User To',
        ],
        [
            'attribute' => 'chat.name',
            'label' => 'Chat',
        ],
        'text:ntext',
        'read',
    ];
    echo DetailView::widget([
        'model' => $model,
        'attributes' => $gridColumn
    ]); 
?>
    </div>
</div>
