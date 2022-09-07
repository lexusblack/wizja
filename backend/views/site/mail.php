<?php

use yii\helpers\Html;
$types = [1=>Yii::t('app', 'Błąd'), 2=>Yii::t('app','Pytanie'), 3=>Yii::t('app','Nowa funkcjonalność')];
$pr = [1=>Yii::t('app', 'Niski'), 2=>Yii::t('app','Wysoki'), 3=>Yii::t('app','Uniemożliwiający pracę')];
?>
	<h2><?=$model->subject?></h2>
    <table>
        <tr>
            <td><?=Yii::t('app', 'Typ zgłoszenia').": ".$types[$model->type]?></td>
        </tr>
        <tr>
            <td><?=Yii::t('app', 'Priorytet').": ".$pr[$model->priority]?></td>
        </tr>
        <tr>
            <td><?=$model->text?></td>
        </tr>
        <tr>
            <td><a href="<?=$model->link?>"><?=$model->link?></a></td>
        </tr>
    </table>
