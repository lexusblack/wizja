<?php
/* @var $this \yii\web\View */
/* @var $content string */
use common\assets\AppAsset;
use backend\assets\MainPanelAsset;
use yii\helpers\Html;
use yii\bootstrap\Nav;
use yii\bootstrap\NavBar;
use yii\widgets\Breadcrumbs;
use yii\bootstrap\Alert;
use \kartik\datecontrol\DateControl;
AppAsset::register($this);
MainPanelAsset::register($this);
$directoryAsset = Yii::$app->assetManager->getPublishedUrl('@vendor/jcabanillas/yii2-inspinia/assets');
?>
<?php $this->beginPage() ?>
<!DOCTYPE html>
<html lang="<?= Yii::$app->language ?>">
    <head>
    <script src="https://unpkg.com/popper.js/dist/umd/popper.min.js"></script>
    <script src="https://unpkg.com/tooltip.js/dist/umd/tooltip.min.js"></script>
        <meta charset="<?= Yii::$app->charset ?>">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <link rel="icon" type="image/png" href="/favicon.png" />
        <?= Html::csrfMetaTags() ?>
        <title><?= Html::encode($this->title) ?></title>
        <?php $this->head() ?>
    </head>

    <body><?php $this->beginBody() ?>

        <div id="wrapper" class="">

            <?= $this->render('sidebar', ['directoryAsset' => $directoryAsset]) ?>

            <div id="page-wrapper" class="gray-bg">
                <div class="row border-bottom">
                    <?= $this->render('header', ['directoryAsset' => $directoryAsset]) ?>
                </div>
                <?php if ($this->title=="Kokpit") { ?>
                    <div class="row wrapper border-bottom newsystem-bg page-heading" style="padding:0;">
                <?php }else{ ?>
                    <div class="row wrapper border-bottom newsystem-bg page-heading">
                <?php } ?>
                
                    <?php if (isset($this->blocks['content-header'])) { ?>
                        <?= $this->blocks['content-header'] ?>
                    <?php } else { ?>
                        <?php if ($this->title!="") {?>
                            <?php if ($this->title=="Kokpit") { ?>
                                    <div  class="col-md-9" style="padding:0;">
                                        <div class="ibox float-e-margins" style="margin:0;">
                                            <div>
                                                        <div class="ibox-content no-padding border-left-right">
                                                            <?php  //echo \common\widgets\WeatherWidget::widget(); ?>
                                                        </div>
                                            </div>
                                        </div>
                                    </div>
                                    <div  class="col-md-3" style="padding:0;">
                                        <div class="ibox float-e-margins" style="margin:0;">
                                        <div class="ibox-content no-padding border-left-right">
                                                <?php //echo \common\widgets\CurrencyWidget::widget(); ?>
                                                </div>
                                            </div>
                                    </div>
                            <?php }else{ ?>
                        <div class="col-sm-<?= isset($this->blocks['content-header-actions']) ? 6 : 12 ?>">

                            <?=
                            Breadcrumbs::widget([
                                'links' => isset($this->params['breadcrumbs']) ? $this->params['breadcrumbs'] : [],
                                'activeItemTemplate' => "<li class=\"active\"><strong>{link}</strong></li>\n"
                            ])
                            ?>
                        </div>
                        <?php } ?>
                        <?php } ?>
    <?php if (isset($this->blocks['content-header-actions'])): ?>
                            <div class="col-sm-6">
                                <div class="title-action">
        <?= $this->blocks['content-header-actions'] ?>
                                </div>
                            </div>
                        <?php endif ?>
<?php } ?>

                </div>

                <div class="wrapper wrapper-content">

<?//=Alert::widget() ?>

                    <div class="row">
                        <div class="col-lg-12">
<?= $content ?>
                        </div>
                    </div>
                </div>
<?= $this->render('footer', ['directoryAsset' => $directoryAsset]) ?>
            </div>
        </div>
