<?php
/* @var $this \yii\web\View */
/* @var $model \backend\modules\permission\models\PermissionForm */

use yii\bootstrap\Html;
use kartik\form\ActiveForm;
use kartik\sortinput\SortableInput;
use kartik\sidenav\SideNav;

$this->title = 'Uprawnienia '.$model->role->description;
$this->params['breadcrumbs'][] = $this->title;

?>

<div id="premissions-mange">
    <?php $form = ActiveForm::begin(); ?>
    <div class="row">
        <div class="col-md-2">
            <?php echo SideNav::widget([
                    'items' => $roleMenuItems,
                    'heading' => Yii::t('app', 'Grupy użytkowników')
                ]);
            ?>
            <?php echo Html::a(Html::icon('plus').' '.Yii::t('app', 'Dodaj'), ['role/create'], ['class'=>['btn btn-block btn-success']]); ?>
            <?php echo Html::a(Html::icon('list').' '.Yii::t('app', 'Zarządzaj'), ['role/index'], ['class'=>['btn btn-block btn-default']]); ?>
            <br />
            <?php echo Html::submitButton(Html::icon('floppy-disk').' '.Yii::t('app', 'Zapisz'), ['class'=>'btn btn-primary btn-block']); ?>

        </div>
        <div class="col-md-2">
            <?php echo $form->field($model, 'assignedUsers')->widget(SortableInput::className(), [
                'items' => $model->getAssignedItems(),
//                'hideInput'=>false,
                'sortableOptions' => [
                    'connected'=>'users-sortable',
                ],

            ])->label(Yii::t('app', "Przypisani użytkownicy"));
            ?>
        </div>
        <div class="col-md-6">
            <?php foreach ($model->getPermissionList() as $label => $items): ?>
                <div class="ibox float-e-margins">
                <div class="ibox-title  newsystem-bg">
                                        <h5><?php echo $label; ?></h5>
                </div>
                <div class="ibox-content">
                <fieldset>
                    <table class="table table-condensed text-center table-striped">
                        <tr><th></th><th><?= Yii::t('app', 'Widok') ?></th><th><?= Yii::t('app', 'Edycja') ?></th><th><?= Yii::t('app', 'Dodawanie') ?></th><th><?= Yii::t('app', 'Usuwanie') ?></th></tr>
                        <?php foreach ($items as $description=>$item): ?>
                            <tr>
                                <td><?php echo $description; ?></td>
                                <?php foreach ($item as $action): ?>
