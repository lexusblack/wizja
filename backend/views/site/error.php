<?php

use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $name string */
/* @var $message string */
/* @var $exception Exception */
$this->context->layout = 'main-panel';
$this->title = $name;
?>
<div class="site-error row">

    <div class="col-md-6 col-md-offset-3">
        <div class="jumbotron jumbotron-no-bg">
            <h1><?= Html::encode($this->title) ?></h1>

            <p>
                <?= nl2br(Html::encode($message)) ?>
            </p>

            <p>
                <?= Yii::t('app', 'Skontaktuj się z administratorem.') ?>
            </p>
        </div>
    </div>


</div>
