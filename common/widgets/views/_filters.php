<?php
/* @var $this \yii\web\View; */
/* @var $model \common\models\form\CalendarSearch */
use kartik\form\ActiveForm;
use common\models\form\CalendarSearch;
use yii\bootstrap\Html;
use kartik\select2\Select2;
use common\models\User;

$user = Yii::$app->user;
?>
<?= Html::a(Yii::t('app', 'Pokaż/ukryj filtry'), '#', ['class'=>'btn btn-info show-hide-filter btn-xs'])?>
<div class="calendar-filters invisible">
    <?php
    $year = Yii::$app->request->get('year');
    $month = Yii::$app->request->get('month');
    $action = null;
    if ($year && $month) {
        $action = '/site/calendar?year='.$year."&month=".$month;
    }

    $form = ActiveForm::begin([
        'id' => 'calendar-filter-form',
        'type' => ActiveForm::TYPE_INLINE,
        'method'=>'get',
        'action'=>[$action],
    ]);
    ?>
    <?php
    echo $form->field($model, 'name')->textInput(['options' => [
                    'placeholder' => Yii::t('app', 'Nazwa'),
                    ]])->label(false);
//        echo $form->field($model, 'type')->dropDownList(CalendarSearch::typeList(), ['prompt'=>'Wszystkie typy']);
    if ($user->can('calendarFiltersType'))
    {
        echo $form->field($model, 'type')->widget(Select2::className(), [
            'data'=>CalendarSearch::typeList(),
            'options' => [
                'placeholder' => Yii::t('app', 'Typ'),
                'multiple'=>true,

            ],
            'pluginOptions' => [
                'allowClear' => true,

            ]

        ]);
    }

    ?>
    <?php //echo $form->field($model, 'department')->dropDownList(\common\models\Department::getModelList(), ['prompt'=>'Wszystkie działy']); ?>
    <?php
    if ($user->can('calendarFiltersDepartment')) {
        echo $form->field($model, 'department')->widget(Select2::className(), [
            'data' => \common\models\Department::getModelList(),
            'options' => [
                'placeholder' => Yii::t('app', 'Dział'),
            ],
            'pluginOptions' => [
                'allowClear' => true,
                'dropdownAutoWidth' => true,
                'multiple'=>true,
            ]

        ]);
    }
    ?>
    <?php //echo $form->field($model, 'manager')->dropDownList(User::getList([User::ROLE_MANAGER]), ['prompt' => 'Wszyscy PM']); ?>
    <?php
    if ($user->can('calendarFiltersPm')) {
        echo $form->field($model, 'manager_id')->widget(Select2::className(), [
            'data' => \common\models\User::getList([\common\models\User::ROLE_PROJECT_MANAGER, \common\models\User::ROLE_ADMIN, \common\models\User::ROLE_SUPERADMIN]),
            'options' => [
                'placeholder' => 'PM',
            ],
            'pluginOptions' => [
                'allowClear' => true,
                'dropdownAutoWidth' => true,
                'multiple'=>true,
            ]

        ]);
    }
    ?>
    <?php //echo $form->field($model, 'customer')->dropDownList(\common\models\Customer::getList(), ['prompt'=>'Wszyscy klienci']); ?>
    <?php
    if ($user->can('calendarFiltersClients')) {
        echo $form->field($model, 'customer')->widget(Select2::className(), [
            'data' => \common\models\Customer::getList(),
            'options' => [
                'placeholder' => Yii::t('app', 'Klient'),
            ],
            'pluginOptions' => [
                'allowClear' => true,
                'dropdownAutoWidth' => true,
                'multiple'=>true,
            ]

        ]);
    }
    ?>
    <?php //echo $form->field($model, 'contact')->dropDownList(\common\models\Contact::getList(), ['prompt' => 'Wszystkie kontakty']); ?>
    <?php
    if ($user->can('calendarFiltersContacts')) {
        echo $form->field($model, 'contact')->widget(Select2::className(), [
            'data' => \common\models\Contact::getList(),
            'options' => [
                'placeholder' => Yii::t('app', 'Kontakt'),
            ],
            'pluginOptions' => [
                'allowClear' => true,
                'dropdownAutoWidth' => true,
                'multiple'=>true,
            ]

        ]);
    }
    ?>
    <?php //echo $form->field($model, 'user')->dropDownList(User::getList([User::ROLE_USER, User::ROLE_MANAGER]), ['prompt' => 'Wszyscy pracownicy']); ?>
    <?php
    if ($user->can('calendarFiltersUsers')) {
        echo $form->field($model, 'user_filter')->widget(Select2::className(), [
            'data' => User::getList([User::ROLE_USER, User::ROLE_PROJECT_MANAGER, User::ROLE_ADMIN]),
            'options' => [
                'placeholder' => Yii::t('app', 'Pracownik'),
            ],
            'pluginOptions' => [
                'allowClear' => true,
                'dropdownAutoWidth' => true
            ]

        ]);
    }
    ?>

    <?php
    if ($user->can('calendarFiltersProjectStatus')) {
        echo $form->field($model, 'projectStatus')->widget(Select2::className(), [
            'data' => CalendarSearch::projectStatusList(),
            'options' => [
                'placeholder' => Yii::t('app', 'Status projektu'),
            ],
            'pluginOptions' => [
                'allowClear' => true,
                'multiple'=>true
            ]

        ]);
    }
    ?>

    <?php
    if ($user->can('calendarFiltersRentStatus')) {
        echo $form->field($model, 'rentStatus')->widget(Select2::className(), [
            'data' => \common\models\Rent::getStatusList(),
            'options' => [
                'placeholder' => Yii::t('app', 'Status wypożyczenia'),
            ],
            'pluginOptions' => [
                'allowClear' => true,
                'multiple'=>true
            ]

        ]);
    }


            if ($user->can('eventsEventEditStatus')){
            $i=0;
            foreach (\common\models\EventAdditionalStatut::find()->where(['active'=>1])->all() as $s)
            {
                $i++;
                if ($s->showToUser())
                {
                            echo $form->field($model, 'statut'.$i)->widget(Select2::className(), [
                            'data' => $s->getStatusList(1),
                            'options' => [
                                'placeholder' => $s->name, 
                            ],
                            'pluginOptions' => [
                                'allowClear' => true,
                                'multiple'=>true
                            ]

                        ]);
                }

            }
        }
    ?>

    <?= Html::submitButton(Yii::t('app', 'Zastosuj'), ['class' => 'btn btn-primary']) ?>
    <?= Html::a(Yii::t('app', 'Wyczyść'), ['site/calendar', 'cleanCalendar' => 1], ['class' => 'btn btn-default']) ?>
    <?php ActiveForm::end(); ?>
</div>

<?php $this->registerCss(
    '
.calendar-filters.invisible{
    display:none;
}
#calendar-filter-form .form-group.field-calendarsearch-type{
    max-width:250px;
    min-width:150px;
}
#calendar-filter-form .form-group.field-calendarsearch-name{
        max-width:250px;
        min-width:150px;
    }
    ');

$this->registerJs(
    '
    $(".show-hide-filter").click(function(e){
        e.preventDefault();
        $(".calendar-filters").toggleClass("invisible");
    });
    ');
