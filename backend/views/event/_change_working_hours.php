<?php
/* @var $model common\models\Event */

use common\helpers\Url;
use common\models\ConflictUserWorkingHours;
use common\models\EventUserPlannedWrokingTime;
use kartik\editable\Editable;
use kartik\popover\PopoverX;
use yii\bootstrap\Html;

/* @var $old_model common\models\Event */
/* @var $conflict common\models\ConflictUserWorkingHours */

$conflict_custom_working_time = [];
$conflict_vacation = [];
$conflict_planned_vacation = [];
$conflict_is_working = [];
$conflict_is_working_close_range = [];

foreach ($conflicts as $conflict) {
    if ($conflict->type === ConflictUserWorkingHours::CUSTOM_WORKING_TIME) {
        $conflict_custom_working_time[] = $conflict;
    }
    if ($conflict->type === ConflictUserWorkingHours::VACATIONS) {
        $conflict_vacation[] = $conflict;
    }
    if ($conflict->type === ConflictUserWorkingHours::PLANNED_VACATIONS) {
        $conflict_planned_vacation[] = $conflict;
    }
    if ($conflict->type === ConflictUserWorkingHours::ALREADY_WORKING) {
        $conflict_is_working[] = $conflict;
    }
    if ($conflict->type === ConflictUserWorkingHours::WORKING_IN_CLOSE_RANGE) {
        $conflict_is_working_close_range[] = $conflict;
    }
}


\kartik\form\ActiveForm::begin(['id' => 'resolve-conflict', 'method' => 'POST', 'action' => Url::toRoute(['event/resolve-conflict'])]);

$i = 1;
if ($conflict_custom_working_time) { ?>
    <div class="panel panel-danger">
        <div class="panel-heading"><?= Yii::t('app', 'Niestandardowe godziny pracy na tym wydarzeniu') ?></div>
        <div class="panel-body">

            <table class="table table-bordered">
                <thead>
                    <tr>
                        <td><?= Yii::t('app', 'Osoba') ?></td>
                        <td><?= Yii::t('app', 'Godziny pracy') ?></td>
                        <td><?= Yii::t('app', 'Co zrobić?') ?></td>
                    </tr>
                </thead>
                <tbody>

                <?php
                foreach ($conflict_custom_working_time as $conflict) { ?>
                    <input type="hidden" value="<?= base64_encode(gzdeflate(serialize(($conflict)))) ?>" name="model[<?= $i ?>]">
                    <tr>
                        <td><?= $conflict->eventUserPlannedWorkingTime->user->getDisplayLabel() ?></td>
                        <td style="white-space: nowrap;">
                            <input class="input-daterangepicker" data-id="<?= $conflict->eventUserPlannedWorkingTime->id ?>" data-start="<?= $conflict->eventUserPlannedWorkingTime->start_time ?>" data-end="<?= $conflict->eventUserPlannedWorkingTime->end_time ?>" style="position: inherit;" value="<?= $conflict->eventUserPlannedWorkingTime->start_time . " - " . $conflict->eventUserPlannedWorkingTime->end_time ?>">
                            <?= Html::a(Html::icon('floppy-disk'), null, ['class' => 'save-custom-working-hours']) ?>
                        </td>
                        <td><?= Html::dropDownList('selected_value['.$i.']', null, ConflictUserWorkingHours::OPTIONS[ConflictUserWorkingHours::CUSTOM_WORKING_TIME]); ?></td>
                    </tr>
                    <?php
                    $i++;
                } ?>

                </tbody>
            </table>

        </div>
    </div><?php
}



if ($conflict_vacation) { ?>
    <div class="panel panel-danger">
        <div class="panel-heading"><<?= Yii::t('app', 'Zatwierdzony urlop') ?></div>
        <div class="panel-body">

            <table class="table table-bordered">
                <thead>
                    <tr>
                        <td><?= Yii::t('app', 'Osoba') ?></td>
                        <td><?= Yii::t('app', 'Urlop') ?></td>
                        <td><?= Yii::t('app', 'Co zrobić?') ?></td>
                    </tr>
                </thead>
                <tbody>

                <?php
                foreach ($conflict_vacation as $conflict) { ?>
                    <input type="hidden" value="<?= base64_encode(gzdeflate(serialize(($conflict)))) ?>" name="model[<?= $i ?>]">
                    <tr>
                        <td><?= $conflict->vacation->user->getDisplayLabel() ?></td>
                        <td style="white-space: nowrap;"><?= $conflict->vacation->start_date . " - " . $conflict->vacation->end_date ?></td>
                        <td><?= Html::dropDownList('selected_value['.$i.']', null, ConflictUserWorkingHours::OPTIONS[ConflictUserWorkingHours::VACATIONS]); ?></td>
                    </tr>
                    <?php
                    $i++;
                } ?>

                </tbody>
            </table>

        </div>
    </div><?php
}

