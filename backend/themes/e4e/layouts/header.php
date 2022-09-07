<?php
use yii\helpers\Url;
use yii\helpers\Html;
use yii\bootstrap\Modal;
Modal::begin([
    'id' => 'phone-modal',
    'header' => Yii::t('app', 'Podaj numer telefonu, aby wysłać link do aplikacji'),
    'options' => [
        'tabindex' => false,
    ],
    'clientOptions' => [
    'keyboard'=> false,
        'backdrop'=> 'static'
    ]
]);
echo "<div class='modalContent'></div>";
Modal::end();
?>
<nav class="navbar navbar-static-top  " role="navigation" style="margin-bottom: 0">

    <?php if(!\Yii::$app->user->isGuest): ?>
    <div class="navbar-header">
        <a class="navbar-minimalize minimalize-styl-2 btn btn-newsystem " href="#"><i class="fa fa-bars"></i> </a>
    </div>
    <?php endif?>

    <ul class="nav navbar-top-links navbar-right">
    
    <li>
        <a href="#" class="show-phone-modal" style="padding:5px" data-type="google"><img alt="image"  style="height:38px" src="/themes/e4e/img/google.png?data=2019"/></a>
    </li>
    <li>
        <a href="#" class="show-phone-modal" style="padding:5px" data-type="apple"><img alt="image"  style="height:38px" src="/themes/e4e/img/Download_on_the_App_Store_Badge_PL_RGB_blk_100317.svg?data=2019"/></a>
    </li>
        <li>
            <a href="<?=Url::to(['/site/first-use'])?>">
                <i class="fa fa-question-circle"></i><span class="m-r-sm text-muted welcome-message"><?= Yii::t('app', 'Instrukcja') ?></span>
            </a>
        </li>
        <li>
            <a href="<?=Url::to(['/request/index'])?>">
                <i class="fa fa-warning"></i><span class="m-r-sm text-muted welcome-message"><?= Yii::t('app', 'Pomoc techniczna') ?></span>
                <?php $reqnotes = \common\models\Request::getNotRead(); 
                if ($reqnotes){ ?>
                <span class="label label-danger"><?=$reqnotes?></span>
                <?php } ?>
            </a>
        </li>
                <li class="dropdown" id="message-top">

                </li>
                <li class="dropdown" id="notification-top">

                </li>
        <li>
            <?php if(!\Yii::$app->user->isGuest): ?>
            <span class="m-r-sm text-muted welcome-message"><?= Yii::t('app', 'Witaj') ?>, <?=\Yii::$app->user->identity->displayLabel?></span>
            <?php endif?>
        </li>

        <?php if(\Yii::$app->user->isGuest):?>
        <li>
            <a href="<?=Url::to(['/site/login'])?>">
                <i class="fa fa-sign-in"></i> <?= Yii::t('app', 'Zaloguj') ?>
            </a>
        </li>
        <?php else:?>
        <li>
            <a href="<?=Url::to(['/site/logout'])?>" data-method="post">
                <i class="fa fa-sign-out"></i> <?= Yii::t('app', 'Wyloguj') ?>
            </a>
        </li>
        <?php endif?>
    </ul>
             <?php       echo Html::beginTag('div', ['class'=>'navbar-center navbar-text']);
                        echo \common\widgets\LanguagePicker::widget();
                         echo Html::endTag('div'); ?>

</nav>