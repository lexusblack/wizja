<?php

use kartik\widgets\Select2;
use yii\bootstrap\Html;
use yii\helpers\Url;
use yii\widgets\ActiveForm;
use yii\bootstrap\Modal;

$this->title = Yii::t('app', 'Nowa inwentaryzacja');
$this->params['breadcrumbs'][] = ['label' => Yii::t('app', 'Inwentaryzacje'), 'url' => ['stocktakings']];
$this->params['breadcrumbs'][] = $this->title;
?>
<script src="https://unpkg.com/sweetalert/dist/sweetalert.min.js"></script>
<div class="outcomes-warehouse-create">
<div class="row">
<div class="ibox">
<div class="ibox-content">

    <h1><?= Html::encode($this->title) ?></h1>

    <div class="outcomes-warehouse-form">

        <?php $form = ActiveForm::begin(['id' => 'dynamic-form']);

        echo   $form->field($model, 'items')->hiddenInput()->label(false);

        echo $form->field($model, 'gear')->textInput(['placeholder' => Yii::t('app', 'Szukaj sprzętu'), 'id' => 'code-input'])->label(Yii::t('app', "Wprowadź kod sprzętu"));
         ?>

    </div>
    <div class="panel_mid_blocks">
        <div class="panel_block">
            <table class="kv-grid-table table kv-table-wrap" id="stocktaking-table">
                <tr>
                    <th style="width: 70px;"><?= Yii::t('app', 'Id') ?></th>
                    <th><?= Yii::t('app', 'L. zeskanowane') ?></th>
                    <th><?= Yii::t('app', 'L. w magazynie') ?></th>
                    <th><?= Yii::t('app', 'Nazwa') ?></th>
                    <th><?= Yii::t('app', 'Numery') ?></th>
                    
                    <th></th>
                </tr>
            </table>
        </div>
    </div>

        <div class="form-group">
            <?= Html::submitButton(Yii::t('app', 'Inwentaryzuj') , ['class' => 'btn btn-success category-menu-link']) ?>
        </div>



<?php ActiveForm::end(); ?>
</div>
</div>
</div>
</div>

<?php 

$this->registerJs('
    var items = [];
    $("#dynamic-form").on("beforeSubmit", function(e){
        $("#stocktakingform-items").val(JSON.stringify(items));
        $.ajax({
                type: "POST",
                url: "' . Url::to(['warehouse/make-stocktaking']) . '",
                data: $("#dynamic-form").serialize(),
                async: false,
                success: function(data){
                    if (data.ok)
                    {
                        window.location.href = "/admin/warehouse/stocktaking-report?id="+data.id;
                    }else{
                        alert(data.error);
                    }
                    
                }    
            });
        return false;
    });

    $("#code-input")[0].addEventListener("keydown", function(event) {
        if (event.keyCode === 13) {
            event.preventDefault();
        var value = $("#code-input").val();
        if (value.length == 13) {
            if (value.length == 13) {
            
                var url = "' . Url::to(["warehouse/get-gear-by-code"]) . '?q=" + value;
            
                $.ajax({
                    url: url,
                    type: "post",
                    async: false,
                    success: function(data) {
                        if (data.error) {
                            
                        }
                        if (data.ok) {       
                                addItems(data);
                        }
                    },
                    error: function(data) {
                            
                    }
                });
                $("#code-input").val(null);
            }
            return false;
        }
        }
    });

    function addItems(data)
    {
        var modelRow = $(".gear-row[data-gearid=\'"+data.gear.id+"\']");
        if (modelRow.length == 0) {
            addGearRow(data.gear, data.total);
        }
        modelRow = $(".gear-row[data-gearid=\'"+data.gear.id+"\']");
        if (data.no_items)
        {
            numberTd =  parseInt(modelRow.find("td:nth-child(2)").html());
            swal({
                              text: "Podaj liczbę sztuk "+data.gear.name+". Dotychczas zeskanowano: "+numberTd,
                              content: {
                                element: "input",
                                attributes: {
                                  placeholder: "Podaj wartość",
                                  type: "number",
                                  value:0
                                }
                            },
                              button: {
                                text: "OK",
                                closeModal: true,
                              },
                            })
                            .then(name => {
                                number = name;
                                if (number != null) {
                                    numberTd+=parseInt(number);
                                    modelRow.find("td:nth-child(2)").html(numberTd);
                                    items[data.items[0].id] = numberTd;
                                }
                            });
        }else{
            for (i=0; i<data.items.length; i++)
            {
                if ((typeof items[data.items[i].id] === "undefined")||(items[data.items[i].id] === null))
                {
                    items[data.items[i].id] = 1;
                    $(".number-list-model-" + data.gear.id).append("<span class=\'item-in-basket number-list-gear-"+data.items[i].id+"\'>"+data.items[i].number+", </span>");
                    numberTd =  parseInt(modelRow.find("td:nth-child(2)").html());
                    numberTd++;
                    modelRow.find("td:nth-child(2)").html(numberTd);

                }else{
                    toastr.error("Egzemplarz "+data.gear.name+" nr "+data.items[i].number+" już został zeskanowany");
                }
            }
        }
    }

    function addGearRow(gear, total)
    {
        var new_row =   "<tr class=\'gear-row\' data-gearid=\'"+gear.id+"\' style=\'cursor:pointer;\' >" +
                        "<td>"+gear.id+"</td>" +
                        "<td>0</td>"+
                        "<td>"+total+"</td>"+
                        "<td>"+gear.name+"</td>"+
                        "<td class=\'number-list-model-"+gear.id+"\'></td>"+
                        "<td><span class=\'remove_model glyphicon glyphicon-remove\' style=\'cursor:pointer;\' data-gearid=\'"+gear.id+"\'></span></td>"+
                    "</tr>";

    if ($("#stocktaking-table tbody").length === 0) {
        $("#stocktaking-table").append("<tbody></tbody>");
    }
    $("#stocktaking-table tbody").each(function(index){
        if (index === 0) {
            $(this).append(new_row);
        }
    });
    }
');