<!--                                <td>--><?php //echo $action; ?><!--</td>-->
                                    <?php if ($action == false): ?>
                                        <td></td>
                                    <?php else: ?>
                                        <td><?php echo $form->field($model, "permissions[$action]")->checkbox([
                                            'label'=>null,
                                            ]); ?></td>
                                    <?php endif; ?>
                                <?php endforeach; ?>
                            </tr>
                        <?php endforeach; ?>
                    </table>
                </fieldset>
                </div>
                </div>
            <?php endforeach;

            $magazyn_in_row = 0;
            $magazyn_zew_in_row = 0;
            $strona_domowa_in_row = 0;
            $kalendarz_in_row = 0;

            ?>
            <?php foreach ($model->getGroupedPermissionList() as $label=>$items):
                if ($label == "Menu" || preg_match('@^_Menu@', $label)) {
                    if ($label == "Menu" ) { ?>
                <div class="ibox float-e-margins">
                <div class="ibox-title  newsystem-bg">
                                        <h5><?php echo $label; ?></h5>
                </div>
                <div class="ibox-content">
                                <table class="table table-condensed table-striped table-bordered text-center">
                        <?php
                    }
                        foreach ($items as $k => $v) :
                            $submenus = [
                                'menuEventsEvent',
                                'menuEventsMeeting',
                                'menuEventsPrivate',
                                'menuEventsRent',
                                'menuEventsVacation',
                                'menuCustomersCustomer',
                                'menuCustomersDiscount',
                                'menuCustomersContact',
                                'menuLocationsLocation',
                                'menuLocationsAttachment',
                                'menuGearsWarehouse',
                                'menuGearsOuterWarehouse',
                                'menuGearsOutcome',
                                'menuGearsIncome',
                                'menuGearsGear',
                                'menuGearsGearItem',
                                'menuGearsGearGroup',
                                'menuGearsBase',
                                'menuGearsCompany',
                                'menuGearsCategory',
                                'menuGearsGearAttachment',
                                'menuGearsService',
                                'menuUsersUser',
                                'menuUsersSkill',
                                'menuUsersSettlement',
                                'menuVehiclesVehicle',
                                'menuVehiclesAttachment',
                                'menuFinancesInvoice',
                                'menuFinancesExpense',
                                'menuFinancesSerie',
                            ];
                            $menu_group = [
                              'menuEvents',
                              'menuCustomers',
                              'menuLocations',
                              'menuGears',
                              'menuUsers',
                              'menuVehicles',
                              'menuFinances',
                            ];

                            $display = '';
                            if (in_array($k, $submenus)) {
                                $display = " style='display:none;'";
                            }

                        ?>
                            <tr class="<?= $k ?>" <?= $display ?>>
                                <td  id="<?= $k ?>" style="width: 30%; text-align: left; padding-left: 10px;">
                                    <?php
                                    if (in_array($k, $submenus)) {
                                        echo "<span style='margin-left: 20px;'>- </span>" . $v;
                                    }
                                    else {
                                        if (in_array($k, $menu_group)) {
                                            echo $v . "<span class='caret'></span>";
                                        }
                                        else {
                                            echo $v;
                                        }
                                    }
                                    ?>
                                </td>
                                <td style="text-align: left;"><?= $form->field($model, "permissions[$k]")->checkbox(['label'=>'']); ?></td>
                            </tr><?php
                        endforeach;


                    if ($label == "_Menu_11") { ?>
                                </table>
                            </div>
                        </div> <?php
                    }
                }
                else if ($label == "Magazyn" || preg_match('@^_Magazyn_@', $label)) {

                    if ($label == "Magazyn"){ ?>
                <div class="ibox float-e-margins">
                <div class="ibox-title  newsystem-bg">
                                        <h5><?php echo $label; ?></h5>
                </div>
                <div class="ibox-content">
                                <table class="table table-condensed table-striped table-bordered text-center">
                        <?php
                    }

                    foreach ($items as $k=>$v) {
                        if ($magazyn_in_row % 4 == 0) {echo "<tr>";} ?>
                            <td style="padding-left: 10px;"><?= $form->field($model, "permissions[$k]")->checkbox(['label'=>'']); ?></td>
                            <td style="text-align: left; padding-left: 10px;"><?= $v ?></td><?php
                        if ($magazyn_in_row+1 % 4 == 0) {echo "</tr>";}
                        $magazyn_in_row++;
                    }

                    if ($label == "_Magazyn_1") { ?>
                                </table>
                            </div>
                        </div><?php
                    }
                }
                else if ($label == "Finanse" ) { ?>

                <div class="ibox float-e-margins">
                <div class="ibox-title  newsystem-bg">
                                        <h5><?php echo $label; ?></h5>
                </div>
                <div class="ibox-content">
                            <table class="table table-condensed table-striped table-bordered text-center"><?php
                                $i = 0;
                                foreach ($items as $k=>$v) {
                                    if ($i % 3 == 0) {echo "<tr>";} ?>
                                        <td><?= $form->field($model, "permissions[$k]")->checkbox(['label'=>'']); ?></td>
                                        <td style="text-align: left; padding-left: 10px;"><?= $v ?></td><?php
                                    if ($i+1 % 3 == 0) {echo "</tr>";}
                                    $i++;
                                } ?>
                            </table>
                        </div>
                    </div><?php
                }
                else if ($label == "Magazyn zewnętrzny" || preg_match('@^_Magazyn zew@', $label)) {
                    if ($label == "Magazyn zewnętrzny") { ?>
                <div class="ibox float-e-margins">
                <div class="ibox-title  newsystem-bg">
                                        <h5><?php echo $label; ?></h5>
                </div>
                <div class="ibox-content">
                                <table class="table table-condensed table-striped table-bordered text-center"><?php
                    }

                    foreach ($items as $k=>$v) {
                        if ($magazyn_zew_in_row % 2 == 0) {echo "<tr>";} ?>
                            <td style="padding-left: 4px;"><?= $form->field($model, "permissions[$k]")->checkbox(['label'=>'']); ?></td>
                            <td style="text-align: left; padding-left: 10px;"><?= $v ?></td><?php
                        if ($magazyn_zew_in_row+1 % 2 == 0) {echo "</tr>";}
                        $magazyn_zew_in_row++;
                    }

                    if ($label == "_Magazyn zewnętrzny_0") { ?>
                                </table>
                            </div>
                        </div><?php
                    }
                }
                else if ($label == "Strona domowa" || preg_match('@^_Strona domo@', $label)) {
                    if ($label == "Strona domowa") { ?>
                <div class="ibox float-e-margins">
                <div class="ibox-title  newsystem-bg">
                                        <h5><?php echo $label; ?></h5>
                </div>
                <div class="ibox-content">
                                <table class="table table-condensed table-striped table-bordered text-center"><?php
                    }

                    foreach ($items as $k=>$v) {
                        if ($strona_domowa_in_row % 3 == 0) {echo "<tr>";} ?>
                            <td style="padding-left: 10px;"><?= $form->field($model, "permissions[$k]")->checkbox(['label'=>'']); ?></td>
                            <td style="text-align: left; padding-left: 10px;"><?= $v ?></td><?php
                        if ($strona_domowa_in_row+1 % 3 == 0) {echo "</tr>";}
                        $strona_domowa_in_row++;
                    }

                    if ($label == "_Strona domowa_0") { ?>
                                </table>
                            </div>
                        </div><?php
                    }
                }
                else if ($label == "Kalendarz" || preg_match('@^_Kalend@', $label)) {
                    if ($label == "Kalendarz") { ?>
                <div class="ibox float-e-margins">
                <div class="ibox-title  newsystem-bg">
                                        <h5><?php echo $label; ?></h5>
                </div>
                <div class="ibox-content">
                                <table class="table table-condensed table-striped table-bordered text-center"><?php
                    }

                    foreach ($items as $k=>$v) {
                        if ($kalendarz_in_row % 3 == 0) {echo "<tr>";} ?>
                            <td><?= $form->field($model, "permissions[$k]")->checkbox(['label'=>'']); ?></td>
                            <td style="text-align: left; padding-left: 10px;"><?= $v ?></td><?php
                        if ($kalendarz_in_row+1 % 3 == 0) {echo "</tr>";}
                        $kalendarz_in_row++;
                    }

                    if ($label == "_Kalendarz_0") { ?>
                                </table>
                            </div>
                        </div><?php
                    }
                }
                else {
                    $no_margin = '';
                    if ($label == "Magazyn zewnętrzny" || $label == "Strona domowa" || $label == "Kalendarz") {
                        $no_margin = " style='margin-bottom:0;'";
                    } ?>
                    <div class="ibox float-e-margins">
                    <?php if(preg_match('@^_@', $label) == false):?>
                
                            <div class="ibox-title  newsystem-bg">
                                                    <h5><?php echo $label; ?></h5>
                            </div>
               
                    <?php endif; ?>
                 <div class="ibox-content">
                        <table class="table table-condensed table-striped table-bordered text-center">
                            <thead>
                            <tr>
                                <?php foreach ($items as $k=>$v):?>
                                    <td class="text-center"><?php echo $v; ?></td>
                                <?php endforeach; ?>
                            </tr>
                            </thead>
                            <tbody>
                            <tr>
                                <?php foreach ($items as $k=>$v):?>
                                    <td><?php echo $form->field($model, "permissions[$k]")->checkbox([
                                            'label'=>'',
                                        ]); ?>
                                   </td>
                                <?php endforeach; ?>
                            </tr>
                            </tbody>
                        </table>
                    </div>
                </div>
            <?php } ?>
            <?php endforeach; ?>
                <fieldset>
                    <legend>
                        <div class="panel_mid_blocks">
                            <div class="panel_block" style="margin-bottom: 0;">
                                <div class="title_box">
                                    <h4><?= Yii::t('app', 'Inne') ?></h4>
                                </div>
                            </div>
                        </div>
                    </legend>

                    <ul class="list-unstyled">
                        <?php foreach ($model->getOtherPermissionList() as $action => $description): ?>
                            <li>
                            <?php echo $form->field($model, "permissions[$action]")->checkbox([
                                'label'=>$description,
                            ]); ?>
                            </li>
                        <?php endforeach; ?>
                    </ul>
                </fieldset>
        </div>
        <div class="col-md-2">
            <?php echo $form->field($model, 'users')->widget(SortableInput::className(), [
                'items' => $model->getUserItems(),
                'sortableOptions' => [
                    'connected'=>'users-sortable',
                ],

            ])->label(Yii::t('app', "Użytkownicy"));
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

