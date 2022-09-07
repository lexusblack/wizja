<?php

use yii\helpers\Html;
use yii\helpers\Url;
?>
	<h2><?=$subject?></h2>
    <table>
        <tr>
            <td><?=Yii::t('app', 'Witaj')." ".$user->displayLabel.", "?></td>
        </tr>
        <tr>
            <td><?=$model->text?></td>
        </tr>
        <tr>
            <td><?=Yii::t('app', 'Termin wykonania').": ".substr($model->task->datetime, 0, 11)?></td>
        </tr>
        <tr>
            <td><?php
                if ($model->task->event_id){
                    echo "<a href='".Url::to(['event/view', 'id'=>$model->task->event_id], 'https')."'>".Yii::t('app', 'Zobacz zadanie')."</a>";
                }else{
                    echo "<a href='".Url::to(['task/ordered'], 'https')."'>".Yii::t('app', 'Zobacz zadanie')."</a>";
                }
                ?>
             </td>
        </tr>
    </table>
