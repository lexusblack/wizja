<?php

/* @var $this yii\web\View */
/* @var $searchModel common\models\ChatSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

use yii\helpers\Html; ?>
<?php

$models = [];
foreach ($model as $mm)
{
    $m['type'] = 1;
    $m['model'] = $mm;
    $m['date'] = $mm->last_message;
    $models[] = $m;
}
foreach ($crn_chats_recieving as $mm)
{
    $m['type'] = 2;
    $m['model'] = $mm;
    $m['date'] = $mm->last_message;
    $models[] = $m;
}
foreach ($crn_chats_asking as $mm)
{
    $m['type'] = 3;
    $m['model'] = $mm;
    $m['date'] = $mm->last_message;
    $models[] = $m;
}
usort($models, function ($item1, $item2) {
    return $item2['date'] <=> $item1['date'];
});
?>

                    <a class="dropdown-toggle count-info" data-toggle="dropdown" href="#">
                        <i class="fa fa-envelope"></i> 
                        <?php if ($notread>0): ?>
                         <span class="label label-warning"><?=$notread?></span>
                     <?php endif; ?>
                    </a>
                    <ul class="dropdown-menu dropdown-messages">
                    <li><?php if (Yii::$app->user->can('chatCreate')) { ?><div class="text-center link-block"><a href="/admin/chat/create"><i class="fa fa-plus"></i> <strong><?= Yii::t('app', 'Nowa') ?> </strong></a><?php } ?><a href="/admin/chat/all"><i class="fa fa-envelope"></i>  <strong><?= Yii::t('app', 'Wyświetl wszystkie') ?></strong></a></div></li>
                    <?php foreach ($models as $mmm){
                        if ($mmm['type']==1)
                        {
                            $mm = $mmm['model'];
                            $m = $mm->getLastMessage(Yii::$app->user->identity->id); 
                        if (($m->user_to==Yii::$app->user->identity->id)&&($m->read<1))
                        {
                            $class= " not-read ".$m->user_to;
                        }else{
                            $class = "";
                        }
                        ?>
                        <li class="divider"></li>
                        <li>
                            <div class="dropdown-messages-box<?=$class?>" onclick="openMessageDialog(<?=$mm->id?>, 1);">
                            <?php if ($mm->name!=Yii::t('app', 'Powiadomienia NEW')){ ?>
                            <a href="#" class="pull-left"><?php echo $m->notMe(Yii::$app->user->identity->id)->getUserPhoto("img-circle img-small"); ?></a>
                            <?php }else{ ?>
                            <a href="#" class="pull-left"><img alt="image" class="img-circle img-small" src="/img/logo-do-chat.jpg"></a>
                            <?php } ?>
                                <div class="media-body">
                                    <small class="pull-right" title="<?=$m->create_time?>"><?=$m->getTime()?></small>
                                    <strong><?=$mm->name?></strong> <br>
                                    <?php if (($m->user_from==Yii::$app->user->identity->id)&&($mm->name!=Yii::t('app', 'Powiadomienia NEW'))) { echo "Ty:";} ?>
                                    <?=substr($m->text,0,80)?>
                                </div>
                            </div>
                        </li>
                        <?php
                        } 
                        if ($mmm['type']==2)
                        {
                            if (isset($m->ccompany)){
                            $mm = $mmm['model'];
                            $m = $mm->getLastMessage(); 
                            $mine = $mm->getLastMessageMine(); 
                        if ((isset($mine))&&($mine->read<1))
                        {
                            $class= " not-read ";
                        }else{
                            $class = "";
                        }
                        ?>
                        <li class="divider"></li>
                        <li>
                            <div class="dropdown-messages-box<?=$class?>" onclick="openMessageDialog(<?=$mm->id?>, 2);">
                            <a href="#" class="pull-left"><?=$m->ccompany->getLogo("img-circle img-small")?></a>
                                <div class="media-body">
                                    <small class="pull-right" title="<?=$m->datetime?>"><?=$m->getTime()?></small>
                                    <strong><?=$mm->name?></strong> <br>
                                    <?=substr($m->text,0,80)?>
                                </div>
                            </div>
                        </li>
                        <?php
                            }

                        } 
                        if ($mmm['type']==3)
                        {
                            $mm = $mmm['model'];
                            $m = $mm->getLastMessage(); 
                            $mine = $mm->getLastMessageMine(); 
                        if ((isset($mine))&&($mine->read<1))
                        {
                            $class= " not-read ";
                        }else{
                            $class = "";
                        }
                        ?>
                        <li class="divider"></li>
                        <li>
                            <div class="dropdown-messages-box<?=$class?>" onclick="openMessageDialog(<?=$mm->id?>, 3);">
                            <a href="#" class="pull-left"><?=$m->ccompany->getLogo("img-circle img-small")?></a>
                                <div class="media-body">
                                    <small class="pull-right" title="<?=$m->datetime?>"><?=$m->getTime()?></small>
                                    <strong><?=$mm->name?></strong> <br>
                                    <?=substr($m->text,0,80)?>
                                </div>
                            </div>
                        </li>
                        <?php
                        } 
                        ?>
                    <?php } ?>
                    </ul>

<script>
<?php foreach ($model as $mm){
    $m = $mm->getLastMessage(Yii::$app->user->identity->id); 
    if (($m->user_to==Yii::$app->user->identity->id)&&($m->read<1))
    {
        echo "openMessageDialog(".$mm->id.", 1);";
        echo "audioElement.play();";
    }
    }?>
<?php foreach ($crn_chats_recieving as $mm){
    $m = $mm->getLastMessageMine(); 
    if ((isset($m))&&($m->read<1))
    {
        echo "openMessageDialog(".$mm->id.", 2);";
        echo "audioElement.play();";
    }
    }?>
<?php foreach ($crn_chats_asking as $mm){
    $m = $mm->getLastMessageMine(); 
    if ((isset($m))&&($m->read<1))
    {
        echo "openMessageDialog(".$mm->id.", 3);";
        echo "audioElement.play();";
    }
    }?>
</script>