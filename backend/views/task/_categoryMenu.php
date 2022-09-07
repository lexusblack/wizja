<?php 
use yii\bootstrap\Nav;
use yii\bootstrap\NavBar;
use yii\helpers\Url;
$user = Yii::$app->user;

?>
<div class="clearfix">
<?php
         $orderedDone = \common\models\Task::find()->where(['creator_id'=>$user->identity->id])->andWhere(['OR', ['<>', 'status', 10], ['is', 'status', null]])->count();
         $ordered = \common\models\Task::find()->where(['creator_id'=>$user->identity->id])->count();
         $mineNotDone = $user->identity->getNotDoneCount();
         $mineAfterTime = $user->identity->getAfterTimeDoneCount();
         $mainItems =[ [
                'label'=>Yii::t('app', 'Moje').' <span class="label pull-right" style="margin-left:10px">'.$mineNotDone.'</span>'.' <span class="label label-danger pull-right" style="margin-left:10px">'.$mineAfterTime.'</span>',
                'url' => Url::to(['/task/index']),
                'active'=>$item == 1,
                'options'=>['class'=>'category-menu-link'],
                'visible'=>$user->can('menuTasksMine')
            ],
            [
                'label'=>Yii::t('app', 'Zlecone').' <span class="label label-warning pull-right" style="margin-left:10px">'.$orderedDone.'</span>',
                'url' => Url::to(['/task/ordered']),
                'active'=>$item == 2,
                'options'=>['class'=>'category-menu-link'],
                'visible'=>$user->can('menuTasksOrdered')

            ],
            [
                'label'=>Yii::t('app', 'Wg eventów'),
                'url' => Url::to(['/task/events']),
                'active'=>$item == 3,
                'options'=>['class'=>'category-menu-link'],
                'visible'=>$user->can('menuTasksEvents')
            ],
            [
                'label'=>Yii::t('app', 'Pozostałe'),
                'url' => Url::to(['/task/all']),
                'active'=>$item == 4,
                'options'=>['class'=>'category-menu-link'],
                'visible'=>$user->can('menuTasksOthers')
            ],
            ];

        echo  Nav::widget([
            'items' => $mainItems,
            'options' => ['class' =>'nav-pills newsystem-bg'],
            'encodeLabels' => false, // set this to nav-tab to get tab-styled navigation
        ]);
?>
</div>
