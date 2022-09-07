<?php
use yii\bootstrap\Html;

?>
<div class="row" style="margin-top:30px">
    <div class="col-md-12">
<div class="client_info">
<h1><?=$model->name?></h1>
<table class="table table-row-border">
<tr><th><?=Yii::t('app', 'Zadanie')?></th><th><?=Yii::t('app', 'Pracownicy')?></th><th><?=Yii::t('app', 'Status')?></th></tr>
            <?php foreach ($model->tasks as $task){
                $event = $task->getEventProdukcja();
            if ($event) { ?>
            <tr style="background-color:#eee;"><td style= "padding:10px; border: 1px solid #444;"><strong><?=$task->title?></strong></td>
            <td style= "padding:10px; border: 1px solid #444;">
                <?php foreach ($event->eventUsers as $u){
                    echo $u->user->displayLabel.", ";
                    }?>
            </td>
            <td style= "padding:10px; border: 1px solid #444;"><?=$event->eventStatut->name?></td></tr>
            <?php if ($task->content!=""){ ?>
            <tr><td style= "padding:10px; border: 1px solid #444;" colspan=3><?=$task->content?></td></tr>
            <?php } ?>
            <?php 
            foreach ($task->getEventProdukcja()->tasks as $etask)
            { ?>
                <tr><td style="padding:10px 10px 10px 50px;  border: 1px solid #444;"><strong><?=$etask->title?></strong></td>

                    <td style= "padding:10px; border: 1px solid #444;">
                <?php foreach ($etask->getAllUsers() as $u){
                    echo $u->displayLabel.", ";
                    }?>
                </td>
                <td style= "padding:10px; border: 1px solid #444;"><?php   if ($etask->status==10){ echo Yii::t('app', 'Wykonane'); }else { echo Yii::t('app', 'Niewykonane'); } ?></td>

                </tr>
                <?php if ($etask->content!=""){ ?>
                <tr><td style="padding-left:50px;  border: 1px solid #444;" colspan=3><?=$etask->content?></td></tr>
                <?php } ?>
            <?php } ?>
            <?php } } ?>
</table>
</div>
</div>
</div>

<?php $this->registerCss('
    .table.table-row-border > tr > td{
        padding:5px;
        border: 1px solid #444;
    }
    ');