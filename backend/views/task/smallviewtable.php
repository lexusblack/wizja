<?php

use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $model common\models\TaskSchema */
/* @var $form yii\widgets\ActiveForm */

?>
<td data-col-seq="1">
    <?php
                    $content = '<div class="pull-left" style="margin-right:10px;">';
                    if (isset($model->creator)) {
                     //$content .= '<img alt="image" class="img-circle img-very-small" src="'.$model->creator->getUserPhotoUrl().'" title="'.$model->creator->first_name.' '.$model->creator->last_name.'">';
                    }
                    $content .='</div>';
                    $content .= Html::a($model->title, ['view', 'id' => $model->id], ['class'=>'show-service']);
                    if (isset($model->event))
                        $content.='<br/><small>'.$model->event->displayLabel.'</small>';
                    if (isset($model->creator))
                        $content.='<br/><small>'.Yii::t('app', 'Utworzył:').$model->creator->first_name.' '.$model->creator->last_name.'</small>';
                    echo $content;
    ?>
</td>
<td style="width: 110px;" data-col-seq="2">
    <?php
    if ($model->datetime) { 
                        if (($model->status==0)&&(date('Y-m-d')>$model->datetime)){ $class= "label-warning"; }else{ $class="";}
                     echo '<span class="label '.$class.'"><i class="fa fa-clock-o"></i> '.substr($model->datetime, 0, 11).'</span> ';
                    }
    ?>

</td>
<td data-col-seq="3">
    <?php $return = ""; 
                    foreach ($model->getAllUsers() as $team){ 
                    $status = $model->checkStatusForUser($team->id);
                    $return .='<a href="#" style="position:relative;">';
                    if ($status) { 
                     $return .='<span class="badge badge-primary pull-right status-bagde"><i class="fa fa-check"></i></span>';
                    } 
                    $return .='<img alt="image" class="img-circle img-very-small" src="'.$team->getUserPhotoUrl().'" title="'.$team->first_name." ".$team->last_name.'"></a>';
                 }

            echo $return;
            ?>

</td>
<td style="width: 110px;" data-col-seq="4">
    <?php
                    if ($model->status==10)
                    {
                        echo '<span class="label label-primary"><i class="fa fa-check-circle"></i> '.Yii::t('app', 'Wykonane').'</span> ';
                    }else{
                    if (($model->status==0)&&(date('Y-m-d')>$model->datetime)&&($model->datetime))
                    {
                        echo '<span class="label label-warning"><i class="fa fa-exclamation-circle"></i> '.Yii::t('app', 'Po terminie').'</span> ';
                    }else{
                        echo '<span class="label">'.Yii::t('app', 'Niewykonane').'</span> ';
                    }}
    ?>
</td>
<td style="width: 70px;" data-col-seq="5">
<?php
                            if (($model->status==10)||($model->checkStatusForUser(Yii::$app->user->id))){
                                   echo Html::a('<i class="fa fa-check"></i> ', ['/task/done', 'id'=>$model->id], ['class'=>'btn btn-primary btn-circle done-button']);
                                 }else { 
                                    echo Html::a('<i class="fa fa-check"></i> ', ['/task/done', 'id'=>$model->id], ['class'=>'btn btn-primary btn-circle btn-outline done-button']);
                                 } 
                                 ?>
</td></tr>