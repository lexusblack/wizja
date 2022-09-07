<?php
use yii\bootstrap\Html;
use common\components\grid\GridView;
use yii\helpers\Url;
/* @var $model \common\models\Gear; */
?>
<div class="panel-body">
<h3><?php echo Yii::t('app', 'Zgłoszone błędy'); ?></h3>
<div class="row">
    <div class="col-md-12">
        <div class="panel_mid_blocks">
            <div class="panel_block">
        <?php
        echo GridView::widget([
            'dataProvider'=>$model->getAssignedErrors(),
            'columns' => [
               	'id',
                'name',
                'mail',
                [
                    'attribute' => 'status',
                    'label' => Yii::t('app', 'Status'),
                    'value' => function($model) {
                    	$statuts = [1=>'Zgłoszony', 2=>'Rozwiązany', 3=>'Odrzucony'];
                    	return $statuts[$model->status];
                    }
                ],
                [
                    'attribute' => 'type',
                    'label' => Yii::t('app', 'Typ'),
                    'value' => function($model) {
                    	$statuts = [1=>Yii::t('app', 'Błąd'), 2=>Yii::t('app','Pytanie'), 3=>Yii::t('app','Nowa funkcjonalność')];
                    	return $statuts[$model->type];
                    }
                ],
                [
                    'attribute' => 'priority',
                    'label' => Yii::t('app', 'Priorytet'),
                    'value' => function($model) {
                    	$statuts = [1=>Yii::t('app', 'Niski'), 2=>Yii::t('app','Wysoki'), 3=>Yii::t('app','Uniemożliwiający pracę')];
                    	return $statuts[$model->priority];
                    }
                ],
                ],
        ]);
        ?>
            </div>
        </div>
    </div>
</div>
</div>

