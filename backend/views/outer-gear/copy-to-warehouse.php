<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;
use yii\widgets\DetailView;


/* @var $this yii\web\View */
/* @var $model common\models\GearItem */

$this->title = Yii::t('app', 'Dodaj sprzęt');
$this->params['breadcrumbs'][] = ['label' => Yii::t('app', 'Sprzęt zewnętrzny'), 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="outer-gear-item-create">

    <div class="outer-gear-item-form">

    <?php $form = ActiveForm::begin(); ?>

		<?php
        echo $form->field($model, 'category_id')->widget(\kartik\tree\TreeViewInput::className(), [
            // single query fetch to render the tree
            // use the Product model you have in the previous step
            'query' => \common\models\GearCategory::find()->addOrderBy('root, lft'),
            'headingOptions'=>['label'=>'Categories'],
            'asDropdown' => true,   // will render the tree input widget as a dropdown.
            'multiple' => false,     // set to false if you do not need multiple selection
            'fontAwesome' => false,  // render font awesome icons
            //'options'=>['disabled' => true],
        ])
        ?>

    <div class="form-group">
        <?= Html::submitButton(Yii::t('app', 'Zapisz'), ['class' =>  'btn btn-success']) ?>
    </div>

    <?php ActiveForm::end(); ?>
	<hr>
	<h3><?= Yii::t('app', 'Szczegóły modelu') ?></h3>
    <?= DetailView::widget([
        'model' => $outerGear,
        'attributes' => [
            'id',
            'name',
            'company_name',
            'quantity',
            'brightness',
            'power_consumption',
            'category.name',
            'width',
            'height',
            'depth',
            'volume',
            'weight',
            'info:html',
            [
                'attribute'=>'photo',
                'value' =>  Html::img($outerGear->getPhotoUrl(), ['style'=>'width: 300px;']),
                'format'=>'html',
            ],
        ],
    ]) ?>

</div>
<?php
$this->registerJs('var catField = $("#w1"); 
                var gearField = $("#'.Html::getInputId($model, 'gear_id').'");
                catField.on("change",function(){
                    var _this = $("this");
                    if(_this.val == ""){
                        gearField.prop("disabled", true).val("").trigger("change");
                    } else {
                        gearField.prop("disabled", false).val("").trigger("change");
                    }
                });');