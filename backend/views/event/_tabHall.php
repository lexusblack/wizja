<?php
use yii\bootstrap\Modal;
use common\components\grid\GridView;
use yii\bootstrap\Html;
use yii\helpers\Url;

Modal::begin([
    'header' => Yii::t('app', 'Rezerwacja'),
    'id' => 'hall_modal',
    'class'=>'inmodal inmodal',
]);
echo "<div class=\"modalContent\"></div>";
Modal::end();

?>



<div class="ibox">
                        <div class="ibox-title">
                            <h5 style="margin-right:10px;"><?php echo Yii::t('app', 'Zarezerwowane powierzchnie'); ?></h5>
                                <?=Html::a('Zarezerwuj', ['/hall-group/book', 'event_id'=>$model->id], ['class'=>'btn btn-success btn-xs'])?>
                        </div>
                        <div class="ibox-content">

                            <div class="project-list">

                                <table class="table table-hover">
                                    <tbody>
                                    <?php
$halls = \common\models\EventHallGroup::find()->where(['event_id'=>$model->id])->all();
foreach ($halls as $hall)
{ ?>
                                    <tr>
                                        <td class="project-status">
                                            <span class="label label-primary" style="background-color:<?=$hall->statut->color?>"><?=$hall->statut->name?></span>
                                        </td>
                                        <td class="project-title">
                                            <?=Html::a($hall->hallGroup->name, ['/hall-group/view', 'id'=>$hall->hallGroup->id])?>
                                            <br>
                                            <small><?=Yii::t('app', 'Utworzona:')." ".$hall->create_time?></small>
                                        </td>
                                        <td class="project-completion">
                                                <?=Yii::t('app', 'Od')." ".substr($hall->start_time, 0, 16)."<br/>".Yii::t('app', 'Do')." ".substr($hall->end_time, 0, 16)?>
                                        </td>
                                        <td class="project-people">
                                            <?=nl2br($hall->description)?>
                                        </td>
                                        <td class="project-actions">
                                            <?=Html::a('<i class="fa fa-pencil"></i> '.Yii::t('app', 'Edytuj') , ['/hall-group/book-edit', 'id'=>$hall->id], ['class'=>'btn btn-success btn-xs edit-hall-button'])?>
                                            <?=Html::a('<i class="fa fa-trash"></i> '.Yii::t('app', 'Usuń') , ['/hall-group/book-delete', 'id'=>$hall->id], ['class'=>'btn btn-danger btn-xs delete-hall-button'])?>
                                        </td>
                                    </tr>
<?php } ?>

                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>

<?php
$this->registerJs('
    $(".edit-hall-button").click(function(e){
        e.preventDefault();
        var modal = $("#hall_modal");
        modal.find(".modalContent").load($(this).attr("href"));
        modal.modal("show");

    });
    $(".delete-hall-button").click(function(e){
        e.preventDefault();
        $.post($(this).attr("href"), {}, function(response){toastr.error("'.Yii::t('app', 'Rezerwacja usunięta').'"); location.reload();});

    });
    ');
