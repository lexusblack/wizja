<?php

use yii\helpers\Html;

?>
	<style type="text/css">
      body {
       padding-top: 0 !important;
       padding-bottom: 0 !important;
       padding-left: 30px !important;
       padding-bottom: 0 !important;
       margin:0 !important;
       width: 100% !important;
       -webkit-text-size-adjust: 100% !important;
       -ms-text-size-adjust: 100% !important;
       -webkit-font-smoothing: antialiased !important;
       font-family: "Roboto", Arial;
     }
       h1{
        color:#024A79;
        font-size:25px;
        font-weight:700;
        margin-top:10px;
        margin-bottom:30px;
       }
    </style>
	<h1><?php echo $model->subject ?></h1>
    <div class="content" style="font-size:13px; color:#797979;">
      <?php
        echo '<p> ' . $model->text . "</p>\n";
      ?>
    </div>
    <div class="button" style="height:40px; margin-top:50px;">
      <a style="padding:14px 25px; color:white; background-color:#1ab394; border-radius:21px; text-transform:uppercase; font-size:12px; letter-spacing:1px; text-decoration:none;" target='_blank' href="http://<?=Yii::$app->getRequest()->serverName?>/admin/site/order?hash=<?=$order->hash?>"><?= Yii::t('app', 'Potwierdź zamówienie') ?></a>
    </div>
