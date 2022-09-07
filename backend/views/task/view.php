<?php

use yii\helpers\Html;
use yii\helpers\Url;
use yii\widgets\DetailView;

/* @var $this yii\web\View */
/* @var $model common\models\Task */

$this->title = $model->title;
$this->params['breadcrumbs'][] = ['label' => Yii::t('app', 'Zadania'), 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="task-view">

    <?php if ($model->canDelete()) { ?>
    <p>
        <?= Html::a('<i class="fa fa-pencil"></i> ' . Yii::t('app', 'Edycja'), ['update', 'id' => $model->id], ['class' => 'btn btn-primary']) ?>
        <?= Html::a('<i class="fa fa-trash"></i> ' . Yii::t('app', 'Usuń'), ['delete', 'id' => $model->id], [
            'class' => 'btn btn-danger',
            'data' => [
                'confirm' => Yii::t('app', 'Na pewno chcesz usunąć?'),
                'method' => 'post',
            ],
        ]) ?>
    </p>
    <?php } ?>

<div class="row">
    <div class="col-md-6">
                    <div class="ibox">
                        <div class="ibox-title" <?php if ($model->color) echo "style='background-color:".$model->color."; color:white;'"?>>
                            <h5><?php echo $model->title ?></h5>
                        </div>
                        <div class="ibox-content">
                            <span class="pull-right">
                                Zmień status na:
                                <?php if ($model->status!=10)
                                {
                                 echo Html::a('<i class="fa fa-check"></i> ' . Yii::t('app', 'Wykonane'), ['update-status', 'id' => $model->id, 'status'=>10], ['class' => 'btn btn-white btn-sm done-button']);
                                }
                                if (($model->status==10)||($model->status===null)){
                                echo Html::a('<i class="fa fa-warning"></i> ' . Yii::t('app', 'Niewykonane'), ['update-status', 'id' => $model->id, 'status'=>0], [
                                    'class' => 'btn btn-white btn-sm done-button',
                                ]);
                                } ?> 
                            </span>                              
                                <p><strong><?= Yii::t('app', 'Tytuł') ?>: </strong><?php echo $model->title ?></p> 
                                <p><strong><?= Yii::t('app', 'Autor') ?>: </strong><?php echo $model->creator->displayLabel ?></p>
                                <p><strong><?= Yii::t('app', 'Termin') ?>: </strong><?php echo $model->end_time ?></p>
                                <p><strong><?= Yii::t('app', 'Aktualny status') ?>: </strong><?php echo $model->statusLabel ?></p>
                                <p><strong><?= Yii::t('app', 'Przypisany do') ?>: </strong><?php 
                                $content = "";
                                foreach ($model->users as $user)
                                {
                                    if ($content!="")
                                        $content .=", ";
                                    $content.=$user->displayLabel;
                                }
                                echo $content;
                                ?></p>
                                <?php if ($model->event_id){ ?>
                                <p><strong><?= Yii::t('app', 'Powiązane wydarzenie') ?>: </strong>
                                <?php 
                                $content = Html::a($model->event->name.' ['.$model->event->code.']', ['/event/view', 'id' => $model->event_id]); 
                                echo $content;
                                ?></p> 
                                <?php } ?>
                                <p><strong><?= Yii::t('app', 'Treść') ?>: </strong></p>
                                <?php echo $model->content ?>
                                <p><strong><?= Yii::t('app', 'Komentarz') ?>: </strong></p>
                                <?php echo $model->comment ?>
                        </div>
                    </div>
            </div>

        </div>
</div>
<script src="https://unpkg.com/sweetalert/dist/sweetalert.min.js"></script>
<script type="text/javascript">
    function updateTaskStatus(href)
    {
                        swal({
                          text: "<?=Yii::t('app', 'Jeśli chcesz możesz dodać komentarz')?>",
                          content: {
                            element: "input",
                            attributes: {
                              placeholder: "<?=Yii::t('app', 'Twój komentarz')?>",
                              type: "text",
                            }
                        },
                          button: {
                            text: "OK",
                            closeModal: true,
                          },
                        })
                        .then(name => {
                          if (!name) name="";
                            data = {comment:name};
                                                $.ajax({
                                                    type: 'POST',
                                                    data: data,
                                                    url: href,
                                                    success: function(response) {
                                                       location.reload();
                                                    }
                                                });
                        });
    }
</script>
<?php
$this->registerJs('


$(".done-button").click(function(e){
        e.preventDefault();
        updateTaskStatus($(this).attr("href"));
    })');
?>
