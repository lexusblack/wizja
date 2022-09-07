<?php

use yii\helpers\Html;
use common\components\grid\GridView;

/* @var $this yii\web\View */
/* @var $searchModel common\models\TaskSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = Yii::t('app', 'Blend Calculator');
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="calculator-index">
 <form id="mainForm">
        <div class="container-fluid">
            <div class="col-md-12">
                <h1 class="text-primary">
                    <i class="glyphicon glyphicon-blackboard"></i> <?=Yii::t('app', 'Blend kalkulator');?>


                    <select name="" id="BlendSelect" class="form-control" style="display: inline-block; width: 300px; margin-left: 14px;" onchange="getProjectData(this.value); changeDeleteURL(this.value);">
                            <option value=""><?=Yii::t('app', 'Wybierz');?></option>
                            <?php foreach ($dataProvider->getModels() as $c) {
                                echo "<option value='".$c->id."'>".$c->name."</option>";
                                }?>
                        </select>

                </h1>
            </div>
            <div class="row blend-calculator">
                <div id="blend-app" class="col-md-5">

                    <div class="pull-right" style="position: fixed; top: 150px; right: 32px; z-index: 10000;">

                        <a class="btn btn-success" id="buttonSave" v-on:click="saveData"><?=Yii::t('app', 'Zapisz');?></a>
                        <a class="btn btn-success" id="buttonPrintPDF" v-on:click="printPdf"><?=Yii::t('app', 'PDF');?></a>
                        <a class="btn btn-success" id="buttonNet" v-on:click="printNet"><?=Yii::t('app', 'Generuj siatkę');?></a>
                        <?= Html::a('<i class="fa fa-trash"></i> ' . Yii::t('app', 'Usuń'), ['index'], [
                                'class' => 'btn btn-danger',
                                'id' => 'deleteCalculator',
                                'data' => [
                                    'confirm' => Yii::t('app', 'Na pewno chcesz usunąć?'),
                                    'method' => 'post',
                                ],
                            ]) ?>
                    </div>
                    <div class="panel panel-default" v-bind:class="{ 'panel-danger': correct }">
                        <div class="panel-heading">
                            <!--<h3 class="panel-title">-->

                            <strong><span class="xpanel-title text-primary"><?=Yii::t('app', 'Blend calculator');?></span></strong>
                            <!--</h3>-->
                        </div>
                        <div class="panel-body">
                                                    <strong>
                            <span v-if="correct" class="pull-right text-danger" style="font-size: 11px;">
                                <?=Yii::t('app', 'Rozdzielczość pionowa większa od natywnej projektora. Zmniejsz liczbę projektorów lub zwiększ zakładkę.');?>
                                
                            </span>
                            <span v-else class="pull-right text-success">
                                <?=Yii::t('app', 'Rozdzielczość ok');?>
                                
                            </span>
                            </strong>
                            <div class="form-group row">
                                <label for="projectName" class="col-xs-3 control-label"><?=Yii::t('app', 'Nazwa projektu');?>:</label>
                                <div class="col-xs-4">
                                    <input type="text" id="projectName" v-model="projectName" class="form-control" v-on:keyup="update" v-on:mouseup="update"
                                    />
                                </div>
                            </div>
                            <div class="form-group row">
                                <label class="col-xs-3"><?=Yii::t('app', 'Wielkość ekranu (m)');?>:</label>
                                <div class="col-xs-2">
                                    <input type="number" id="screenW" v-model="screenSize.w" class="form-control numeric" step="1" min="0" v-on:keyup="update"
                                        v-on:mouseup="update" />
                                </div>
                                <div class="col-xs-2">
                                    <input type="number" id="screenH" v-model="screenSize.h" class="form-control numeric" step="1" min="0" v-on:keyup="update"
                                        v-on:mouseup="update" />
                                </div>
                                <div class="col-xs-5">
                                    <label><?=Yii::t('app', 'Ilość zakładek');?>: {{ overlapsCount }}</label>
                                </div>
                            </div>
                            <div class="form-group row">
                                <label class="col-xs-3"><?=Yii::t('app', 'Ilość projektorów (szt)');?>:</label>
                                <div class="col-xs-4">
                                    <input type="number" id="projCount" v-model="projCount" class="form-control numeric" step="1" min="1" v-on:keyup="update"
                                        v-on:mouseup="update" />
                                </div>
                                <div class="col-xs-5">
                                    <label><?=Yii::t('app', 'Aspekt ekranu');?>: {{ projRatio }}</label>
                                </div>
                            </div>
                            <div class="form-group row">
                                <label class="col-xs-3"><?=Yii::t('app', 'Jasność projektora (lumen)');?></label>
                                <div class="col-xs-4">
                                    <input type="number" id="projLuminosity" v-model="projLuminosity" class="form-control numeric" step="1" min="0" v-on:keyup="update"
                                        v-on:mouseup="update" />
                                </div>
                                <div class="col-xs-5">
                                    <label><?=Yii::t('app', 'Rozdzielczość zakładki');?>: {{ overlapRes }}</label>
                                </div>
                            </div>
                            <div class="form-group row">
                                <label class="col-xs-3"><?=Yii::t('app', 'Rozdzielczość projektora (px)');?>:</label>
                                <div class="col-xs-3">
                                    <input type="number" id="projResW" v-model="projRes.w" class="form-control numeric" step="1" min="0" v-on:keyup="update"
                                        v-on:mouseup="update" />
                                </div>
                                <div class="col-xs-3">
                                    <input type="number" id="projResH" v-model="projRes.h" class="form-control numeric" step="1" min="0" v-on:keyup="update"
                                        v-on:mouseup="update" />
                                </div>
                                <div class="col-xs-3">
                                    <label><?=Yii::t('app', 'Rozdzielczość projektora (px)');?>: {{ screenRes.w }} x {{ screenRes.h }}</label>
                                </div>
                            </div>
                            <div class="form-group row">
                                <label class="col-xs-3"><?=Yii::t('app', 'Wielkość zakładki');?> (%):</label>
                                <div class="col-xs-4">
                                    <input type="number" id="overlapSize" v-model="overlapSize" class="form-control numeric" step="0.1" min="0" v-on:keyup="update"
                                        v-on:mouseup="update" />
                                </div>
                                <div class="col-xs-5">
                                    <label><?=Yii::t('app', 'Całkowita wielkość zakładki');?> (m): {{ totalOverlapSize }}</label>
                                </div>
                            </div>
                            <div class="form-group row">
                                <label class="col-xs-3"><?=Yii::t('app', 'Ogniskowa obiektywu');?> (%):</label>
                                <div class="col-xs-4">
                                    <input type="number" id="focalLength" v-model="focalLength" class="form-control numeric" step="0.01" min="0" v-on:keyup="update"
                                        v-on:mouseup="update" />
                                </div>
                                <div class="col-xs-5">
                                    <label><?=Yii::t('app', 'Pow. projekcji jednego projektora');?> (m): {{ projArea.w }} x {{ projArea.h }}</label>
                                </div>
                            </div>
                            <hr />
                            <div class="row">
                                <label class="col-md-7">
                                <?=Yii::t('app', 'Odległość świecenia');?>: {{ lightDistance }}
                            </label>
                                <label class="col-md-5">
                                <?=Yii::t('app', 'Jasność (lumen na m2)');?>: {{ luminosity }}
                            </label>
                            </div>

                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-5">
                            <div class="panel panel-default">
                                <div class="panel-heading">
                                    <strong><span class="text-primary"><?=Yii::t('app', 'Projektory');?></span></strong>
                                </div>
                                <div class="panel-body">
                                    <div class="row">
                                        <div class="col-xs-8">
                                            <label><?=Yii::t('app', 'Odległość między projektorami');?>:</label>
                                        </div>
                                        <div class="col-xs-4">
                                            {{ projDistance }} <?= Yii::t('app', 'cm') ?>
                                        </div>
                                    </div>
                                    <hr />
                                    <div class="row">
                                        <label class="col-md-12"><?=Yii::t('app', 'Odległości projektorów od krawędzi ekranu w');?> <?= Yii::t('app', 'cm') ?>:</label>
                                    </div>
                                    <div class="row" v-for="(item, index) in projOffsets">
                                        <div class="col-xs-8">
                                            <?=Yii::t('app', 'Projektor');?> {{ index + 1 }}
                                        </div>
                                        <div class="col-xs-4">
                                            {{ item }} <?= Yii::t('app', 'cm') ?>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-7">
                            <div class="panel panel-default">
                                <div class="panel-heading">
                                    <strong><span class="text-primary"><?=Yii::t('app', 'Stage simulation');?></span></strong>
                                </div>
                                <div class="panel-body">

                                    <div class="form-group row">
                                        <label class="col-xs-8"><?=Yii::t('app', 'Wysokość na której wisi projektor (obiektyw)');?> (<?= Yii::t('app', 'm') ?>:</label>
                                        <div class="col-xs-4">
                                            <input type="number" id="projHangHeight" v-model="projHangHeight" class="form-control numeric" step="1" min="0" v-on:keyup="update"
                                                v-on:mouseup="update" />
                                        </div>
                                    </div>
                                    <div class="form-group row">
                                        <label class="col-xs-8"><?=Yii::t('app', 'Wysokość sceny');?> (<?= Yii::t('app', 'm') ?>:</label>
                                        <div class="col-xs-4">
                                            <input type="number" id="sceneHeight" v-model="sceneHeight" class="form-control numeric" step="1" min="0" v-on:keyup="update"
                                                v-on:mouseup="update" />
                                        </div>
                                    </div>
                                    <div class="form-group row">
                                        <label class="col-xs-8"><?=Yii::t('app', 'Odległośc ekranu od sceny');?> (<?= Yii::t('app', 'm') ?>:</label>
                                        <div class="col-xs-4">
                                            <input type="number" id="screenSceneDistance" v-model="screenSceneDistance" class="form-control numeric" step="1" min="0"
                                                v-on:keyup="update" v-on:mouseup="update" />
                                        </div>
                                    </div>
                                    <div class="form-group row">
                                        <label class="col-xs-8"><?=Yii::t('app', 'Wzrost człowieka');?>:</label>
                                        <div class="col-xs-4">
                                            <input type="number" id="personHeight" v-model="personHeight" class="form-control numeric" step="0.01" min="0" v-on:keyup="update"
                                                v-on:mouseup="update" />
                                        </div>
                                    </div>
                                    <div class="form-group row">
                                        <label class="col-xs-9"><?=Yii::t('app', 'odległość przecięcia się obrazu z człowiekiem stojącym na scenie');?> (<?= Yii::t('app', 'm') ?>:</label>
                                        <div class="col-xs-3">
                                            {{ headInterDist }}
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-md-7">
                    <div class="panel panel-default">
                        <div class="panel-heading">
                            <strong><span class="xpanel-title text-primary"><?= Yii::t('app', 'Symulacja blendu') ?></span></strong>
                        </div>
                        <div class="panel-body canvas-container" id="cvBlendSimulationContainer">
                            <canvas id="cvBlendSimulation"></canvas>
                        </div>
                    </div>
                    <div class="panel panel-default">
                        <div class="panel-heading">
                            <strong><span class="xpanel-title text-primary"><?= Yii::t('app', 'Symulacja dystansu') ?></span></strong>
                        </div>
                        <div class="panel-body canvas-container" id="cvDistanceSimulationContainer">
                            <canvas id="cvDistanceSimulation"></canvas>
                        </div>
                    </div>
                    <div class="panel panel-default">
                        <div class="panel-heading">
                            <strong><span class="xpanel-title text-primary"><?= Yii::t('app', 'Symulacja sceny') ?></span></strong>
                        </div>
                        <div class="panel-body canvas-container" id="cvStageSimulationContainer">
                            <canvas id="cvStageSimulation"></canvas>
                        </div>
                    </div>
                </div>
            </div>
            <div class="net-canvas hidden" style="display: none !important;">
                <canvas id="cvNet"></canvas>
            </div>
        </div>
    </form>
    <script>
        var blend;
        function changeDeleteURL(id){
            if (id!=""){
                $("#deleteCalculator").attr('href', '/admin/calculator/delete?id='+id); 
            }else{
                $("#deleteCalculator").attr('href', ''); 
            }
        }

        function getProjectData(id) {
            if (id!="")
            {
             $.ajax({
                type: 'POST',
                url: '<?=Yii::$app->getUrlManager()->createUrl('calculator/getconfig')?>?id='+id, 
                success: function (data) {
                    blend.id = id;
                    data = jQuery.parseJSON(data);
                    blend.projectName =data.name;
                    blend.projCount = data.projCount;
                    blend.screenSize = { w: data.screenW, h: data.screenH};
                    blend.projLuminosity = data.projLuminosity;
                    blend.projRes = { w: data.projResW, h: data.projResH};
                    blend.overlapSize = data.overlapSize;
                    blend.focalLength = data.focalLength;
                    blend.projHangHeight = data.projHangHeight;
                    blend.sceneHeight = data.sceneHeight;
                    blend.screenSceneDistance = data.screenSceneDistance;
                    blend.personHeight = data.personHeight;
                    blend.doUpdate();
                },
                error: function (exception) {
                    toastr.error("<?= Yii::t('app', "Błąd!") ?>");
                }
            });               
            }else{
                blend.id = "";
            }
        }
        function saveProjectData() {
            data = {
                "id": blend.id,
                "name": blend.projectName,
                "screenW": blend.screenSize.w,
                "screenH": blend.screenSize.h,
                "projCount": blend.projCount,
                "projLuminosity": blend.projLuminosity,
                "projResW": 1920,
                "projResH": 1200,
                "overlapSize": blend.overlapSize,
                "focalLength": blend.focalLength,
                "projHangHeight": blend.projHangHeight,
                "sceneHeight": blend.sceneHeight ,
                "screenSceneDistance": blend.screenSceneDistance,
                "personHeight": blend.personHeight
            };
            $.ajax({
                type: 'POST',
                url: '<?=Yii::$app->getUrlManager()->createUrl('calculator/saveconfig')?>',
                data:{data:data},
                success: function (data) {
                    data = jQuery.parseJSON(data);
                    toastr.success(data.message);
                },
                error: function (exception) {
                    toastr.error("<?= Yii::t('app', "Błąd!") ?>");
                }
            }); }
    </script>
        <?=$this->registerJs("
            blend = new Vue({
                el: '#blend-app',
                data: {
                    id : '',
                    overlapsCount: null,
                    screenRes: {},
                    focalLength: null,
                    projRatio: null,
                    overlapRes: null,
                    screenRatio: null,
                    pixelPerCm: null,
                    totalOverlapSize: null,
                    projArea: {},
                    lightDistance: null,
                    luminosity: null,
                    projOffsets: []
                    // ,
                    // projHangHeight: null
                    // ,
                    // sceneHeight: null,
                    // screenSceneDistance: null,
                    // personHeight: null
                },
                created: function () {
                    this.init();
                },
                methods: {
                    init: function () {
                        this.projectName = 'test';
                        this.screenSize = { w: 20, h: 4 };
                        this.projCount = 4;
                        this.projLuminosity = 20000;
                        this.projRes = { w: 1920, h: 1200 };
                        this.overlapSize = 29.2;
                        this.focalLength = 0.67;

                        this.projHangHeight = 4;
                        this.sceneHeight = 0.5;
                        this.screenSceneDistance = 3;
                        this.personHeight = 1.8;
                        this.doUpdate();
                    },
                    recalculate: function () {
                        this.overlapsCount = this.projCount - 1;

                        this.projRatio = this.projRes.w / this.projRes.h;
                        this.screenRatio = this.screenSize.w / this.screenSize.h;

                        this.overlapRes = parseFloat(this.projRes.w * this.overlapSize / 100);
                        this.screenRes.w = Math.round(this.projRes.w * this.projCount - (this.projCount - 1) * this.overlapRes);
                        this.screenRes.h = Math.round(this.screenRes.w / this.screenRatio);

                        this.pixelPerCm = this.screenRes.w / (this.screenSize.w * 100);
                        // console.log('or: ' + this.overlapRes + ' pixelPerCm: ' + this.pixelPerCm);
                        this.totalOverlapSize = Math.round(this.overlapRes / this.pixelPerCm);
                        // console.log('tos: ' + this.totalOverlapSize);
                        // console.log(this.projRes.w + ' ' + this.overlapRes + ' ' + this.pixelPerCm + ' ' + this.totalOverlapSize);

                        this.projArea.w = Math.round((this.projRes.w - this.overlapRes) / this.pixelPerCm + this.totalOverlapSize);
                        this.projArea.h = this.projArea.w / this.projRatio;

                        this.lightDistance = parseFloat(this.projArea.w * this.focalLength).toFixed(2);
                        this.luminosity = parseFloat(this.projLuminosity / ((this.projArea.w / 100) * (this.projArea.h / 100))).toFixed(2);

                        this.projOffsets = [];

                        if (this.projCount > 0) {
                            this.projOffsets[0] = this.projArea.w / 2;
                        }

                        if (this.projCount > 1) {
                            this.projOffsets[1] = this.projArea.w - this.totalOverlapSize + this.projOffsets[0];
                        }
                        if (this.projCount > 2) {
                            for (var i = 2; i < this.projCount; i++) {
                                this.projOffsets[i] = this.projOffsets[i - 1] + this.projOffsets[0] - this.totalOverlapSize + this.projOffsets[0];
                            }
                        }

                        this.projDistance = this.projOffsets[0] * 2 - this.totalOverlapSize;
                        // console.log(this.lightDistance + ' ' + this.personHeight + ' ' + this.sceneHeight + ' ' + this.screenSceneDistance);
						var lightDistanceMeter = this.lightDistance/100;
                        //this.headInterDist = parseFloat((lightDistanceMeter * (parseFloat(this.personHeight) + parseFloat(this.sceneHeight)) - this.screenSceneDistance) / lightDistanceMeter).toFixed(2);
						
						this.headInterDist = 0;
						var screenYPos = (parseFloat(this.screenSceneDistance) + parseFloat(this.sceneHeight)); // wysokość na jakiej znajduje się dół ekranu (z wysokością sceny)
						var headYPos = (parseFloat(this.personHeight) + parseFloat(this.sceneHeight)); // wysokość na jakiej znajduje się dół ekranu (z wysokością sceny)
						var projScreenDiff = (parseFloat(this.projHangHeight) - screenYPos ); // różica wysokości między obiektywem a dołem ekranu
							
						if( screenYPos < parseFloat(this.projHangHeight) && screenYPos < headYPos )
						{
							var screenHeadDiff = ( headYPos - screenYPos ); // różnica wysokości między czubkiem głowy a dołem ekranu
							var percentDistance = screenHeadDiff / projScreenDiff; // odległość procentowa od dolnego krańca erkanu do głowy, gdy czubek głowy dotyka promieni
							var headScreenDistance = percentDistance * Math.sqrt( lightDistanceMeter*lightDistanceMeter + projScreenDiff*projScreenDiff ) // odległość od dolnego krańca erkanu do głowy, gdy czubek głowy dotyka promieni
							this.headInterDist = Math.sqrt( headScreenDistance*headScreenDistance - screenHeadDiff*screenHeadDiff );
							this.headInterDist = this.headInterDist.toFixed(2);
						}						
						if( screenYPos > parseFloat(this.projHangHeight) && parseFloat(this.projHangHeight) < headYPos )
						{
							var projHeadDiff = ( headYPos - parseFloat(this.projHangHeight) ); // różnica wysokości między czubkiem głowy a obiektywem
							var percentDistance = projHeadDiff / projScreenDiff; // odległość procentowa od obiektywu do głowy, gdy czubek głowy dotyka promieni
							var headScreenDistance = percentDistance * Math.sqrt( lightDistanceMeter*lightDistanceMeter + projScreenDiff*projScreenDiff ) // odległość od obiektywu erkanu do głowy, gdy czubek głowy dotyka promieni
							this.headInterDist = Math.sqrt( headScreenDistance*headScreenDistance - projHeadDiff*projHeadDiff );
							this.headInterDist = this.headInterDist.toFixed(2);
						}


                        if(isNaN(this.headInterDist)){
                            this.headInterDist = 0;
                        }

                        this.correct = (this.screenRes.h > this.projRes.h);// ? 'roz. pion. większa od natywnej projektora!!' : 'Rfozdzielczość ok';
                        this.verified = this.verify();
                    },
                    update: function () {
                        if (window.globalTimer != undefined)
                            clearTimeout(window.globalTimer);
                        var self = this;
                        window.globalTimer = setTimeout(function () {
                            self.doUpdate();
                        }, 600);
                        //todo
                        //this.printNet();
                    },
                    doUpdate: function () {
                        this.recalculate();
                        this.renderBlendSimulation();
                        this.renderDistanceSimulation();
                        this.renderStageSimulation();
                    },
                    drawRect: function (ctx, x, y, w, h, fillColor, borderColor, lineWidth) {
                        ctx.fillStyle = fillColor;
                        ctx.strokeStyle = borderColor;
                        ctx.lineWidth = lineWidth;
                        ctx.fillRect(x, y, w, h);
                        ctx.strokeRect(x, y, w, h);
                    },
                    drawText: function (ctx, x, y, text, color, font, textAlign, baseLine) {
                        ctx.font = font;
                        ctx.textBaseline = baseLine || 'middle';
                        ctx.textAlign = textAlign || 'center';
                        ctx.fillStyle = color;
                        ctx.fillText(text, x, y);
                    },
                    drawLine: function (ctx, x1, y1, x2, y2, color) {
                        if (color != null)
                            ctx.strokeStyle = color;
                        ctx.beginPath();
                        ctx.moveTo(x1, y1);
                        ctx.lineTo(x2, y2);
                        ctx.stroke();
                    },
                    drawCircle: function (ctx, x, y, r, fillColor, borderColor, lineWidth) {
                        ctx.beginPath();
                        ctx.arc(x, y, r, 0, 2 * Math.PI, false);
                        ctx.fillStyle = fillColor || '#000';
                        ctx.fill();
                        ctx.lineWidth = lineWidth || 1;
                        ctx.strokeStyle = borderColor;
                        ctx.stroke();
                    },
                    verify: function () {
                        var screenResH = Math.round(this.screenRes.h);
                        var projResH = Math.round(this.projRes.h);
                        if (screenResH > projResH) {
                            return 1;
                        } else if (screenResH < projResH) {
                            return -1;
                        } else {
                            return 0;
                        }
                    },
                    renderBlendSimulation: function () {
                        var canvas = document.getElementById('cvBlendSimulation'), ctx = canvas.getContext('2d');
                        var canvasHeight = 200;

                        var canvasWidth = $('#cvBlendSimulationContainer').width() - 10;//canvasHeight * this.screenRatio;
                        canvas.width = canvasWidth;
                        canvas.height = canvasHeight;//$('#cvBlendSimulationContainer').height();
                        var w = canvas.width, h = canvas.height;

                        var scale = w / (this.screenSize.w * 100);
                        var scaleX = 1;
                        var scaleY = 1;

                        var rectW = this.projArea.w * scale * scaleX;
                        var projDist = this.projDistance * scale * scaleX;

                        var realW = (this.projCount - 1) * projDist + rectW;

                        ctx.clearRect(0, 0, w, h);
                        this.drawRect(ctx, 0, 0, w, h, '#eee', '#aaa', 1);

                        // ekrany
                        for (var i = 0; i < this.projCount; i++) {
                            this.drawRect(ctx, i * projDist, 0, rectW, h, 'rgba(180, 180, 180, 0.4)', '#aaa', 2);
                        }

                        // labele od zakładek
                        for (var i = 0; i < this.projCount - 1; i++) {
                            this.drawText(ctx, i * projDist + (rectW / 2) + (projDist / 2), h / 2, this.overlapSize + '%', '#000', 'bold 14px Helvetica');
                            this.drawText(ctx, i * projDist + (rectW / 2) + (projDist / 2), 20, this.totalOverlapSize + ' cm', '#000', 'bold 12px Helvetica');
                        }

                        // kontrola błędu
                        if (this.verified == 1) {
                            this.drawRect(ctx, 0, 0, w, h, 'rgba(0, 0, 0, 0)', '#f00', 3);
                        }
                    },
                    getProjCoords: function (x, y, w, h) {
                        var projW = w / 5, projH = h / 4;
                        var mx = x + (w / 2), my = h - projH;
                        return {
                            x1: mx - (projW / 2),
                            x2: mx - (projW / 2) + projW,
                            y1: my,
                            y2: my + projH,
                            w: projW,
                            h: projH,
                            mx: mx,
                            my: my,
                            centerX: mx,
                            centerY: h - (projH / 2)
                        }
                    },
                    drawProj: function (ctx, x, y, w, h) {
                        var coords = this.getProjCoords(x, y, w, h);
                        this.drawRect(ctx, coords.x1, coords.y1, coords.w, coords.h, '#fff', '#333', 1);
                        this.drawLine(ctx, coords.mx, coords.my, x, y);
                        this.drawLine(ctx, coords.mx, coords.my, x + w, y);
                    },
                    renderDistanceSimulation: function () {
                        var canvas = document.getElementById('cvDistanceSimulation'),
                            ctx = canvas.getContext('2d');

                        var canvasHeight = 200;
                        var canvasWidth = $('#cvDistanceSimulationContainer').width() - 10;//canvasHeight * this.screenRatio;
                        canvas.width = canvasWidth;
                        canvas.height = 300;//$('#cvBlendSimulationContainer').height();

                        var w = canvas.width, h = canvas.height, topPadding = 5;

                        var scale = w / (this.screenSize.w * 100);
                        var scaleX = 1;
                        var scaleY = 1;

                        var rectW = this.projArea.w * scale * scaleX;
                        var projDist = this.projDistance * scale * scaleX;

                        var realW = (this.projCount - 1) * projDist + rectW;

                        ctx.clearRect(0, 0, w, h);

                        // projektory
                        for (var i = 0; i < this.projCount; i++) {
                            this.drawProj(ctx, i * projDist, topPadding, rectW, h);
                        }

                        var linePadding = 20;
                        // ekran z góry
                        this.drawLine(ctx, 0, topPadding + linePadding, 0, h - (h / 4) - linePadding, '#000');
                        this.drawText(ctx, 10, (topPadding + h - (h / 4)) / 2, this.lightDistance + ' cm', '#000', '12px Helvetica', 'left');

                        for (var i = 0; i < this.projCount + 1; i++) {
                            // var offset = this.projOffsets[i] * scale;

                            if (i == 0) {
                                var coords = this.getProjCoords(i * projDist, topPadding, rectW, h);
                                this.drawLine(ctx, linePadding, coords.centerY, coords.x1 - linePadding, coords.centerY);
                                this.drawText(ctx, coords.x1 / 2, coords.centerY + 10, this.projOffsets[0] + ' cm', '#000', '12px Helvetica');
                            }
                            else if (i == this.projCount) {
                                var coords = this.getProjCoords((i - 1) * projDist, topPadding, rectW, h);
                                this.drawLine(ctx, coords.x2 + linePadding, coords.centerY, w - linePadding, coords.centerY);
                                this.drawText(ctx, (coords.x2 + w) / 2, coords.centerY + 10, this.projOffsets[0] + ' cm', '#000', '12px Helvetica');
                            }
                            else {
                                var coords1 = this.getProjCoords((i - 1) * projDist, topPadding, rectW, h);
                                var coords2 = this.getProjCoords(i * projDist, topPadding, rectW, h);

                                this.drawLine(ctx, coords1.x2 + linePadding, coords1.centerY, coords2.x1 - linePadding, coords1.centerY);
                                this.drawText(ctx, (coords1.x2 + coords2.x1) / 2, coords1.centerY + 10, this.projDistance + ' cm', '#000', '12px Helvetica');
                            }
                        }

                        // ekran z góry
                        if (this.verified == 1) {
                            this.drawRect(ctx, 0, 0, canvasWidth, topPadding, '#f00', '#ccc');
                        } else {
                            this.drawRect(ctx, 0, 0, canvasWidth, topPadding, '#ccc', '#ccc');
                        }
                        // kontrola błędu
                        // if (this.verify(realW, canvasWidth) != 0) {
                        //     this.drawRect(ctx, 0, 0, canvasWidth, h, 'rgba(0, 0, 0, 0)', '#f00', 3);
                        // }
                    },
                    renderStageSimulation: function () {
                        var canvas = document.getElementById('cvStageSimulation'),
                            ctx = canvas.getContext('2d');

						var divWidth = document.getElementById('cvStageSimulationContainer').offsetWidth * 2*document.getElementById('cvStageSimulationContainer').offsetLeft;
                        var canvasHeight = 220;
                        var canvasWidth = 500;

                        canvas.width = canvasWidth;// $('#cvBlendSimulationContainer').width();
                        canvas.height = canvasHeight;//200;//$('#cvBlendSimulationContainer').height();
                        var w = canvas.width, h = canvas.height;

                        ctx.clearRect(0, 0, w, h);
                        var padding = 20;
                        // projektor
                        var projX = w - 60, projY = 0, projW = 60, projH = 40;
						var headLineText = 'nigdy nie przecina';
						if(this.headInterDist > 0)
							headLineText = this.headInterDist + ' m'
						if(parseFloat(this.screenSceneDistance) + parseFloat(this.sceneHeight) == this.projHangHeight)
						{
							if( parseFloat(this.projHangHeight) < parseFloat(this.personHeight) + parseFloat(this.sceneHeight) )
							{
								this.headInterDist = 1;
								headLineText = 'zawsze przecina';
							}
							projY = 100;
						}
						if(parseFloat(this.screenSceneDistance) + parseFloat(this.sceneHeight) > this.projHangHeight) projY = 170;
                        this.drawRect(ctx, projX, projY, projW, projH, '#fff', '#333');

                        // odleglość projektora od ziemi
                        this.drawLine(ctx, w, 0, w, h, '#000');
                        if (this.projHangHeight != null) {
                            this.drawText(ctx, w - 10, projH + ((h - projH) / 2), this.projHangHeight + ' m', '#000', 'bold 12px Helvetica', 'right');
                        }

                        // ekran
                        var screenH = 120, screenW = 7;
                        this.drawRect(ctx, padding, 0, screenW, screenH, '#fff', '#333');

                        // linie od projektora
                        this.drawLine(ctx, projX, projH / 2 + projY, padding, 0);
                        this.drawLine(ctx, projX, projH / 2 + projY, padding + screenW, screenH);

                        // scena
                        var sceneX = padding, sceneY = h - 30, sceneW = 250, sceneH = 30;
                        this.drawRect(ctx, sceneX, sceneY, sceneW, sceneH, '#fff', '#333');
                        this.drawText(ctx, sceneX + (sceneW / 2), sceneY + (sceneH / 2), 'STAGE', '#333', 'bold 14px Helvetica');

                        // odległośc sceny od ekranu
                        this.drawLine(ctx, 10, screenH, 10, sceneY);
                        if (this.screenSceneDistance != null) {
                            this.drawText(ctx, 15, (screenH + sceneY) / 2, this.screenSceneDistance + ' m', '#000', 'bold 12px Helvetica', 'left');
                        }

                        // odległość sceny od projektora (ziemia)
                        this.drawLine(ctx, padding, h, w, h, '#333');

                        // wysokość sceny
                        if (this.sceneHeight != null) {
                            this.drawText(ctx, sceneX + sceneW + 5, sceneY + (sceneH / 2), 'h = ' + this.sceneHeight + ' m', '#000', 'bold 12px Helvetica', 'left');
                        }

                        // linia do głowy
						var personHeight = (sceneY-screenH)*0.6;
						if(this.headInterDist > 0)
						{
							personHeight = (sceneY-screenH)*1.4;
						}
						var lineX = padding + screenW;
						if( parseFloat(this.projHangHeight) < parseFloat(this.screenSceneDistance) + parseFloat(this.sceneHeight) )
						{
							lineX = projX;
							personHeight = (sceneY-screenH)*0.7;
							if(this.headInterDist <= 0)
								personHeight = (sceneY-screenH)*0.5;
						}
							
                        this.drawLine(ctx, lineX + 5, sceneY - personHeight, sceneX + (sceneW / 2) - 5, sceneY - personHeight, '#f00');
                        this.drawText(ctx, (lineX + sceneX + (sceneW / 2)) / 2, sceneY - personHeight-10, headLineText, '#000', 'bold 12px Helvetica');
						
						this.drawCircle(ctx, sceneX + (sceneW / 2), sceneY-personHeight*5/6, personHeight/6, '#fff', '#333', 1);
                        this.drawLine(ctx, sceneX + (sceneW / 2), sceneY-personHeight*4/6, sceneX + (sceneW / 2)-personHeight/6, sceneY-personHeight*3/6, '#333');
                        this.drawLine(ctx, sceneX + (sceneW / 2), sceneY-personHeight*4/6, sceneX + (sceneW / 2)+personHeight/6, sceneY-personHeight*3/6, '#333');
                        this.drawLine(ctx, sceneX + (sceneW / 2), sceneY-personHeight*2/6, sceneX + (sceneW / 2)-personHeight/6, sceneY-personHeight*0/6, '#333');
                        this.drawLine(ctx, sceneX + (sceneW / 2), sceneY-personHeight*2/6, sceneX + (sceneW / 2)+personHeight/6, sceneY-personHeight*0/6, '#333');
                        this.drawLine(ctx, sceneX + (sceneW / 2), sceneY-personHeight*4/6, sceneX + (sceneW / 2), sceneY-personHeight*2/6, '#333');
                    },
                    saveBase64AsFile: function (base64, fileName) {
                        var link = document.createElement('a');

                        link.setAttribute('href', base64);
                        link.setAttribute('download', fileName);
                        link.click();
                    },
                    printNet: function () {
                        var canvas = document.getElementById('cvNet'), ctx = canvas.getContext('2d');
                        var w = this.screenRes.w, h = this.screenRes.h;

                        canvas.width = w;
                        canvas.height = h;
                        ctx.clearRect(0, 0, w, h);

                        var img = new Image();
                        img.setAttribute('crossOrigin', 'anonymous');
                        img.src = './net-bg.jpg';
                        var margin = 0;

                        // czarne to
                        this.drawRect(ctx, 0, 0, w, h, '#000', '#000');

                        // obraz w tle
                        var self = this;
                        img.onload = function () {
                            var imgWidth = (h - margin * 2) * img.width / img.height;
                            var imgHeight = h - margin * 2;

                            for (var w1 = 0; w1 < canvas.width; w1 += imgWidth) {
                                for (var h1 = 0; h1 < canvas.height; h1 += imgHeight) {
                                    ctx.drawImage(img, w1 + margin, h1 + margin, imgWidth, imgHeight);
                                }
                            }
                            self.drawRect(ctx, w - margin, 0, margin, h, '#000', '#000');
                            self.drawRect(ctx, 0, h - margin, w, margin, '#000', '#000');

                            // siatka
                            var netW = w / 20, netH = h / 20;

                            for (var i = 0; i < w / netH; i++) {
                                self.drawLine(ctx, i * netH, 0, i * netH, h, 'rgba(255, 255, 255, 1.0)', 1.0);
                            }

                            for (var i = 0; i < h / netH; i++) {
                                self.drawLine(ctx, 0, i * netH, w, i * netH, 'rgba(255, 255, 255, 1.0)', 1.0);
                            }

                            self.drawText(ctx, 2, 2, '0', '#fff', '10px Helvetica', 'left', 'top');

                            self.drawLine(ctx, 0, 600, w, 600, 'rgba(200, 200, 200, 0.5)', 0.5);
                            self.drawText(ctx, 2, 600, '600', '#fff', '10px Helvetica', 'left', 'bottom');

                            self.drawLine(ctx, 0, 768, w, 768, 'rgba(200, 200, 200, 0.5)', 0.5);
                            self.drawText(ctx, 2, 768, '768', '#fff', '10px Helvetica', 'left', 'bottom');

                            self.drawLine(ctx, 0, 1024, w, 1024, 'rgba(200, 200, 200, 0.5)', 0.5);
                            self.drawText(ctx, 2, 1024, '1024', '#fff', '10px Helvetica', 'left', 'bottom');

                            self.drawLine(ctx, 0, 1200, w, 1200, 'rgba(200, 200, 200, 0.5)', 0.5);
                            self.drawText(ctx, 2, 1200, '1200', '#fff', '10px Helvetica', 'left', 'bottom');

                            var img2 = new Image();
                            img2.setAttribute('crossOrigin', 'anonymous');
                            img2.src = './logo.png';

                            img2.onload = function () {
								var xMargin = img2.width;
								var yMargin = img2.height*2.0;
								
							
								
								var rowCount = parseInt( canvas.height / (img2.height + yMargin) );
								var totalLogoHeight = rowCount*img2.height + (rowCount-1)*yMargin;
								for(var i2=0; i2<rowCount; i2++)
								{
									var y = canvas.height/2 - totalLogoHeight/2 + i2 * (totalLogoHeight+yMargin) / rowCount;
								
									var columnCount = parseInt( canvas.width / (img2.width + xMargin) );
									if(i2%2 == 1)
										columnCount--;
									
									var totalLogoWidth = columnCount*img2.width + (columnCount-1)*xMargin;
									for(var i=0; i<columnCount; i++)
									{
										var x = canvas.width/2 - totalLogoWidth/2 + i * (totalLogoWidth+xMargin) / columnCount;
										ctx.drawImage(img2, x, y, img2.width, img2.height);
									}
								}
								

								
                                var imgData = canvas.toDataURL();
                                // alert(imgData);
                                // console.log(imgData);
                                window.open(imgData, '_blank');
                                // window.open(imgData, 'Etykieta', 'width=900,height=400,location=no');

                            }

                            // console.log(imgData);
                            // alert(imgData);
                            // window.location.href = imgData.replace('image/png', 'image/octet-stream');
                            // self.saveBase64AsFile(imgData, 'test.png');
                        }


                    },
                    printPdf: function () {

                        document.getElementById('buttonSave').style.visibility = 'hidden';
                        document.getElementById('buttonPrintPDF').style.visibility = 'hidden';
                        document.getElementById('buttonNet').style.visibility = 'hidden';
                        html2canvas(document.body, {
                            onrendered: function (canvas) {
                                var imgData = canvas.toDataURL();
                                document.getElementById('buttonSave').style.visibility = 'visible';
                                document.getElementById('buttonPrintPDF').style.visibility = 'visible';
                                document.getElementById('buttonNet').style.visibility = 'visible';

                                var pdf = null;
                                if(canvas.height > canvas.width)
                                    pdf = new jsPDF('p', 'mm', [canvas.width, canvas.height]);
                                else
                                    pdf = new jsPDF('l', 'mm', [canvas.width, canvas.height]);

                                pdf.addImage(imgData, 'JPEG', 0, 0, canvas.width, canvas.height);
                                pdf.save('download.pdf');
                            }
                        });

                        
                    },
                    saveData: function(){
                        saveProjectData();
                    }
                }
            });


                // $(document).on('keyup', '.numeric', function (event) {
                //     var v = this.value;
                //     if ($.isNumeric(v) === false && this.value[this.value.length - 1] != '.') {
                //         //chop off the last char entered
                //         this.value = this.value.slice(0, -1);
                //     }
                // });
        "); ?>






<!--

// GET: GetProjects
// Lista projektów (kalkulatorów) na podstawie zalogowanego użytkownika i wybranego eventu
[{
"id": 1, // id projektu (kalkulatora), int
"name": "Projekt testowy 1", // nazwa projektu, varchar(200)
"modifyDate": "2017-01-01 16:00:15" //  data modyfikacji, datetime
},
{
"id": 2,
"name": "Projekt testowy 2",
"modifyDate": "2017-01-01 16:15:15"
}]

// GET: GetProjectData?id=1
// Dane projektu na podstawie id projektu (kalkulatora)
{
"id": 1,
"name": "Projekt testowy 1",
"screenW": 20,
"screenH": 4,
"projCount": 4,
"projLuminosity": 20000,
"projResW": 1920,
"projResH": 1200,
"overlapSize": 29.5,
"focalLength": 0.67,
"projHangHeight": 3.2,
"sceneHeight": 2.5,
"screenSceneDistance": 3.5,
"personHeight": 1.8,
"logoFile": "/cache/user/logo.png",
"modifyDate": "2017-01-01 16:00:15",
"userId": 1,
"eventId": 1
}

// GET: GetUserInfo
// Pobieranie danych zalogowanego użytkownika
{
    "userId": 1, // Id użytkownika, int
    "eventId": 1, // Id eventu, int
    "logoFile": "/cache/user/???/logo.png", // Ścieżka do domyślnego logo użytkownika z nazwą pliku, varchar(255)
    "name": "Test", // Nazwa użytkownika, varchar(200)
    "company": "Firma", // Firma użytkownika, varchar(200)
    "backgroundFile" : "/cache/user/???/bg.png", // Ścieżka i nazwa pliku backgroundu obrazu testowego, varchar(255)
    "langXML": "/cache/user/???/lang.xml" // Ścieżka i nazwa pliku z tłumaczeniami, varchar(255)
}

// POST: SaveProjectData

Zapis z tą samą strukturą co odczyt.
Jeśli id projektu jest nullem to Insert, w przeciwnym wypadku update.

// POST: DeleteProject
Usuwanie projektu na podstawie id.


-->
</div>