<?php $this->endBody() ?>
    <script>
        var intervals = [];
        var intervals2 = [];
        var audioElement;
        $( document ).ready(function() {
            $(".show-phone-modal").click(function(){
                openPhoneModal($(this).data("type"));
            });

            $("#message-top").load("/admin/chat/load/", function() {
                });
            setInterval( function() { $("#message-top").load("/admin/chat/load/", function() {
                });
              }, 15000 );
            $("#notification-top").load("/admin/user/load-notification/", function() {
                });
            $("#notification-top").click(function(){
                $.get("/admin/user/read-notifications", function(data){
                });
                $("#notification-label").hide();
            })
        audioElement = document.createElement('audio');
        audioElement.setAttribute('src', '/audio/chime.mp3');
        $.get();
        audioElement.addEventListener("load", function() {
        audioElement.play();
        }, true);


        });
        function openMessageDialog(id, type) {
            
            if (type==1)
            {
                $('#page-wrapper').append("<div class='small-chat-box fadeInRight animated' id='messager-"+id+"''></div>");
                $('#messager-'+id).load("/admin/chat/loaddialog?id="+id, function() {
                    $(this).addClass('active');
                    var elem = document.getElementById('messageContent-'+id);
                    elem.scrollTop = elem.scrollHeight;
                    });
            }

            if (type==2)
            {
                $('#page-wrapper').append("<div class='small-chat-box fadeInRight animated' id='crn-messager-"+id+"''></div>");
                $('#crn-messager-'+id).load("/admin/chat/loadcrndialog?id="+id, function() {
                    $(this).addClass('active');
                    var elem = document.getElementById('crnmessageContent-'+id);
                    elem.scrollTop = elem.scrollHeight;
                    });
                }

            if (type==3)
            {
                $('#page-wrapper').append("<div class='small-chat-box fadeInRight animated' id='crn-messager-"+id+"''></div>");
                $('#crn-messager-'+id).load("/admin/chat/loadcrndialog?id="+id, function() {
                    $(this).addClass('active');
                    var elem = document.getElementById('crnmessageContent-'+id);
                    elem.scrollTop = elem.scrollHeight;
                    });
                }
        }

        function closeDialog(id, type){
            if (type==1)
            {
                window.clearInterval(intervals[id]);
                $('#messager-'+id).remove();
            }
            if (type==2)
            {
                window.clearInterval(intervals2[id]);
                $('#crn-messager-'+id).remove();
            }            
        }
        function sendMessage(id, type) {
            if (type==1)
            {
                 data = $("#messageInput-"+id).val();
                if (data!="")
                {
                    $.ajax({
                        data: { text:data},
                        type: 'POST',
                        url: "/admin/chat/send?id="+id
                    });
                    $("#messageInput-"+id).val("");
                    content = '<div class="right"><div class="author-name"><?=Yii::$app->user->identity->displayLabel ?> <small class="chat-date"></small></div><div class="chat-message">'+data+'</div></div>';
                    $('#messageContent-'+id).append(content);
                    var elem = document.getElementById('messageContent-'+id);
                    elem.scrollTop = elem.scrollHeight;
                    //$('#messageContent-'+id).slimScroll();
                }               
            }
            if (type==2)
            {
                 data = $("#crnmessageInput-"+id).val();
                if (data!="")
                {
                    $.ajax({
                        data: { text:data},
                        type: 'POST',
                        url: "/admin/chat/sendcrn?id="+id
                    });
                    $("#crnmessageInput-"+id).val("");
                    content = '<div class="right"><div class="author-name"><?=Yii::$app->user->identity->displayLabel ?> <small class="chat-date"></small></div><div class="chat-message">'+data+'</div></div>';
                    $('#crnmessageContent-'+id).append(content);
                    var elem = document.getElementById('crnmessageContent-'+id);
                    elem.scrollTop = elem.scrollHeight;
                    //$('#messageContent-'+id).slimScroll();
                }               
            }
        }

        function createEventChat(id){
            $.get("/admin/chat/createevent?id="+id, function(data){
                openMessageDialog(data, 1);
            });         
        }

        function createUserChat(id){
            $.get("/admin/chat/createuser?id="+id, function(data){
                openMessageDialog(data);
            });         
        }

        function ajaxLoad(id, type) {
            var content;
            if (type==1)
            {
                $.get("/admin/chat/ajaxload?id="+id, function(data){
                    content= data;
                    $('#messageContent-'+id).append(content);
                    if (content!="")
                    {
                            var elem = document.getElementById('messageContent-'+id);
                            elem.scrollTop = elem.scrollHeight;
                            audioElement.play();
                    }
                });
            }
            if (type==2)
            {
                $.get("/admin/chat/ajaxloadcrn?id="+id, function(data){
                    content= data;
                    $('#crnmessageContent-'+id).append(content);
                    if (content!="")
                    {
                            var elem = document.getElementById('crnmessageContent-'+id);
                            elem.scrollTop = elem.scrollHeight;
                            audioElement.play();
                    }
                });
            }
        }

        function numberWithCommas(x) {
            return x.toString().replace(/\B(?=(\d{3})+(?!\d))/g, " ");
        }

        function createOrder()
        {
           var keys = $("#orderGear").yiiGridView("getSelectedRows");
           console.log(keys);
           var json = JSON.stringify(keys)
           location.href = "/admin/order/add?ids="+json;
        }

        function openPhoneModal(type)
        {
            $("#phone-modal").modal("show").find(".modalContent").html('<div class="phone-form"><form id="PhoneForm" action="/admin/site/send-app" method="post"><div class="row"><input name="type" type="hidden" value="'+type+'"/><div class="col-lg-12"><div class="form-group"><label class="control-label" for="task-title"><?=Yii::t('app', 'Numer telefonu')?></label><input type="text" class="form-control" name="phone" maxlength="255" placeholder="<?=Yii::t('app', 'Numer telefonu')?>" autocomplete="off" aria-required="true" aria-invalid="true"></div></div></div><div class="form-group"> <button type="submit" class="btn btn-success">Wyślij</button>    </div></form></div>');
            $('#PhoneForm').on('beforeSubmit', function(e) {

            }).on('submit', function(e){

                e.preventDefault();
                var form = $(this);
                var formData = form.serialize();
                $.ajax({
                    url: form.attr('action'),
                    type: form.attr('method'),
                    data: formData,
                    success: function (data) {
                        $('#phone-modal').find('.modalContent').empty();
                        $('#phone-modal').find('.modal-dialog').addClass('modal-lg');
                        $('#phone-modal').find('.modalContent').html('<iframe width="800" height="450" src="https://www.youtube.com/embed/AQ5r5U5mrkg?autoplay=1" frameborder="0" allow="accelerometer; autoplay encrypted-media; gyroscope; picture-in-picture" allowfullscreen></iframe>');
                        //$('#phone-modal').modal('hide');
                        toastr.success("<?=Yii::t('app', 'Wiadomość wysłana')?>")
                    },
                    error: function () {
                        alert('Something went wrong');
                    }
                });
            });
        }
    </script>
    </body>
</html>
<?php $this->endPage() ?>



