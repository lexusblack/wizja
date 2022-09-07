<?php

use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $model common\models\Rent */

$this->title =  Yii::t('app', 'Edycja').': ' . $model->name;
$this->params['breadcrumbs'][] = ['label' => Yii::t('app', 'Wypożyczenie'), 'url' => ['index']];
$this->params['breadcrumbs'][] = ['label' => $model->name, 'url' => ['view', 'id' => $model->id]];
$this->params['breadcrumbs'][] =  Yii::t('app', 'Edycja');
?>
<div class="rent-update">
<?php
if (count($dateError))
{
	?>
	<div class="alert alert-danger">
    <?=Yii::t('app', 'Zmiana terminu wypożyczenia nie udała się. Jeden lub więcej sprzętów przypisanych do wypożyczenia jest niedostępny. Zmień rezerwacje w zakładce sprzęt i spróbuj ponownie')?>
    </br>
    <?php foreach ($dateError as $de)
    {
    	echo $de['gear']->name." ( brakujących ".$de['missing']."szt.)<br/>";
    	}?>
    	<?=Html::a(Yii::t('app', 'Powrót do zakładki sprzęt'), ['/rent/view', 'id'=>$model->id])?>
    	</div>
	<?php
} ?>
    <?= $this->render('_formc', [
        'model' => $model,
        'schema_change_possible' => $schema_change_possible
    ]) ?>

</div>
