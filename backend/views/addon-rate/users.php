<?php
/* @var $this \yii\web\View */
/* @var $model \backend\models\UserAddonForm */
use kartik\dropdown\DropdownX;
use yii\bootstrap\Html;
use kartik\form\ActiveForm;
use kartik\sortinput\SortableInput;

$this->title = Yii::t('app', 'Dodatki');
$this->params['breadcrumbs'][] = $this->title;
$user = Yii::$app->user;
?>

<div id="addon-mange">
    <?php $form = ActiveForm::begin(); ?>
    <div class="row">
        <div class="col-md-3">
            <?php
            if ($user->can('settingsAddons')) {
                echo Html::a(Html::icon('plus') . ' ' . Yii::t('app', 'Dodaj stawkę'), ['create', 'level' => $model->level], ['class' => ['btn btn-block btn-primary']]);
            }
            if ($user->can('settingsRoleAdd')) {
                echo Html::a(Html::icon('plus-sign').' '.Yii::t('app', 'Dodaj rolę'), ['user-event-role/create', 'level'=>$model->level], ['class'=>['btn btn-block btn-primary']]);
            }
            if ($user->can('settingsAddonsRateManage')) {
                echo Html::a(Html::icon('list') . ' ' . Yii::t('app', 'Zarządzaj'), ['index'], ['class' => ['btn btn-block btn-default']]);
            }
            if ($user->can('settingsAddonsSave')) {
                echo Html::submitButton(Html::icon('floppy-disk').' '.Yii::t('app', 'Zapisz'), ['class'=>'btn btn-warning btn-block']);
            } ?>
            <br />

            <?php
            echo Html::beginTag('div', ['class'=>'dropdown']);
            echo Html::button(Yii::t('app', 'Rola').': '.$model->getCurrentRoleName().' <span class="caret"></span></button>',
                ['type'=>'button', 'class'=>'btn btn-default btn-block', 'data-toggle'=>'dropdown']);
            echo DropdownX::widget([
                'items' => $model->getMenuItems(),
            ]);
            echo Html::endTag('div');

            ?>
            <?php
                echo Html::beginTag('div', ['class'=>'dropdown']);
                echo Html::button(Yii::t('app', 'Poziom eventu').': '.$model->level.' <span class="caret"></span></button>',
                    ['type'=>'button', 'class'=>'btn btn-default btn-block', 'data-toggle'=>'dropdown']);
                echo DropdownX::widget([
                    'items' => $model->getMenu2Items(),
                ]);
                echo Html::endTag('div');

            ?>
            <?php
                echo Html::beginTag('div', ['class'=>'dropdown']);
                echo Html::button(Yii::t('app', 'Okres naliczania').': '.$model->periodLabel.' <span class="caret"></span></button>',
                    ['type'=>'button', 'class'=>'btn btn-default btn-block', 'data-toggle'=>'dropdown']);
                echo DropdownX::widget([
                    'items' => $model->getMenu3Items(),
                ]);
                echo Html::endTag('div');

            ?>
        </div>
        <div class="col-md-4">

            <?php
            foreach ($model->getRateList() as $id=>$amount)
            {
                echo $form->field($model, "assignedItems[$id]")->widget(SortableInput::className(), [
                    'items' => $model->getAssignedItems($id),
                    'sortableOptions' => [
                        'connected'=>'users-sortable',
                    ],

                ])->label($amount);
            }

            ?>
        </div>
        <div class="col-md-5">
            <?php echo $form->field($model, 'items')->widget(SortableInput::className(), [
                'items' => $model->getItems(),
                'sortableOptions' => [
                    'connected'=>'users-sortable',
                ],

            ])->label(Yii::t('app', "Pracownicy"));
            ?>
        </div>
    </div>

    <?php ActiveForm::end(); ?>
</div>

<?php

$this->registerJs('
    $("body").on("dragend", "li", function(){
        $("ul[id$=\"sortable\"").trigger("sortupdate");
    });
');

$this->registerCss('
.form-group.field-useraddonform-items {
    position: fixed;
}
#useraddonform-items-sortable :last-child {
    margin-bottom: 600px;
}

');