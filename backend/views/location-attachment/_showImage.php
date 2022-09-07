<?php

use yii\helpers\Html;
use yii\widgets\DetailView;

/* @var $this yii\web\View */
/* @var $model common\models\LocationAttachment */

?>

    <div class="thumbnail">
    <?php echo Html::img($model->getFileUrl()); ?>
    </div>
    <?php echo $this->render('_view', ['model'=>$model]) ?>
