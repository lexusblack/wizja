<?php

use yii\helpers\Html;
?>
	<h2><?=Yii::t('app', 'New Event Management - hasło tymczasowe')?></h2>
    <table>
        <tr>
            <td><?=Yii::t('app', 'Witaj')." ".$model->first_name." ".$model->last_name?></td>
        </tr>
        <tr>
            <td><?=Yii::t('app', 'Twoje nowe tymczasowe hasło to').": ".$token?></td>
        </tr>
        <tr>
            <td><?=Yii::t('app', 'Zmienisz je przy kolejnym logowaniu.')?></td>
        </tr>
    </table>
