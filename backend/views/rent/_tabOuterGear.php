<?php
use yii\bootstrap\Html;
use common\helpers\Url;
use kartik\tabs\TabsX;


/* @var $model \common\models\Event; */
$user = Yii::$app->user;
?>
<div class="panel-body">
<h3><?php echo Yii::t('app', 'Sprzęt zewnętrzny'); ?></h3>
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
                ]
    
            ]; 

echo TabsX::widget([
                'items'=>$tabItems,
                'encodeLabels'=>false,
                ]);
            ?>
</div>