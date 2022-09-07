<?php

use yii\helpers\Html;
use yii\widgets\DetailView;

/* @var $this yii\web\View */
/* @var $model common\models\LocationAttachment */

?>

        <?php
        echo \common\widgets\PannellumWidget::widget([
            'imageFileUrl' => $model->getFileUrl(),
        ]);
        ?>
    <?php echo $this->render('_view', ['model'=>$model]) ?>

</div>
