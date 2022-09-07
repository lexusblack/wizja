<?php

use yii\helpers\Html;
use yii\helpers\ArrayHelper;
use yii\widgets\ActiveForm;
use kartik\widgets\DatePicker;
use kartik\widgets\DateTimePicker;

/* @var $this yii\web\View */
/* @var $model common\models\TaskSchema */
/* @var $form yii\widgets\ActiveForm */

?>

<div class="task-schema-form">

<h3><?= Yii::t('app', 'Lista zakupów dla eventu')?></h3>
<table class="table">
    <tr>
        <th>#</th>
        <th><?= Yii::t('app', 'Nazwa')?></th>
        <th><?= Yii::t('app', 'Ilość')?></th>
        <th><?= Yii::t('app', 'Cena')?></th>
        <th><?= Yii::t('app', 'Firma')?></th>
        <th><?= Yii::t('app', 'Lista')?></th>
        <th><?= Yii::t('app', 'Status')?></th>
    </tr>
    <?php $i = 0; foreach ($items as $item)
    { $i++;
        ?>
    <tr>
        <td><?=$i?>.</td>
        <td><?= $item->name?></td>
        <td><?= $item->quantity?></td>
        <td><?= $item->price?></td>
        <td><?= $item->company_name?></td>
        <td><?= Html::a($item->purchaseList->name, ['/purchase-list/view', 'id'=>$item->purchase_list_id])?></td>
        <td><?= \common\models\PurchaseListItem::getStatusList()[$item->status];?></td>
    </tr>
    <?php } ?>
</table>
</div>
<?php

