<?php
/* @var $this \yii\web\View */
/* @var $warehouse \common\models\form\WarehouseSearch; */

use yii\bootstrap\Html;
use yii\helpers\Url;
use kartik\widgets\ActiveForm;

$user = Yii::$app->user;
?>

<div class="row">
<div class="col-lg-12">
<div class="ibox">
<div class="ibox-content no-padding" style="overflow-x: scroll">
<?php $form = ActiveForm::begin(['id'=>'warehouse-form', 'options'=>[ 'class'=>'form-inline']]); ?>
	<table class="table">
		<tr><th><?=Yii::t('app', ' Nazwa sprzętu')?></th>
		<?php 
		$w_array = [];
		foreach ($warehouses as $w){
			$w_array[] = $w->id;
		?>
		<th><?=$w->name?>
			<?php if (!$gear->no_items){?>
			<input type="checkbox" name="default-group" data-group_id="<?=$w->id?>" class="group-warehouse">
			<?php } ?>

		</th>
		<?php }?>
		</tr>
		<?php if ($gear->no_items){ ?>
			<tr><td><?=$gear->name?></td>
			<?php foreach ($w_array as $w){ ?>
				<td><?php 
				echo $form->field($wform, 'warehouses['.$gear->id.']['.$w.']')->textInput(['class'=>'input-warehouse', 'data-gear_id'=>$gear->id, 'data-group_id'=>$w, 'data-total'=>$gear->quantity, 'style'=>'width:50px;'], false)->label(false);
				?></td>
			<?php } ?>
			</tr>
		<?php }else{ 
			foreach ($gear->gearItems as $item)
			{
				if ($item->active){
			?>
			<tr><td><?=$gear->name." [".$item->number."]"?></td>
			<?php foreach ($w_array as $w){ ?>
				<td><?php 
				echo $form->field($wform, 'warehouses['.$item->id.']['.$w.']')->checkbox(['class'=>'checkbox-warehouse', 'data-gear_id'=>$item->id, 'data-group_id'=>$w], false)->label(false);
				?></td>
			<?php } ?>
			</tr>
		<?php } } } ?>
	</table>
	<?= Html::a(Yii::t('app', 'Zapisz'), ['manage-warehouse', 'gear_id'=>$gear->id], ['class'=>'btn btn-primary save-form'])?>
<?php ActiveForm::end(); ?>
</div>
</div>
</div>
</div>
<?php
$this->registerJs('
	$(".checkbox-warehouse").click(function(){
		var checked = $(this).is(":checked");
		var gear_id = $(this).data("gear_id");
		if (checked)
		{
			$("*[data-gear_id=\'"+gear_id+"\']").prop("checked", false);
			$(this).prop("checked", true);
		}
	});


		$(".group-warehouse").click(function(){
		var checked = $(this).is(":checked");
		var gear_id = $(this).data("group_id");
		if (checked)
		{
			$(".checkbox-warehouse").prop("checked", false);
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

if (!$gear->no_items){
	$this->registerJs('
	$(".save-form").click(function(e){
		e.preventDefault();
        $(this).attr("disabled", "disabled");
        //pobieramy id
        data = $("#warehouse-form").serialize();
        $.post($(this).attr("href"), data, function(response){
            window.location.reload();
        });

	});
		');
}else{
	$this->registerJs('
		$(".save-form").click(function(e){
		e.preventDefault();
		total = 0;
		$(".input-warehouse").each(function(){
			total +=parseInt($(this).val());
		})
		if (total > '.$gear->quantity.')
		{
			alert("Suma sprzętu w magazynach jest za duża. Łącznie powinno być: '.$gear->quantity.'")
		}else{
			        $(this).attr("disabled", "disabled");
	        //pobieramy id
	        data = $("#warehouse-form").serialize();
	        $.post($(this).attr("href"), data, function(response){
	            window.location.reload();
	        });
		}


	}); ');
}