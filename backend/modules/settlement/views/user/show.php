<?php
use yii\bootstrap\Modal;

use yii\helpers\Html;
use common\components\grid\GridView;
/* @var $this yii\web\View */
/* @var $model common\models\SettlementUser */
/* @var $user \common\models\User */


$formatter = Yii::$app->formatter;

$this->title = $user->getDisplayLabel();
$this->params['breadcrumbs'][] = ['label' => Yii::t('app', 'Rozliczenia'), 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
if ($user->rate_type==720)
{
    //$data['summary']['sum']+=$user->rate_amount;
    $data['summary2']['sum']+=$user->rate_amount;
}
?>
<div class="settlement-user-view">

    <h1><?= Html::encode($this->title) ?>  <small><?= Yii::t('app', 'Rodzaj stawki') ?>: <?php echo $data['rateType']; ?></small> <small><?= Yii::t('app', 'Stawka') ?>: <?php echo $data['rate']; ?></small></h1>

    <?php if (!$ajax) { ?>
    <div>
        <?= Html::a('<<', $data['prevUrl'], ['class'=>'btn btn-primary']); ?>
        <?= Html::a('>>', $data['nextUrl'], ['class'=>'btn btn-primary']); ?>
        <?= $data['dateString']; ?>
        <?= Html::dropDownList(null, $selectedItem, $dropdownItems, ['class' => 'changeMonth']) ?>
    </div>
    <?php  } ?>

    <div class="panel_mid_blocks">
        <div class="panel_block">
            <?= GridView::widget([
                'dataProvider' => $dataProvider,
                'tableOptions' => [
                    'class' => 'kv-grid-table table table-condensed kv-table-wrap'
                ],
                'filterModel' => null,
                'panel' => [
                    'heading'=>false,
                    'footer'=>false,
                ],
                'columns' => [
                    [
                        'label'=>Yii::t('app', 'Data'),
                        'format'=>'html',
                        'value'=>function($model)
                        {
                            $start = Yii::$app->formatter->asDateTime($model->event->getTimeStart(),'short');
                            $end = Yii::$app->formatter->asDateTime($model->event->getTimeEnd(), 'short');
                            return $start.' <br /> '.$end;
                        },
                        'contentOptions'=>['style'=>'min-width: 90px;']
                    ],
                    [
                        'label'=>Yii::t('app', 'Wydarzenie'),
                        'attribute'=>'event_id',
                        'value'=>function($model) use ($year, $month)
                        {
                            
                            $content =  Html::a($model->event->name." [".$model->event->code."]", ['/event/view', 'id'=>$model->event_id, '#'=>'tab-working-time'])." <br/>".Yii::t('app', 'Poziom ').$model->event->level;
                            
                            return $content;
                        },
                        'format'=>'html',
                    ],
                    [
                        'format'=>'html',
                        'label'=>Yii::t('app', 'Manager')."/".Yii::t('app', 'Dział')."/".Yii::t('app', 'Funkcja'),
                        'value'=>function($model){
                            return $model->event->managerDisplayLabel."<br/>".$model->departmentsString."<br/>".$model->rolesString;
                        }
                    ],
                    [
                        'label'=>Yii::t('app', 'Prowizja'),
                        'format'=>'html',
                        'value'=>function($model) use ($year, $month)
                        {
                            $content = "";
                            $event_end =$model->event->getTimeEnd();
                            $begin = $year."-".$month."-01";
                            $end = date("Y-m-t", strtotime($begin))." 23:59:59";
                            if (($event_end>=$begin)&&($event_end<=$end))
                            {
                                $show = true;
                            }else{
                                $show = false;
                            }
                            if ((isset($model->event->eventStatut))&&($model->event->eventStatut->count_provision)){
                                    $prov = "";
                            }else{
                                    $prov = Yii::t('app', ' (niezaakceptowana)');
                            }
                            $provs = $model->event->getUserGProvision($model->user_id);
                            if ($provs)
                            {
                                if ($show)
                                {
                                    foreach ($provs as $p)
                                    {
                                        $content .="<strong>".$p['name'].$prov."</strong>: ".Yii::$app->formatter->asCurrency($p['value'])."<br/>";
                                    }
                                    
                                }else{
                                    $content .="<strong>".Yii::t('app', 'Prowizja naliczana w ')."</strong>: ".date("m-Y", strtotime($model->event->getTimeEnd()));
                                }
                            }
                            return $content;
                            /*
                            if ($model->event->manager_id == $model->user_id){
                                if ($show)
                                {
                                    $content .="<br/><strong>".$prov."</strong>: ".Yii::$app->formatter->asCurrency($model->event->getTotalProvision());
                                }else{
                                    //$content .="<br/><strong>".Yii::t('app', 'Prowizja naliczana w ')."</strong>: ".date("m-Y", strtotime($model->event->getTimeEnd()));
                                }
                                
                            }
                            else
                                if ($model->user->role ==30){
                                    if ($show)
                                    {
                                        $content .="<br/><strong>".$prov."</strong>: ".Yii::$app->formatter->asCurrency($model->event->getSectionProvision($model->user));
                                    }else{
                                        $content .="<br/><strong>".Yii::t('app', 'Prowizja naliczana w ')."</strong>: ".date("m-Y", strtotime($model->event->getTimeEnd()));
                                    }
                                }
                            */
                        }
                    ],
                    [
                        'attribute'=>'workingHoursString',
                        'format'=>'raw',
                        'contentOptions'=>['style'=>'min-width: 130px;'],
                        'label'=>Yii::t('app', 'Godziny pracy'),
                        'value'=>function($model) use ($ajax) {
                            if ($ajax) {
                                return $model->workingHoursString;
                            }else{
                                $data = unserialize($model->working_hours_data);
                                    $formatter = Yii::$app->formatter;
                                    $content = Html::a(Yii::t('app', 'Dodaj'), ['#'], ['class' => 'btn btn-success btn-xs',
                                'onClick'=>'openTimeModal('.$model->event->id.'); return false;'])."<br/>";
                                    $content .= Html::beginTag('table');
                                    $content .= Html::tag('tr', Html::tag('th', Yii::t('app', 'Data')).Html::tag('th', Yii::t('app', 'Czas')));
                                    foreach ($data as $d)
                                    {
                                        $style = "";
                                        $p = $d['type'];
                                        if ($p==1)
                                        {
                                                $start = $model->event->packing_start;
                                                $end = $model->event->packing_end;
                                        }
                                        if ($p==2)
                                        {
                                                $start = $model->event->montage_start;
                                                $end = $model->event->montage_end;
                                        }
                                        if ($p==3)
                                        {
                                                $start = $model->event->event_start;
                                                $end = $model->event->event_end;
                                        }
                                        if ($p==4)
                                        {
                                                $start = $model->event->disassembly_start;
                                                $end = $model->event->disassembly_end;
                                        }
                                        if ($end)
                                        {
                                            $end = new DateTime($end);
                                            $start = new DateTime($start);
                                            $end->modify('+2 hours');
                                            $start->modify('-2 hours');
                                            if (($end->format('Y-m-d H:i:s') < $d['end_time'])||($start->format('Y-m-d H:i:s')> $d['start_time'])) {
                                                    $style ='color:red';
                                            }
                                        }
                                        $content .= Html::tag('tr',
                                            Html::tag('td', "<div class='nowrap'>od: " . $formatter->asDatetime($d['start_time'], 'short') . "</div><div class='nowrap'>do: " . $formatter->asDatetime($d['end_time'], 'short') . "</div>", ['class' => 'nowrap', 'style'=>$style])
                                            .Html::tag('td', str_replace(['dni', 'dzień', 'gidzin', 'godzina', 'godziny', 'minuty', 'minut', 'sekund', 'sekundy'], ['d', 'd', 'h', 'h', 'h', 'min', 'min', 's', 's'], $formatter->asDuration($d['duration']) )." ".Html::a('<i class="fa fa-pencil"></i>', ['#'], [
                                'onClick'=>'openEditTime('.$d['id'].'); return false;'])." ".Html::a('<i class="fa fa-trash"></i>', ['/event-user-working-time/delete', 'id'=>$d['id']], ['data' => ['confirm' => Yii::t('app', 'Na pewno chcesz usunąć?'), 'method' => 'post',]]) , ['class' => 'nowrap']));
                                    }
                                    $content .= Html::endTag('table');
                                    return $content;
                            }
                        }
                    ],
                    'workingHoursCostString:html:'.Yii::t('app', 'Podstawa'),
                    'rolesAddonsString:html:'.Yii::t('app', 'Funkcje dodatkowe'),
                    [
                        'attribute'=>'addonsString',
                        'format'=>'raw',
                        'contentOptions'=>['style'=>'min-width: 170px;'],
                        'label'=>Yii::t('app', 'Koszty dodatkowe'),
                        'value'=>function($model) use ($ajax, $formatter) {
                            if ($ajax) {
                                return $model->addonsString;
                            }else{
                                $content = Html::a(Yii::t('app', 'Dodaj koszt'), ['#'], ['class' => 'btn btn-success btn-xs',
                                'onClick'=>'openCostsModal('.$model->event->id.'); return false;'])." ".Html::a(Yii::t('app', 'Dodaj dietę'), ['#'], ['class' => 'btn btn-success btn-xs',
                                'onClick'=>'openDietsModal('.$model->event->id.'); return false;'])."<br/>";
                                $data = unserialize($model->addon_data);
                                foreach ($data as $k=>$v)
                                {
                                    if ($v['type']=='addon')
                                    {
                                        $start = substr(\common\models\EventUserAddon::findOne($v['id'])->start_time, 0, 7);
                                        $func = 'openEditCostModal';
                                        $func2 = '/event-user-addon/delete';
                                    }else{
                                        $start = substr(\common\models\EventUserAllowance::findOne($v['id'])->start_time, 0, 7);
                                        $func = 'openEditDietModal';
                                        $func2 = '/event-user-allowance/delete';
                                    }
                                    $content .= $v['label'].'/'.$formatter->asCurrency($v['amount'])." ".Html::a('<i class="fa fa-pencil"></i>', ['#'], [
                                'onClick'=>$func.'('.$v['id'].'); return false;'])." ".Html::a('<i class="fa fa-trash"></i>', [$func2, 'id'=>$v['id']], ['data' => ['confirm' => Yii::t('app', 'Na pewno chcesz usunąć?'), 'method' => 'post',]])."[".Yii::t('app', 'Zal. w ').substr($start, 0, 7)."]<br/>";
                                }
                                return $content;
                            }
                        }
                    ],
                    'sum:currency:'.Yii::t('app', 'Razem'),
                ],
            ]); ?>
        </div>
    </div>
    <div class="row">
        <div class="col-md-offset-6 col-md-2">
            <?php if ($data['status'] == \common\models\SettlementUser::STATUS_UNSETTLED && !$ajax){ ?>
                <?php
                if (Yii::$app->user->can('menuSettlementSave')) {
                    echo Html::a(Yii::t('app', 'Miesiąc rozliczony'), \common\helpers\Url::current(), ['class' => 'btn btn-block btn-primary',
                        'data' => ['method' => 'post', 'confirm' => Yii::t('app', 'Jesteś pewny?'),
                            'params' => ['status' => \common\models\SettlementUser::STATUS_SETTLED],]]);
                }
                }
                if ($data['status'] != \common\models\SettlementUser::STATUS_UNSETTLED && !$ajax){
                if (Yii::$app->user->can('menuSettlementSave2')) {
                    echo Html::a(Yii::t('app', 'Cofnij miesiąc rozliczony'), \common\helpers\Url::current(), ['class' => 'btn btn-block btn-danger',
                        'data' => ['method' => 'post', 'confirm' => Yii::t('app', 'Jesteś pewny?'),
                            'params' => ['status' => \common\models\SettlementUser::STATUS_UNSETTLED],]]);
                }
                    } ?>
        </div>
        <div class="col-md-4">
            <div class="panel_mid_blocks">
                <div class="panel_block" style="margin-bottom: 0;">
                    <div class="title_box">
                        <h4><?= Yii::t('app', 'Podsumowanie') ?></h4>
                    </div>
                    
                </div>
            </div>

            <div class="panel_mid_blocks">
                <div class="panel_block">
                    <dl class="dl-horizontal">
                        <dt><?= Yii::t('app', 'Stawka') ?></dt>
                        <dd><?= $data['rate']; ?></dd>
                        <dt><?= Yii::t('app', 'Przepracowanych h') ?></dt>
                        <dd><?= $data['summary2']['hours']; ?></dd>
                        <dt><?= Yii::t('app', 'Godziny pracy') ?></dt>
                        <dd><?= $data['summary']['salary']; ?></dd>
                        <dt><?= Yii::t('app', 'Prowizja') ?></dt>
                        <dd><?= $data['summary']['provision']; ?></dd>
                        <dt><?= Yii::t('app', 'Prowizja niezaakcpetowana') ?></dt>
                        <dd><?= $data['summary']['provision_non']; ?></dd>
                        <dt><?= Yii::t('app', 'Funkcje dodatkowe') ?></dt>
                        <dd><?= $data['summary']['roleAddons']; ?></dd>
                        <dt><?= Yii::t('app', 'Dodatki') ?></dt>
                        <dd><?= $data['summary']['addons']; ?></dd>
                        <dt><?= Yii::t('app', 'Diety') ?></dt>
                        <dd><?= $data['summary']['allowances']; ?></dd>
                        <dt><?= Yii::t('app', 'Suma') ?></dt>
                        <dd><?= $formatter->asCurrency($data['summary2']['sum']); ?></dd>
                         <dt><?= Yii::t('app', 'ZUS Społeczne') ?></dt>
                        <dd><?= $formatter->asCurrency($user->zus_rate); ?></dd>   
                         <dt><?= Yii::t('app', 'ZUS Zdrowotne') ?></dt>
                        <dd><?= $formatter->asCurrency($user->nfz_rate); ?></dd>  
                         <dt><?= Yii::t('app', 'Podatek dochodowy') ?></dt>
                        <dd><?php 
                        $brutto = ($data['summary2']['sum']+5*$user->nfz_rate/36)/(1-$user->tax_rate/100)+$user->zus_rate;
                        $brutto2 = $data['summary2']['sum']+$user->nfz_rate+$user->zus_rate;
                        if ($brutto>$brutto2)
                        {
                            $podatek = $brutto-$brutto2;
                        }else{
                            $podatek = 0;
                            $brutto = $brutto2;
                        }
                        echo $formatter->asCurrency($podatek);
                        ?></dd> 
                         <dt><?= Yii::t('app', 'Netto') ?></dt>
                        <dd><?= $formatter->asCurrency($brutto) ?></dd>  
                        <dt><?= Yii::t('app', 'Brutto') ?></dt>
                        <dd><?= $formatter->asCurrency($brutto*(1+$user->vat_rate/100)) ?></dd>                 
                    </dl>
                </div>
            </div>
        </div>

    </div>

    <?php
    //echo var_dump($data);
    $this->registerJs('

$(".changeMonth").change(function(){
    if ($(this).val()==0) {
        return;
    }
    var month = $(this).val().substring(5,7);
    var year = $(this).val().substring(0,4);
    location.href = "show?month="+month+"&year="+year+"&userId='.$user->id.'";
});

$(".table-bordered").each(function(){
    $(this).removeClass("table-bordered");
});
$(".table-striped").each(function(){
    $(this).removeClass("table-striped");
});
');

?>

</div>
<script src="https://unpkg.com/sweetalert/dist/sweetalert.min.js"></script>

<script type="text/javascript">
function openCostsModal(id){
    var modal = $("#costs");
    <?php if ($user->id!=Yii::$app->user->id){ ?>
        modal.find(".modalContent").load("/admin/event-user-addon/create?id="+id+"&userId=<?=$user->id?>");
    <?php }else{ ?>
        modal.find(".modalContent").load("/admin/event-user-addon/create?id="+id);
    <?php } ?>
    
    modal.modal("show");
    //alert(id);
}
function openTimeModal(id){
    var modal = $("#working_hours");
    modal.find(".modalContent").load("/admin/event-user-working-time/create?user_id=<?=$user->id?>&id="+id);
    modal.modal("show");
    //alert(id);
}
function openEditTime(id){
    var modal = $("#working_hours");
    modal.find(".modalContent").load("/admin/event-user-working-time/update?id="+id);
    modal.modal("show");
    //alert(id);
}
function openDietsModal(id){
    var modal = $("#diets");
    <?php if ($user->id!=Yii::$app->user->id){ ?>
        modal.find(".modalContent").load("/admin/event-user-allowance/create?id="+id+"&user_id=<?=$user->id?>");
    <?php }else{ ?>
        modal.find(".modalContent").load("/admin/event-user-allowance/create?id="+id);
    <?php } ?>
    modal.modal("show");
    //alert(id);
}

function openEditDietModal(id){
    var modal = $("#diets");
    modal.find(".modalContent").load("/admin/event-user-allowance/update?id="+id);
    modal.modal("show");
}

function openEditCostModal(id){
     var modal = $("#costs");
    modal.find(".modalContent").load("/admin/event-user-addon/update?id="+id);
    modal.modal("show");   
}

</script>
<?php
// --- Ekipa modal ---
Modal::begin([
    'header' => Yii::t('app', 'Godziny pracy'),
    'id' => 'working_hours',
    'class' => 'modal'
]);
echo "<div class='modalContent'></div>";
Modal::end();

// --- Vehicle modal ---
Modal::begin([
    'header' => Yii::t('app', 'Koszty dodatkowe'),
    'id' => 'costs',
    'class' => 'modal'
]);
echo "<div class='modalContent'></div>";
Modal::end();

// --- Event breaks modal ---
Modal::begin([
    'header' => Yii::t('app', 'Diety'),
    'id' => 'diets',
    'class' => 'modal'
]);
echo "<div class='modalContent'></div>";
Modal::end();
?>