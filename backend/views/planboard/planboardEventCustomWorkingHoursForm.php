<?php

use common\models\User;
use yii\bootstrap\Html;
use yii\helpers\Url;

/** @var array $customHours */
/** @var common\models\event $event */

foreach ($customHours as $hours) { ?>

    <table class="table table-bordered">
        <thead>
            <tr>
                <th><?= $hours[0] ?></th>
                <th><?= $hours[1] ?></th>
                <th><?= Html::a(Html::icon('trash'), null, ['class' => 'delete-custom-working-hours', 'data' => ['event_id' => $event->id, 'start' => $hours[0], 'end' => $hours[1]]]) ?></th>
            </tr>
        </thead>
        <tbody><?php
        foreach ($event->users as $user) {
            $checked = null;
            if (\common\models\EventUserPlannedWrokingTime::find()->where(['event_id'=>$event->id])->andWhere(['user_id'=>$user->id])->andWhere(['start_time'=>$hours[0]])->andWhere(['end_time'=>$hours[1]])->one() )  {
                $checked = "checked";
            }

            $disabled = null;
            if (!User::findOne(['id' => $user->id])->isAvailableInRangeWithoutThisRange($hours[0], $hours[1])) {
                $disabled = "disabled";
            }
            ?>

            <tr>
                <td colspan="2"><?= $user->getDisplayLabel() ?></td>
                <td><input type="checkbox" <?= $checked ?> <?= $disabled ?> class="user_working_hours" data-userid="<?= $user->id ?>" data-eventid="<?= $event->id ?>" data-start="<?= $hours[0] ?>" data-end="<?= $hours[1] ?>"></td>
            </tr>

            <?php
        }
        ?>
        </tbody>
    </table>
<?php

}

?>

<table class="table table-bordered">
    <thead>
    <tr>
        <th><input type="text" name="new_start" class="new_start"></th>
        <th><input type="text" name="new_end" class="new_end"></th>
        <th><?= Html::a(Html::icon('floppy-disk'), null, ['class' => 'add-custom-working-hours', 'data' => ['event_id' => $event->id]]) ?></th>
    </tr>
    </thead>
    <tbody><?php
    foreach ($event->users as $user) { ?>
        <tr>
            <td colspan="2"><?= $user->getDisplayLabel() ?></td>
            <td><input type="checkbox" class="new_user" data-userid="<?= $user->id ?>" data-eventid="<?= $event->id ?>"></td>
        </tr>
        <?php
    }
    ?>
    </tbody>
</table>

<?php

$this->registerJs('


$(".user_working_hours").click(function(){
    var data = {
        add: $(this).is(":checked"),
        user_id: $(this).data("userid"),
        event_id: $(this).data("eventid"),
        start: $(this).data("start"),
        end: $(this).data("end")
    }
    $.post("/admin/crew/manage-working-hours", data);
});

$(".delete-custom-working-hours").click(function(){
    if (window.confirm("'. Yii::t('app', 'Czy na pewno usunąć niestandardowe godziny pracy?').'")) {
        $(this).parent().parent().parent().parent().slideUp("slow");
        $.post("/admin/event/delete-custom-working-hours?event_id=" + $(this).data("event_id") + "&start=" + $(this).data("start") + "&end=" + $(this).data("end"));    
    }
});

$(".add-custom-working-hours").click(function(){
    $(".new_user").each(function(){
        if ($(this).is(":checked")) {
            var data = {
                add: true,
                user_id: $(this).data("userid"),
                event_id: $(this).data("eventid"),
                start: $(".new_start").val(),
                end: $(".new_end").val()
            }
            $.ajax({
                type: "post",
                url: "/admin/crew/manage-working-hours", 
                data: data,
                async: false,    
            });
        }        
    });
    var modal = $("#event_custom_working_hours_modal");
    modal.find(".modalContent").load("'.Url::to("event-custom-working-hours-form").'?event_id=" + $(this).data("event_id"), function () {
            $(".new_start").datetimepicker({format: "yyyy-mm-dd hh:ii", autoclose: true});
            $(".new_end").datetimepicker({format: "yyyy-mm-dd hh:ii", autoclose: true});
        });
});

');