if ($conflict_planned_vacation) { ?>
    <div class="panel panel-danger">
    <div class="panel-heading"><?= Yii::t('app', 'Zaplanowany urlop') ?></div>
    <div class="panel-body">

        <table class="table table-bordered">
            <thead>
            <tr>
                <td><?= Yii::t('app', 'Osoba') ?></td>
                <td><?= Yii::t('app', 'Urlop') ?></td>
                <td><?= Yii::t('app', 'Co zrobić?') ?></td>
            </tr>
            </thead>
            <tbody>

            <?php
            foreach ($conflict_planned_vacation as $conflict) { ?>
                <input type="hidden" value="<?= base64_encode(gzdeflate(serialize(($conflict)))) ?>" name="model[<?= $i ?>]">
                <tr>
                    <td><?= $conflict->vacation->user->getDisplayLabel() ?></td>
                    <td style="white-space: nowrap;"><?= $conflict->vacation->start_date . " - " . $conflict->vacation->end_date ?></td>
                    <td><?= Html::dropDownList('selected_value['.$i.']', null, ConflictUserWorkingHours::OPTIONS[ConflictUserWorkingHours::PLANNED_VACATIONS]); ?></td>
                </tr>
                <?php
                $i++;
            } ?>

            </tbody>
        </table>

    </div>
    </div><?php
}


if ($conflict_is_working) { ?>
    <div class="panel panel-danger">
    <div class="panel-heading"><?= Yii::t('app', 'Pracuje w czasie innego eventu') ?></div>
    <div class="panel-body">

        <table class="table table-bordered">
            <thead>
            <tr>
                <td><?= Yii::t('app', 'Osoba') ?></td>
                <td><?= Yii::t('app', 'Pracuje w godzinach') ?></td>
                <td><?= Yii::t('app', 'W czasie wydarzenia') ?></td>
                <td><?= Yii::t('app', 'Co zrobić?') ?></td>
            </tr>
            </thead>
            <tbody>

            <?php
            $event_done = [];
            foreach ($conflict_is_working as $conflict) {
                if (!in_array($conflict->second_event->id, $event_done)) {
                    $options = [
                        0 => Yii::t('app', 'Usunąć pracownika z wydarzenia').' ' . $conflict->first_event->getDisplayLabel(),
                        1 => Yii::t('app', 'Usunąć pracownika z wydarzenia').' ' . $conflict->second_event->getDisplayLabel(),
                    ] ?>
                    <input type="hidden" value="<?= base64_encode(gzdeflate(serialize(($conflict)))) ?>" name="model[<?= $i ?>]">
                    <tr>
                        <td><?= $conflict->eventUserPlannedWorkingTime->user->getDisplayLabel() ?></td>
                        <td style="white-space: nowrap;"><?php foreach (EventUserPlannedWrokingTime::find()->where(['user_id' => $conflict->eventUserPlannedWorkingTime->user_id])->andWhere(['event_id' => $conflict->second_event->id])->all() as $time) {
                                echo $time->start_time . " - " . $time->end_time . "<br>";
                            } ?></td>
                        <td><?= Html::a($conflict->second_event->getDisplayLabel(), ['event/view', 'id' => $conflict->second_event->id], ['target' => '_blank']) ?></td>
                        <td><?= Html::dropDownList('selected_value['.$i.']', null, $options, ['style' => ['width' => '200px;']]); ?></td>
                    </tr>
                    <?php
                    $i++;
                }
            } ?>

            </tbody>
        </table>

    </div>
    </div><?php
}


if ($conflict_is_working_close_range) { ?>
    <div class="panel panel-danger">
    <div class="panel-heading"><?= Yii::t('app', 'Pracuje w okresie +/- 12h na innym wydarzeniu') ?></div>
    <div class="panel-body">

        <table class="table table-bordered">
            <thead>
            <tr>
                <td><?= Yii::t('app', 'Osoba') ?></td>
                <td><?= Yii::t('app', 'Pracuje w godzinach') ?></td>
                <td><?= Yii::t('app', 'W czasie wydarzenia') ?></td>
                <td><?= Yii::t('app', 'Co zrobić?') ?></td>
            </tr>
            </thead>
            <tbody>

            <?php
            $event_done = [];
            foreach ($conflict_is_working_close_range as $conflict) {
                if (!in_array($conflict->second_event->id, $event_done)) {
                    $event_done[] = $conflict->second_event->id;
                    $options = [
                        0 => Yii::t('app', 'Usunąć pracownika z wydarzenia').' ' . $conflict->first_event->getDisplayLabel(),
                        1 => Yii::t('app', 'Usunąć pracownika z wydarzenia').' ' . $conflict->second_event->getDisplayLabel(),
                        2 => Yii::t('app', 'Pozostawić bez zmian')
                    ] ?>
                    <input type="hidden" value="<?= base64_encode(gzdeflate(serialize(($conflict)))) ?>" name="model[<?= $i ?>]">
                    <tr>
                        <td><?= $conflict->eventUserPlannedWorkingTime->user->getDisplayLabel() ?></td>
                        <td style="white-space: nowrap;"><?php foreach (EventUserPlannedWrokingTime::find()->where(['user_id' => $conflict->eventUserPlannedWorkingTime->user_id])->andWhere(['event_id' => $conflict->second_event->id])->all() as $time) {
                                echo $time->start_time . " - " . $time->end_time . "<br>";
                            } ?></td>
                        <td><?= Html::a($conflict->second_event->getDisplayLabel(), ['event/view', 'id' => $conflict->second_event->id], ['target' => '_blank']) ?></td>
                        <td><?= Html::dropDownList('selected_value['.$i.']', null, $options, ['style' => ['width' => '200px;']]); ?></td>
                    </tr>
                    <?php
                    $i++;
                }
            } ?>

            </tbody>
        </table>

    </div>
    </div><?php
}


echo Html::submitButton(Yii::t('app', 'Zmień'), ['id'=>'button-resolve-conflict', 'class' => 'btn btn-primary']);
\kartik\form\ActiveForm::end();

