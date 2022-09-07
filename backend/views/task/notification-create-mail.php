<?php

use yii\helpers\Html;
use yii\helpers\Url;
?>
	<h2><?=$subject?></h2>
    <table>
        <tr>
            <td><?=Yii::t('app', 'Witaj ').$user->displayLabel.", "?></td>
        </tr>
        <tr>
            <td><?=$model->creator->displayLabel." ".Yii::t('app', 'dodał/-a dla Ciebie zadanie.')?></td>
        </tr>
        <tr>
            <td><strong><?=$model->title?></strong></td>
        </tr>
        <tr>
            <td><?=$model->content?></td>
        </tr>
        <tr>
            <td><?=Yii::t('app', 'Termin:').$model->datetime?></td>
        </tr>
        <tr>
            <td><?php
                if ($model->event_id){
                    echo "<a href='".Url::to(['event/view', 'id'=>$model->event_id], 'https')."'>".Yii::t('app', 'Zobacz zadanie')."</a>";
                }else{
                    if ($model->rent_id)
                    {
                        echo "<a href='".Url::to(['rent/view', 'id'=>$model->rent_id], 'https')."'>".Yii::t('app', 'Zobacz zadanie')."</a>";
                    }else{
                        echo "<a href='".Url::to(['task/index'], 'https')."'>".Yii::t('app', 'Zobacz zadanie')."</a>";
                    }
                    
                }
                ?>
             </td>
        </tr>
    </table>
