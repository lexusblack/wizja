<?php
use yii\bootstrap\Modal;
use yii\helpers\Url;

?>
<div class="footer">
    <div class="pull-right">
        
    </div>
    <div>
        <strong>Copyright</strong> <?=isset(\Yii::$app->params['copyright']) ? \Yii::$app->params['copyright'] : 'Newsystems &copy; '.date("Y")?>
    </div>
</div>
<div id="open-check-list">
<span class="badge badge-warning pull-right"><?=\common\models\Checklist::getUndone()?></span>
    <a class="open-check-list" title="<?=Yii::t('app', 'CheckLista')?>"><i class="fa fa fa-check-square-o"></i></a>
</div>
<?php
Modal::begin([
    'header' => "<h4 class='modal-title'>".Yii::t('app', 'Twoja personalna checklista')."</h4><small class='font-bold'>".Yii::t('app', 'Twórz swoje zadania, grupuj je w listy zadań, zaznaczaj wykonane.')."</small>",
    'id' => 'checklist_list_modal',
    'class'=>'inmodal inmodal',
    'size' => 'modal-lg',
]);
echo "<div class=\"modalContent\"></div>";
Modal::end();
$this->registerJs('

$(".open-check-list").on("click", function(){ 
    openCheckListModal();
})');
$spinner =  "<div class='sk-spinner sk-spinner-double-bounce'><div class='sk-double-bounce1'></div><div class='sk-double-bounce2'></div></div>";
$checkListUrl = Url::to(['/checklist/get-all']);
?>
<script type="text/javascript">
	function openCheckListModal()
	{
	    var modal = $("#checklist_list_modal");
	    modal.find(".modalContent").empty();
	    modal.find(".modalContent").append("<?=$spinner?>");
	    modal.find(".modalContent").load("<?=$checkListUrl?>");
	    modal.modal("show");
	}
</script>