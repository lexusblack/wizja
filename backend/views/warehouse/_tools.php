<?php
/* @var $this \yii\web\View */
/* @var $warehouse \common\models\form\WarehouseSearch; */
use yii\bootstrap\Html;
use yii\helpers\Url;
use kartik\widgets\DatePicker;
$user = Yii::$app->user;
$request = Yii::$app->request;
?>
<div class="row">
    <div class="tools warehouse-tools col-md-6">
    <div class="ibox">
        <div class="search-form">
                <?php echo Html::beginForm(Url::current(['to_date'=>null, 'from_date'=>null, 'q'=>null]), 'get', ['class'=>'form-inline']); ?>

            <div class="form-group">
                <?php echo Html::textInput('q', $warehouse->q, ['placeholder'=>Yii::t('app', 'Szukaj'), 'class'=>'form-control', 'autocomplete'=>"off"]); ?>
            </div>
            <?php if (!$w) { ?>
                        <div class="form-group">
            <?php
            echo DatePicker::widget([
                'name' => 'from_date',
                'value' => $warehouse->from_date,
                'type' => DatePicker::TYPE_RANGE,
                'name2' => 'to_date',
                'value2' => $warehouse->to_date,
                'pluginOptions' => [
                    'autoclose'=>true,
                    'format' => 'yyyy-mm-dd'
                ]
            ]);
            ?>
            </div>
            <?php } ?>
            <button type="submit" class="btn btn-primary btn-sm"><?= Yii::t('app', 'Szukaj') ?></button>
            <?php echo Html::endForm(); ?>
        </div>
        </div>
        
    </div>
    <div class="tools warehouse-tools col-md-6">
    <div class="ibox" style="padding-top:10px;">
    <?php 
    $warehouses = \common\models\Warehouse::find()->all();
    if (count($warehouses)>2){
            if (!$w)
            $style = "btn-primary";
        else
            $style = "btn-default";
        echo Html::a("Magazyn łącznie", ['index', 'c'=>Yii::$app->request->get('c', false), 's'=>Yii::$app->request->get('s', null), 'q'=>Yii::$app->request->get('q', null)], ['class'=>'btn  '.$style])." ";
    if (count($warehouses)>6)
    {
        foreach ($warehouses as $ware)
    {

        if ($w==$ware->id){
            $warehouseCurrent = $ware;
        }
    }
        $url = Url::to(['warehouse', 'c'=>Yii::$app->request->get('c', false), 's'=>Yii::$app->request->get('s', null), 'q'=>Yii::$app->request->get('q', null)]);
            echo Html::dropDownList('ware', $w, \common\helpers\ArrayHelper::map($warehouses, 'id', 'name'), ['class'=>'form-control form-inline', 'style'=>'width:auto; display:inline;', 'prompt' => 'Wybierz...', 'id'=>'warehouseChooser']);
            $this->registerJs("
                    $('#warehouseChooser').change(function(e)
                    {
                            location.href = '".$url."&w='+$(this).val();
                    });
                ");
    }else{
    foreach ($warehouses as $ware)
    {

        if ($w==$ware->id){
            $style = "btn-primary";
            $warehouseCurrent = $ware;
        }
        else
            $style = "btn-default";
        echo Html::a($ware->name, ['warehouse', 'w'=>$ware->id, 'c'=>Yii::$app->request->get('c', false), 's'=>Yii::$app->request->get('s', null), 'q'=>Yii::$app->request->get('q', null)], ['class'=>'btn  '.$style])." ";
    } } }?>
    </div>
    </div>
</div>
<?php if ($user->can('gearWarehouseOutcomes')){
    if (count($warehouses)>2){ if ($w){

echo Html::a('<i class="fa fa-shopping-cart"></i> Przesunięcie magazynowe <span class="label label-warning float-right gear-movement-label">'.$warehouseCurrent->getMovement().'</span>', '#', ['class'=>'btn btn-primary open-gear-movement-list']);
            } } }?>


