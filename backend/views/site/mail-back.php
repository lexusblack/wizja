<?php

use yii\helpers\Html;

?>
	<h2><?=Yii::t('app', 'Zgłoszenie przyjęte')?></h2>
    <table>
        <tr>
            <td><?=Yii::t('app', 'Twoje zgłoszenie zostało przyjęte. Nadaliśmy Twojej sprawie numer: ').'ERR_'.$model->id?></td>
        </tr>
        <tr>
            <td><?=Yii::t('app', 'Pozdrawiamy Zespół New Event Management')?></td>
        </tr>
        <tr><td>
        <?=Yii::t('app', 'Treść błędu:')?></td>
        </tr>
        <tr><td><?=$model->text?></td></tr>
    </table>
