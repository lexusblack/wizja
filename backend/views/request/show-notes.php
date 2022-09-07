<?php
use yii\bootstrap\Html;
use common\components\grid\GridView;
use yii\helpers\Url;
use yii\widgets\ActiveForm;

/* @var $model \common\models\Customer; */


?>
<div class="panel-body">
<div class="row">
    <div class="col-md-12">
            <div class="ibox float-e-margins">
                    <div class="ibox-content">

                            <div>
                                <div class="feed-activity-list">
                                <div class="feed-element">
                                <small class="pull-right text-navy"><?=$model->create_time?></small>
                                <strong><?=Yii::t('app', 'Opis błędu:')?></strong><br/>
                                <?=$model->name."<br/>".$model->text."<br/>".$model->link?>

                                </div>
                                 <?php       
                                    foreach ($model->requestNotes as $m)
                                        {
                                        ?>
                                        <div class="feed-element">
                                        <a href="#" class="pull-left">
                                        <?php if ($m->type==1){
                                            echo $model->company->getLogo("img-circle");
                                        }else{
                                            echo Html::img('/img/logo-do-chat.jpg', array('class'=>'img-circle'));
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
                                    <?php $form = ActiveForm::begin(['id'=>'requestnote-form', 'action'=>['request/add-note', 'id'=>$model->id]]); 
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
</div>

<?php
$this->registerJs('
$("#requestnote-form").on("beforeSubmit", function(e) {
    var form = $(this);
    var formData = form.serialize();
    $.ajax({
        url: form.attr("action"),
        type: form.attr("method"),
        data: formData,
        success: function (data) {
            $("#request-notes").find(".modalContent").empty().load("/admin/request/show-notes?id='.$model->id.'");
        },
        error: function () {
            alert("Something went wrong");
        }
    });
}).on("submit", function(e){
    e.preventDefault();
});');
