<?php

use yii\helpers\Html;
use yii\helpers\Url;
?>
	<h2><?=$subject?></h2>
    <table>
        <tr>
            <td><?=$user->displayLabel." ".Yii::t('app', 'zmienił status zadania na wykonane')?></td>
        </tr>
        <tr>
            <td><?php
                if ($model->event_id){
                    echo "<a href='".Url::to(['event/view', 'id'=>$model->event_id], 'https')."'>".Yii::t('app', 'Zobacz zadanie')."</a>";
                }else{
                    echo "<a href='".Url::to(['task/ordered'], 'https')."'>".Yii::t('app', 'Zobacz zadanie')."</a>";
                }
                ?>
             </td>
        </tr>
        <tr>
            <td><?php if ($model->only_one!=1){ 
                            $users = 0;
                            $done = 0;
                            foreach ($model->getAllUsers() as $team){ 
                                $status = $model->checkStatusForUser($team->id);
                                if ($status){
                                    $done++;
                                }
                                $users++;
                            }
                            if ($users)
                                $status = intval($done/$users*100);
                            else
                                $status = 0;

                            echo Yii::t('app', 'Aktualny status zadania: ').$status."% (".$done."/".$users.")";} ?></td>
        </tr>
    </table>
