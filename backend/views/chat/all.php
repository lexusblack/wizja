<?php

/* @var $this yii\web\View */
/* @var $searchModel common\models\ChatSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

use yii\helpers\Html;
use kartik\export\ExportMenu;
use kartik\grid\GridView;

$this->title = Yii::t('app', 'Chat');
$this->params['breadcrumbs'][] = $this->title;
$search = "$('.search-button').click(function(){
    $('.search-form').toggle(1000);
    return false;
});";
$this->registerJs($search);
?>
<div class="chat-index">

    <h1><?= Html::encode($this->title) ?></h1>
    <?php if (Yii::$app->user->can('chatCreate')) { ?>
    <p>
        <?= Html::a(Yii::t('app', 'Nowa rozmowa'), ['create'], ['class' => 'btn btn-success']) ?>
    </p>
    <?php } ?>
    <div class="row">
        <div class="col-lg-12">

                <div class="ibox chat-view">

                    <div class="ibox-title" id="chat-title">
                    </div>


                    <div class="ibox-content">

                        <div class="row">

                            <div class="col-md-9">
                                <div class="chat-discussion" id="current-chat">


                                </div>
                            <div class="col-lg-12" style="background:#eee; padding-top:10px;">
                                <div class="chat-message-form">

                                    <div class="form-group">
                                    <input type="hidden" id="chatId"></input>
                                    
                                        <textarea class="form-control message-input" name="message" id="chatMessage" placeholder="<?= Yii::t('app', 'Wiadomość') ?>"></textarea>
                                    <input type="hidden" id="chatHist"></input>
                                    </div>

                                </div>
                            </div>
                            </div>
                            <div class="col-md-3">
                                <div class="chat-users">
                                <div class="users-list">
                                <?php foreach ($model as $mm)
                                { 
                                $m = $mm->getLastMessage(Yii::$app->user->identity->id);
                                if (($m->user_to==Yii::$app->user->identity->id)&&($m->read<1))
                                {
                                    $class= " not-read ".$m->user_to;
                                }else{
                                    $class = "";
                                }
                                    ?>
                                        <div class="chat-user">
                                            <?php echo $m->notMe(Yii::$app->user->identity->id)->getUserPhoto("chat-avatar"); ?>

                                            <div class="chat-user-name">
                                                <a href="#" onclick="loadChat(<?=$mm->id?>,'<?=$mm->name?>', <?=$mm->isHistory()?>); return false;"><?=$mm->name?></a>
                                                                                            <?php if (Yii::$app->user->can('chatUpdate')) { ?>
                                            <a href="/admin/chat/update?id=<?=$mm->id?>" class="pull-right" style="margin-right:5px;">
                                                    <i class="fa fa-pencil"></i>
                                            </a>
                                            <?php } ?>
                                            </div>

                                        </div>
                                <?php } ?>
                                    </div>

                                </div>
                            </div>

                        </div>


                    </div>

                </div>
        </div>

    </div>

</div>
<script type="text/javascript">
    function loadChat(id, name, history)
    {
        $("#current-chat").empty();
        $("#current-chat").load("/admin/chat/loadchat?id="+id, function() {
                    var elem = document.getElementById('current-chat');
                    elem.scrollTop = elem.scrollHeight;
                    setInterval(function(){ loadChatMessage(); }, 3000);
                });
        $("#chat-title").empty();
        $("#chat-title").append(name);
        <?php if (Yii::$app->user->can('chatUpdate')) { ?>
        $("#chat-title").append('<a href="/admin/chat/update?id='+id+'" style="margin-left:10px;"><i class="fa fa-pencil"></i></a>');
        <?php } ?>
        $('#chatId').val(id);
        $('#chatHist').val(history);
    }


    function loadChatMessage()
    {
        id=$("#chatId").val();
            var content;
            $.get("/admin/chat/ajaxchatload?id="+id, function(data){
                content= data;
                $('#current-chat').append(content);
                if (content!="")
                {
                        var elem = document.getElementById('current-chat');
                        elem.scrollTop = elem.scrollHeight;
                        audioElement.play();
                }
            });
    }

    function sendChatMessage() {
            data = $("#chatMessage").val();
            id=$("#chatId").val();
            fhistory = $("#chatHist");
            if (fhistory.val()!="1")
            {
                if ((data!="")&&(data.length>1))
                {
                    $.ajax({
                        data: { text:data},
                        type: 'POST',
                        url: "/admin/chat/send?id="+id
                    });
                    $("#chatMessage").val("");
                    content = '<div class="chat-message right"><?php echo Yii::$app->user->identity->getUserPhoto("message-avatar"); ?><div class="message"><a class="message-author" href="#"><?=Yii::$app->user->identity->displayLabel ?></a><span class="message-date"></span><span class="message-content">'+data+'</span></div></div>';
                    $('#current-chat').append(content);
                    var elem = document.getElementById('current-chat');
                    elem.scrollTop = elem.scrollHeight;
                    //$('#messageContent-'+id).slimScroll();
                }                
            }else{
                alert("<?=Yii::t('app', 'Zostałeś odpięty od tego chatu, możesz tylko przeglądać wiadomości archiwalne')?>");
                $("#chatMessage").val("");
            }

        }
</script>
<?php if (count($model)>0){ ?>
<?=$this->registerJs('
        $( document ).ready(function() {
            loadChat('.$model[0]->id.', "'.$model[0]->name.'", '.$model[0]->isHistory().');
        $("#chatMessage").on("keyup", function (e) {
                if (e.keyCode == 13) {
                        // Do something
                        sendChatMessage();
                    }
            });
        });
');?>
<?php }?>