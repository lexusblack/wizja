<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;

/* @var $this yii\web\View */
/* @var $model common\models\Gear */
/* @var $form yii\widgets\ActiveForm */
$return = true;
?>
<?php
        if ((!$gear->no_items)&&(!$items)) {
                echo "Wybierz egzemplarze z listy, które nie zostały wydane.";
                
                
        }else{ ?>
<div class="gear-form">

    <?php $form = ActiveForm::begin(); ?>

    <div class="row">
        <div class="col-md-6">
        <?php if ($model->type==1){
                $title = Yii::t('app', 'Dodaj egzemplarze');
        }
         if ($model->type==2){
                $title = Yii::t('app', 'Usuń egzemplarze');
        }
         if ($model->type==3){
                $title = Yii::t('app', 'Przenieś egzemplarze');
        }
        ?>
        <h1><?=$title?></h1>

            <?php  
$disabled = false;
            if (!$items) echo $form->field($model, 'quantity')->textInput(['maxlength' => true, 'autocomplete'=>"off"])->label(Yii::t('app', 'Liczba sztuk')); else{   $w = $items[0]->warehouse_id;
                    
                    foreach ($items as $i)
                    {
                        if ($i->warehouse_id!=$w)
                            $return = false;

                    }
                    $disabled = true;
                    echo "<h3>Przenosimy ".count($items)." sztuk</h3>"; } ?>
<?php if ($return){ ?>
            <?php if ($model->type==2)
            {
                if ($disabled)
                {
                    echo $form->field($model, 'warehouse_from')->hiddenInput(['maxlength' => true, 'autocomplete'=>"off"])->label(false);
                    echo "<p>Magazyn, z którego usuwamy sprzęt: ".yii\helpers\ArrayHelper::map(\common\models\Warehouse::find()->asArray()->all(), 'id' ,'name')[$w]."</p>";
                }else{


                    echo $form->field($model, 'warehouse_from')->widget(\kartik\widgets\Select2::className(), [
                'data' => yii\helpers\ArrayHelper::map(\common\models\Warehouse::find()->asArray()->all(), 'id' ,'name'),
                'options' => [
                    'placeholder' => Yii::t('app', 'Wybierz magazyn...')

                ],
                'pluginOptions' => [
                    'allowClear' => false,
                    'multiple' => false,
                ],
            ])->label(Yii::t('app', 'Magazyn, z którego usuwamy sprzęt'));
            }
            }
            ?>
            <?php if (($model->type==1))
            {
                $warehouses = \common\models\Warehouse::getList();

            if (count($warehouses)>1){ ?>

            <?php  echo $form->field($model, 'warehouse_to')->widget(\kartik\widgets\Select2::className(), [
                'data' => yii\helpers\ArrayHelper::map(\common\models\Warehouse::find()->asArray()->all(), 'id' ,'name'),
                'options' => [
                    'placeholder' => Yii::t('app', 'Wybierz magazyn...'),

                ],
                'pluginOptions' => [
                    'allowClear' => false,
                    'multiple' => false,
                ],
            ])->label(Yii::t('app', 'Magazyn, do którego dodajemy sprzęt'));

             }else{
                $w = \common\models\Warehouse::find()->where(['type'=>1])->one();
                $model->warehouse_to = $w->id;

                 echo $form->field($model, 'warehouse_to')->hiddenInput(['maxlength' => true])->label(false);

             } 
                    
            }
            ?>
            <?php if (($model->type==3))
            {
                if ($items)
                {

                    if ($return)
                    {
                        $model->warehouse_from = $w;
                        echo $form->field($model, 'warehouse_from')->widget(\kartik\widgets\Select2::className(), [
                'data' => yii\helpers\ArrayHelper::map(\common\models\Warehouse::find()->asArray()->all(), 'id' ,'name'),
                'options' => [
                    'placeholder' => Yii::t('app', 'Wybierz magazyn...'),

                ],
                'pluginOptions' => [
                    'allowClear' => false,
                    'multiple' => false,
                ],
            ])->label(Yii::t('app', 'Magazyn, z którego przenosimy sprzęt'));
                    }else{
                        ?>

                        <?php
                    }
                    
                }else{
                    echo $form->field($model, 'warehouse_from')->widget(\kartik\widgets\Select2::className(), [
                'data' => yii\helpers\ArrayHelper::map(\common\models\Warehouse::find()->asArray()->all(), 'id' ,'name'),
                'options' => [
                    'placeholder' => Yii::t('app', 'Wybierz magazyn...'),


                ],
                'pluginOptions' => [
                    'allowClear' => false,
                    'multiple' => false,
                ],
            ])->label(Yii::t('app', 'Magazyn, z którego przenosimy sprzęt'));
                }
                
                    echo $form->field($model, 'warehouse_to')->widget(\kartik\widgets\Select2::className(), [
                'data' => yii\helpers\ArrayHelper::map(\common\models\Warehouse::find()->asArray()->all(), 'id' ,'name'),
                'options' => [
                    'placeholder' => Yii::t('app', 'Wybierz magazyn...'),

                ],
                'pluginOptions' => [
                    'allowClear' => false,
                    'multiple' => false,
                ],
            ])->label(Yii::t('app', 'Magazyn, do którego przenosimy sprzęt'));
            }
            ?>
            <?= $form->field($model, 'info')->widget(\yii\redactor\widgets\Redactor::className(), [
                'clientOptions' => [
                    'buttons' => ['html','formatting', 'bold', 'italic', 'deleted',
                        'unorderedlist', 'orderedlist','outdent', 'indent', 'alignment', 'link', 'horizontalrule'],
                ]
            ])->label(Yii::t('app', 'Przyczyna/Komentarz'));?>

        </div>
        
    </div>





    <div class="form-group">
        <?= Html::submitButton(Yii::t('app', 'Zapisz'), ['class' =>  'btn btn-success']) ?>
    </div>

    <?php }else{ ?>
<div class="alert alert-danger">
                                Wybrane egzemplarze nie znajdują się w jednym magazynie, operacja jest niemożliwa.
                            </div>
     <?php   }?>

    <?php ActiveForm::end(); ?>

</div>
<?php } ?>