<?php
use yii\bootstrap\Html;
use yii\helpers\Url;
use yii\bootstrap\Modal;
use kartik\widgets\ColorInput;
use backend\modules\permission\models\BasePermission;
use common\models\Task;
use yii\widgets\ActiveForm;

$user = Yii::$app->user;
?>
<div class="panel-body">
<?php foreach ($model->requests as $request){ ?>
<div class="row">
    <div class="col-md-12">
            <div class="ibox float-e-margins">
                    <div class="ibox-content">

                            <div>
                                <div class="feed-activity-list">
                                <div class="feed-element">
                                <small class="pull-right text-navy"><?=$request->create_time?></small>
                                <h3><?=$request->company->name?></h3>
                                <strong><?=Yii::t('app', 'Opis błędu:')?></strong><br/>
                                <?=$request->name."<br/>".$request->text."<br/>".$request->link?>

                                </div>
                                 <?php       
                                    foreach ($request->requestNotes as $m)
                                        {
                                        ?>
                                        <div class="feed-element">
                                        <a href="#" class="pull-left">
                                        <?php if ($m->type==1){
                                            echo $request->company->getLogo("img-circle");
                                        }else{
                                            echo Html::img('/themes/e4e/img/newemwhite.png', array('class'=>'img-circle'));
                                        }
                                        ?>
                                        </a>
                                        <div class="media-body ">
                                            <small class="pull-right text-navy"><?=$m->datetime?></small>
                                            <strong><?=$m->user_name?>:<br></strong><?=$m->text?></br>
                                        
                                            <div class="actions">
                                            <?php 
                                            if ((Yii::$app->user->id==$m->user_id)||(($m->type==2)&&(Yii::$app->params['companyID']=="admin"))){
                                            echo Html::a('<i class="fa fa-trash"></i> '.Yii::t('app', 'Usuń'), ['/request/delete-note', 'id'=>$m->id], ['class'=>'btn btn-xs btn-danger', 'data' => ['confirm' => Yii::t('app', 'Na pewno chcesz usunąć?'), 'method' => 'post']]);
                                            } ?>
                                            </div>
                                        </div>
                                        </div>
                                        <?php
                                        
                                        }   
                                    ?>  
                                    <div class="feed-element">
                                    <?php $form = ActiveForm::begin(['id'=>'requestnote-form'.$request->id, 'action'=>['request/add-note', 'id'=>$request->id]]); 
                                    $note = new \common\models\RequestNote();
                                    ?>
                                     <?=$form->field($note, 'text')->textarea([
                                                                  'row'=>3, 'class'=>'form-control'
                                                            ])->label(false)?>
                                    <div class="form-group">
                                        <?= Html::submitButton(Yii::t('app', 'Dodaj'), ['class' => 'btn btn-success submit-note']) ?>
                                    </div>

                                    <?php ActiveForm::end(); ?>   
                                    </div>                           
                                </div>
                            </div>
                    </div>
            </div>
    </div>
</div>
<?php
$this->registerJs('
$("#requestnote-form'.$request->id.'").on("beforeSubmit", function(e) {
    var form = $(this);
    var formData = form.serialize();
    $.ajax({
        url: form.attr("action"),
        type: form.attr("method"),
        data: formData,
        success: function (data) {
            location.reload();
        },
        error: function () {
            alert("Something went wrong");
        }
    });
}).on("submit", function(e){
    e.preventDefault();
});');
 } ?>
</div>

<?php
