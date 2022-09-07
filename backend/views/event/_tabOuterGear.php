<?php
use common\models\EventOuterGear;
use common\models\OutcomesGearOuter;
use yii\bootstrap\Html;
use common\components\grid\GridView;
use kartik\editable\Editable;
use common\helpers\Url;
use kartik\tabs\TabsX;


/* @var $model \common\models\Event; */
$user = Yii::$app->user;
?>
<div class="panel-body">
<h3><?php echo Yii::t('app', 'Sprzęt'); ?></h3>
<?php
            $tabItems = [
                [
                    'label'=>Yii::t('app', 'Zapotrzebowanie na sprzęt zewnętrzny').$model->getAssignedOuterGearModelsNumber(),
                    'content'=>$this->render('_tabOuterGear2', ['model'=>$model]),
                    'options'=> [
                        'id'=>'tab-outer1',
                    ]
                ],
                [
                    'label'=>Yii::t('app', 'Sprzęt zarezerwowany u wypożyczającego'),
                    'content'=>$this->render('_tabOuterGear1', ['model'=>$model]),
                                        'options'=> [
                        'id'=>'tab-outer3',
                    ]
                ],
                
                [
                    'label'=>Yii::t('app', 'Konflikty').$model->getConflictCount(),
                    'content'=>$this->render('_tabOuterGear3', ['model'=>$model]),
                    'active'=>true,
                    'options'=> [
                        'id'=>'tab-outer2',
                    ]
                ]
    
            ]; 

echo TabsX::widget([
                'items'=>$tabItems,
                'encodeLabels'=>false,
                ]);
            ?>
</div>