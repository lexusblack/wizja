<?php
use yii\bootstrap\Html;
use common\components\grid\GridView;
use yii\helpers\Url;
/* @var $model \common\models\Customer; */


?>
<div class="panel-body">
<div class="row">
    <div class="col-md-12">
        <?php echo Html::a(Yii::t('app', 'Dodaj'), ['/customer-discount/default/create-discount',  'customer_id'=>$model->id], ['class'=>'btn btn-success']); ?>
    </div>
</div>
<h3><?php echo Yii::t('app', 'Rabaty'); ?></h3>
<table class="table">
    <thead>
    <tr>
        <th><?=Yii::t('app', 'Kategoria')?></th>
        <th><?=Yii::t('app', 'Wartość')?></th>
        <th style="width:100px"></th>
    </tr>
    </thead>
    <tbody>
    <?php         
    foreach ($model->customerDiscounts as $m)
        {
            foreach ($m->customerDiscountCategories as $discountCategory)
            {
                ?>
                <tr><td><?=$discountCategory->category->name?></td><td><?=$m->discount?>%</td>
                <td>
                <?=Html::a('<span class="glyphicon glyphicon-pencil"></span>',['/customer-discount/default/update', 'id'=>$m->id, 'customer_id'=>$model->id])?>
                <?=Html::a('<span class="glyphicon glyphicon-trash"></span>',['/customer-discount/default/delete', 'id'=>$m->id, 'customer_id'=>$model->id], [
            'data' => [
                'confirm' => Yii::t('app', 'Na pewno chcesz usunąć?'),
                'method' => 'post',
            ]])?>

                </td>
                </tr>
                <?php
            }
        }   
    ?> 
    </tbody>
</table>
</div>