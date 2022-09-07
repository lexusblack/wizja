<?php

use yii\helpers\Html;
use common\components\grid\GridView;
use yii\helpers\Url;

/* @var $this yii\web\View */
/* @var $searchModel common\models\PersonalSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title =  Yii::t('app', 'Spotkanie prywatne');
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="personal-index">

    <p>
        <?= Html::a('<i class="fa fa-plus"></i> ' .  Yii::t('app', 'Dodaj'), ['create'], ['class' => 'btn btn-success']) ?>
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
                'attribute'=>'name',
                'label'=>Yii::t('app', 'Nazwa'),
                'format'=>'html',
                'value'=>function($model)
                {
                    $content = Html::a($model->name, ['view', 'id' => $model->id]);
                    return $content;
                },
            ],
            'location',
            'start_time',
            'end_time',
            [
                'label' =>  Yii::t('app', 'Przypomnienie sms'),
                'format' => 'html',
                'value' => function($model) {
                    if ($model->notificationSms) {
                        if ($model->notificationSms) {
                            $button = null;
                            if (new DateTime() < new DateTime($model->notificationSms->sending_time)) {
                                $button = " " . Html::a( Yii::t('app', 'Usuń'), ['personal/delete-sms', 'id' => $model->id], ['class' => 'btn btn-primary delete_sms']);
                            }
                            return $model->notificationSms->sending_time . $button;
                        }
                    }
                }
            ],
            [
                'label' =>  Yii::t('app', 'Przeypomnienie mailowe'),
                'format' => 'html',
                'value' => function($model) {
                    if ($model->notificationMail) {
                        $button = null;
                        if (new DateTime() < new DateTime($model->notificationMail->sending_time)) {
                            $button = " " . Html::a('Usuń', ['personal/delete-mail', 'id' => $model->id], ['class' => 'btn btn-primary delete_sms']);
                        }
                        return $model->notificationMail->sending_time . $button;
                    }
                }
            ],

            ['class' => 'yii\grid\ActionColumn'],
        ],
    ]); ?>
        </div>
    </div>
</div>

<?php

$this->registerJs('

$(".delete_sms").click(function(e){
    e.preventDefault();
    $(this).parent().html("-");
    $.post($(this).attr("href"));

});

');