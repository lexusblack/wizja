<?php
/* @var $model \common\models\Customer; */
/* @var $this \yii\web\View; */

use yii\bootstrap\Html;
use kartik\grid\GridView;
use kartik\widgets\ActiveForm;
use kartik\tabs\TabsX;

if (!Yii::$app->user->can('clientClientsSeeProjects')) {
    return;
}

?>
<div class="panel-body">
<h3><?php echo Yii::t('app', 'Projekty'); ?></h3>
<?php
$tabItems = [];
                
                    $tabItems[] = ['label' => Yii::t('app', 'Wydarzenia'),
                        'content' => $this->render('_tabEvents', ['model' => $model]),
                        'options' => ['id' => 'tab-events',]

                    ];
                    $tabItems[] = ['label' => Yii::t('app', 'Wypożyczenia'),
                        'content' => $this->render('_tabRents', ['model' => $model]),
                        'options' => ['id' => 'tab-rents',]

                    ];
echo TabsX::widget([
                    'items'=>$tabItems,
                    'encodeLabels'=>false,
                    'enableStickyTabs'=>true,
                ]);
?>
</div>