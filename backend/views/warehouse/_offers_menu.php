<?php
use yii\bootstrap\Html;

?>

<div class="clearfix">
<br>
<?php
	if(isset($_GET['event_id']) && isset($_GET['id'])){
		foreach ($offers as $key => $offer) {
			$class="btn-default";
			if($_GET['id'] == $offer->id){
				$class="btn-primary";
			}
			
			echo Html::a($offer->name, ['/warehouse/assign-gear-item-to-offer', 'event_id' => $_GET['event_id'], 'id'=>$offer->id], ['class'=>'btn '.$class]).' ';
		}
	}
?>
</div>
<br />