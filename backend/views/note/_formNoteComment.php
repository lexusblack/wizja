<?php
use kartik\grid\GridView;
use kartik\builder\TabularForm;
use yii\data\ArrayDataProvider;
use yii\helpers\Html;
use yii\widgets\Pjax;


?>

<div class="col-md-12" style="margin-bottom:20px">
<textarea class="form-control message-input" name="message" id="noteMessage<?=$id?>" placeholder="<?= Yii::t('app', 'Twój komentarz') ?>"></textarea>
           <?php echo Html::a(Yii::t('app', 'Dodaj'), '#', ['class' => 'btn btn-success btn-sm', 'id'=>'sendNote'.$id]); ?>

</div>

<script type="text/javascript">

    function sendNote() {
            data = $("#noteMessage<?=$id?>").val();
            if (data!="")
            {
                $.ajax({
                    data: { text:data},
                    type: 'POST',
                    url: "/admin/note/save-comment?id=<?=$id?>",
                    success:function(){ reloadComments(<?=$id?>);}
                });
            }
        }
</script>
<?php $this->registerJs('
    $("#sendNote'.$id.'").click(function(e){
        e.preventDefault();
        $(this).prop("disabled", true);
        sendNote();
    });
'); ?>