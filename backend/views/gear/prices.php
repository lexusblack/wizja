<?php
/* @var $this \yii\web\View */
/* @var $warehouse \common\models\form\WarehouseSearch; */

use yii\bootstrap\Html;
use yii\helpers\Url;
use kartik\widgets\ActiveForm;

$this->title = Yii::t('app', 'Ceny sprzętu');
$user = Yii::$app->user;
$name = "";
?>
<div class="menu-pils">
<div class="clearfix">
	<ul id="pg_navi" class="nav-pills newsystem-bg nav">
	<li><a><?=Yii::t('app', 'Wybierz grupę cenową: ')?></a></li>
	<?php foreach ($priceGroups as $pg){ 
		$url = Url::current(['c'=>Yii::$app->request->get('c', null), 's'=>Yii::$app->request->get('s', null), 'priceGroup'=>$pg->id]);
				if ($pg->id == $priceGroup) { $class="active"; $name = $pg->name;}else{ $class="";}?>
			<li class="<?=$class?>"><a class="auto-save category-menu-link" href="<?=$url?>"><?=$pg->name?></a></li>
	<?php } ?>
	</ul>
</div>
<div class="row">
<div class="col-lg-12">
<h1><?=$name?></h1>
<p>
        
        
        <?= Html::a(Yii::t('app', 'Dodaj grupę cenową'), ['/price-group/create'], ['class' => 'btn btn-success']) ?>
        <?= Html::a(Yii::t('app', 'Edytuj grupę cenową'), ['/price-group/update', 'id'=>$priceGroup], ['class' => 'btn btn-success']) ?>
        <?= Html::a(Yii::t('app', 'Dodaj stawkę'), ['/gears-price/create'], ['class' => 'btn btn-success']) ?>
        <?= Html::a(Yii::t('app', 'Zarządzaj stawkami'), ['/gears-price/index'], ['class' => 'btn btn-success']) ?>
        <?= Html::a(Yii::t('app', 'Eksport'), ['/gears-price/export'], ['class' => 'btn btn-success']) ?>
</p>
</div>
</div>
</div>
<div class="menu-pils">
<?= $this->render('/warehouse/_categoryMenu'); ?>
</div>
<div class="row">
<div class="col-lg-12">
<div class="ibox">
<div class="ibox-content no-padding">
<?php $total_inputs = count($groups)*count($model->gears)*2;
if ($total_inputs>1000){
 ?>

<div class="alert alert-danger">
<?=Yii::t('app', 'Uwaga, w przypadku kategorii z dużą liczbą pozycji, prosimy dodawać ceny w podakategoriach. W przciwnym wypadku mogą występować problemy z niezapisanymi cenami')?>
</div>
<?php } ?>

<?php $form = ActiveForm::begin(['id'=>'price-form', 'options'=>[ 'class'=>'form-inline']]); ?>
<div class="form-group" data-spy="affix" style="z-index: 1000; right: 10px; top: 250px;">
            <?= Html::submitButton(Yii::t('app', 'Zapisz'), ['class' =>  'btn btn-success']) ?><br/>
        </div>
	<table class="table">
		<tr><th><?=Yii::t('app', ' Nazwa sprzętu')?></th>
		<?php 
		$group_order = [];
		$group_count = count($groups)+1;
		$i = 0;
		foreach ($groups as $group){
			$group_order[$i] = $group->id;
		?>
		<th><?=$group->name." (".$group->currency.")"?> <?=Html::a('<i class="fa fa-pencil"></i>', ['/gears-price/update', 'id'=>$group->id]) ?><input type="checkbox" name="default-group" data-group_id="<?=$group->id?>" class="price-default-group"> </th>
		<?php }?>
		</tr>
		<?php foreach ($model->gears as $gear)
		{ ?>
		<tr><td><?=Html::a($gear->name, ['/gear/view', 'id'=>$gear->id, '#'=>'tab_finance'])?></td>
		<?php 
		$i = 0;
		foreach ($groups as $group){
			$baseIndex = 'prices['.$gear->id.']['.$group->id.']';
			$baseIndex2 = 'defaults['.$gear->id.']['.$group->id.']';
		?>	
		<td><?php echo $form->field($model, $baseIndex.'[price]')->textInput(['class'=>'price-input'])->label(false); ?><?php echo $form->field($model, $baseIndex2.'[check]')->checkbox(['class'=>'checkbox-default-price', 'data-gear_id'=>$gear->id, 'data-group_id'=>$group->id], false)->label(false); ?></td>
		
		<?php } ?>
		</tr>
		<?php if ($gear->getGearsPricesByGroup($priceGroup)){ ?>
		<tr><td colspan="<?=$group_count ?>" style="padding-left:70px"><table><tr><td style="padding-right:10px"><strong><?=Yii::t('app', 'Specjalne stawki: ')?></strong></td>
		<?php foreach($gear->getGearsPricesByGroup($priceGroup) as $p){ ?>
		<td><?php $baseIndex = 'prices['.$gear->id.']['.$p->id.']'; $baseIndex2 = 'defaults['.$gear->id.']['.$p->id.']'; echo $form->field($model, $baseIndex.'[price]')->textInput(['class'=>'price-input'])->label($p->name); ?><?php echo $form->field($model, $baseIndex2.'[check]')->checkbox(['class'=>'checkbox-default-price', 'data-gear_id'=>$gear->id, 'data-group_id'=>$p->id], false)->label(false); ?></td>
		<?php } ?>
		</tr></table></td></tr>
		<?php } ?>
		<?php } ?>
	</table>
<?php ActiveForm::end(); ?>
</div>
</div>
</div>
</div>

<?php $this->registerCss('
	.help-block{ display:none;}
	.form-group{margin-bottom:0px}
	.form-inline .form-control.price-input{ width:100px; margin-right:20px}
	.price-default-group{margin-left:10px;;}');

$this->registerJs('
	$(".checkbox-default-price").click(function(){
		var checked = $(this).is(":checked");
		var gear_id = $(this).data("gear_id");
		if (checked)
		{
			$("*[data-gear_id=\'"+gear_id+"\']").prop("checked", false);
			$(this).prop("checked", true);
		}
	});
	$(".price-default-group").click(function(){
		var checked = $(this).is(":checked");
		var gear_id = $(this).data("group_id");
		if (checked)
		{
			$(".price-default-group").prop("checked", false);
			$(this).prop("checked", true);
			$("*[data-group_id=\'"+gear_id+"\']").each(function(){
				if ($(this).is(":checked"))
				{

				}else{
					$(this).trigger("click");
				}
				
			});
		}else{
				$("*[data-group_id=\'"+gear_id+"\']").prop("checked", false);
		}
	});

	');


