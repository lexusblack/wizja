<?php
/* @var $this \yii\web\View */
/* @var $model \backend\modules\permission\models\PermissionTree */

use yii\bootstrap\Html;
use kartik\form\ActiveForm;
use kartik\sortinput\SortableInput;
use kartik\sidenav\SideNav;
\common\assets\TreeTableAsset::register($this);

$this->title = 'Uprawnienia '.$model->role->description;
$this->params['breadcrumbs'][] = $this->title;
$user = Yii::$app->user;
?>
<?php 
    $groups_super_user = \common\helpers\ArrayHelper::map(\common\models\AuthItem::find()->where(['superuser'=>1])->asArray()->all(), 'name', 'name');
    $superusers = \common\models\User::find()->where(['active'=>1])->andWhere(['id'=>\common\helpers\ArrayHelper::map(\common\models\base\AuthAssignment::find()->where(['item_name'=>$groups_super_user])->asArray()->all(), 'user_id', 'user_id')])->count(); 
            $superuser = \common\models\Company::find()->where(['code'=>\Yii::$app->params['companyID']])->one();
        $users = \common\models\User::find()->where(['active'=>1])->count()-$superusers;
            $superuser = \common\models\Company::find()->where(['code'=>\Yii::$app->params['companyID']])->one();
    ?>
    <h1><?=Yii::t('app', 'Limit kont typu SuperUser: ').$superusers."/".$superuser->superusers_paid?></h1>
    <h3><?=Yii::t('app', 'Limit kont User: ').$users."/".$superuser->users_paid?></h3>
    <div id="premissions-mange">
        <?php $form = ActiveForm::begin(['id' => 'form-permissions']); ?>
        <div class="row">
            <div class="col-md-2">
                <?php echo SideNav::widget([
                    'items' => $roleMenuItems,
                    'heading' => Yii::t('app', 'Grupy użytkowników')
                ]);
                ?>
                <?php
                if ($user->can('settingsAccessControlAdd')) {
                    echo Html::a(Html::icon('plus').' '.Yii::t('app', 'Dodaj'), ['role/create'], ['class'=>['btn btn-block btn-success']]);
                } ?>
                <?php
                if ($user->can('settingsAccessControlManage')) {
                    echo Html::a(Html::icon('list') . ' '.Yii::t('app', 'Zarządzaj'), ['role/index'], ['class' => ['btn btn-block btn-default']]);
                } ?>
                <br />
                <?php
                if ($user->can('settingsAccessControlSave')) {
                    echo Html::submitButton(Html::icon('floppy-disk') . ' '.Yii::t('app', 'Zapisz'), ['class' => 'btn btn-primary btn-block, save-changes']);
                } ?>

            </div>
            <div class="col-md-2">
                <?php echo $form->field($model, 'assignedUsers')->widget(SortableInput::className(), [
                    'items' => $model->getAssignedItems(),
                    'sortableOptions' => [
                        'connected'=>'users-sortable',
                    ],

                ])->label(Yii::t('app', "Przypisani użytkownicy"));
                ?>
            </div>
            <div class="col-md-6">
                <table id="treeTable" class="treetable">
                    <tbody>
                        <?php $model->render() ?>
                    </tbody>
                </table>
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


    <div class="alert alert-warning saveing" role="alert" style="text-align: center;">
        <?= Yii::t('app', 'Zapisuje zmiany') ?> <span class="glyphicon glyphicon-refresh glyphicon-refresh-animate"></span>
    </div>

    <div class="alert alert-success saved" role="alert" style="text-align: center;">
        <?= Yii::t('app', 'Zapisano!') ?>
    </div>

<?php


$this->registerJs('

$("#treeTable").treetable({ 
    expandable: true, 
//    initialState: "expanded" 
});

// powiązanie uprawnień o tej samej nazwie
$(".permission_checkbox").change(function(){
    var checked = $(this).prop("checked");
    $(".permission_checkbox[name=\'"+$(this).attr("name")+"\']").prop("checked", checked);
});

// zapisywanie bez przeładowania strony
$(".save-changes").click(function(e){
    e.preventDefault();
    $(".saveing").show();
    $.post(
        $("#form-permissions").attr("action"),
        $("#form-permissions").serialize(),
        function() {
            $(".saveing").hide();
            $(".saved").show();
            setInterval(function(){ $(".saved").hide(); },1000);
        }
   );
   return false;
});

//*********************************************************************//

$(".permission_checkbox").click(function()
{
    var checked = $(this).prop("checked");
    var permission_id = $(this).parent().parent().parent().data("tt-id");
    checkChildren(permission_id, checked);
});

function checkChildren(permission_id, check)
{
    $("tr").filter("[data-tt-parent-id="+permission_id+"]").each(function(){

        $(this).find("input").prop("checked", check);
        checkChildren($(this).data("tt-id"), check);
    });
}

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

$("#menuOrders").click(function(){
    $(".menuOrder").toggle("slow");
    $(".menuOrderList").toggle("slow");
});

');

$this->registerCss('
    label {font-weight: normal;} 
    .saveing, .saved { position: fixed; top: 200px; left: 50%; margin-left: -200px; width: 200px; display: none;}
    .glyphicon-refresh-animate {
        -animation: spin 1s infinite linear;
        -webkit-animation: spin2 1s infinite linear;
        margin-left: 10px;
    }
    
    @-webkit-keyframes spin2 {
        from { -webkit-transform: rotate(0deg);}
        to { -webkit-transform: rotate(360deg);}
    }
    
    @keyframes spin {
        from { transform: scale(1) rotate(0deg);}
        to { transform: scale(1) rotate(360deg);}
    }
    .alert-success { border-color: #3c763d; }
    .alert-warning { border-color: #8a6d3b }
    ');