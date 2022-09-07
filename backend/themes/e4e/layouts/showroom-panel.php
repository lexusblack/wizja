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

AppAsset::register($this);
MainPanelAsset::register($this);
$directoryAsset = Yii::$app->assetManager->getPublishedUrl('@vendor/jcabanillas/yii2-inspinia/assets');
?>
<?php $this->beginPage() ?>
<!DOCTYPE html>
<html lang="<?= Yii::$app->language ?>">
    <head>

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
                                                            <?php echo \common\widgets\WeatherWidget::widget(); ?>
                                                        </div>
                                            </div>
                                        </div>
                                    </div>
                                    <div  class="col-md-3" style="padding:0;">
                                        <div class="ibox float-e-margins" style="margin:0;">
                                        <div class="ibox-content no-padding border-left-right">
                                                <?php echo \common\widgets\CurrencyWidget::widget(); ?>
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
        var audioElement;
        $( document ).ready(function() {
            $("#message-top").load("/admin/chat/load", function() {
                });
            setInterval( function() { $("#message-top").load("/admin/chat/load", function() {
                });
              }, 15000 );

        audioElement = document.createElement('audio');
        audioElement.setAttribute('src', '/audio/chime.mp3');
        //audioElement.setAttribute('autoplay', 'autoplay');
        //audioElement.load()
        $.get();
        audioElement.addEventListener("load", function() {
        audioElement.play();
        }, true);


        });
        function openMessageDialog(id) {
            $('#page-wrapper').append("<div class='small-chat-box fadeInRight animated' id='messager-"+id+"''></div>");
            $('#messager-'+id).load("/admin/chat/loaddialog?id="+id, function() { 
                $(this).addClass('active');
                var elem = document.getElementById('messageContent-'+id);
                elem.scrollTop = elem.scrollHeight;
                });
        }

        function closeDialog(id){
            $('#messager-'+id).remove();
            window.clearInterval(intervals[id]);
        }
        function sendMessage(id) {
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

        function createEventChat(id){
            $.get("/admin/chat/createevent?id="+id, function(data){
                openMessageDialog(data);
            });         
        }

        function ajaxLoad(id) {
            var content;
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

        function createOrder()
        {
           var keys = $("#orderGear").yiiGridView("getSelectedRows");
           console.log(keys);
           var json = JSON.stringify(keys)
           location.href = "/admin/order/add?ids="+json;
        }
    </script>
    </body>
</html>
<?php $this->endPage() ?>