$(".table-bordered").each(function(){
    $(this).removeClass("table-bordered");
});
$(".table-striped").each(function(){
    $(this).removeClass("table-striped");
});


$("#menuEvents").click(function(){
    $(".menuEventsEvent").toggle("slow");
    $(".menuEventsMeeting").toggle("slow");
    $(".menuEventsPrivate").toggle("slow");
    $(".menuEventsRent").toggle("slow");
    $(".menuEventsVacation").toggle("slow");
});

$("#menuCustomers").click(function(){
    $(".menuCustomersCustomer").toggle("slow");
    $(".menuCustomersDiscount").toggle("slow");
    $(".menuCustomersContact").toggle("slow");
});

$("#menuLocations").click(function(){
    $(".menuLocationsLocation").toggle("slow");
    $(".menuLocationsAttachment").toggle("slow");
});

$("#menuGears").click(function(){
    $(".menuGearsWarehouse").toggle("slow");
    $(".menuGearsOuterWarehouse").toggle("slow");
    $(".menuGearsOutcome").toggle("slow");
    $(".menuGearsIncome").toggle("slow");
    $(".menuGearsGear").toggle("slow");
    $(".menuGearsGearItem").toggle("slow");
    $(".menuGearsGearGroup").toggle("slow");
    $(".menuGearsCategory").toggle("slow");
    $(".menuGearsGearAttachment").toggle("slow");
    $(".menuGearsService").toggle("slow");
    $(".menuGearsBase").toggle("slow");
    $(".menuGearsCompany").toggle("slow");
});

$("#menuUsers").click(function(){
    $(".menuUsersUser").toggle("slow");
    $(".menuUsersSkill").toggle("slow");
    $(".menuUsersSettlement").toggle("slow");
});

$("#menuVehicles").click(function(){
    $(".menuVehiclesVehicle").toggle("slow");
    $(".menuVehiclesAttachment").toggle("slow");
});

$("#menuFinances").click(function(){
    $(".menuFinancesInvoice").toggle("slow");
    $(".menuFinancesExpense").toggle("slow");
    $(".menuFinancesSerie").toggle("slow");
});

');