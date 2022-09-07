<?php
use yii\helpers\Html;
use yii\bootstrap\ActiveForm;

/* @var $this yii\web\View */
/* @var $form yii\bootstrap\ActiveForm */
/* @var $model \common\models\LoginForm */

$this->title = 'Zaloguj';

$fieldOptions1 = [
    'options' => ['class' => 'form-group has-feedback'],
    'inputTemplate' => "{input}<span class='glyphicon glyphicon-envelope form-control-feedback'></span>"
];

$fieldOptions2 = [
    'options' => ['class' => 'form-group has-feedback'],
    'inputTemplate' => "{input}<span class='glyphicon glyphicon-lock form-control-feedback'></span>"
];
?>
<div class="no-mobile">
<div class="row">
	<div class="col-sm-5" id="top">
<div class="middle-box text-center loginscreen animated fadeInDown" style="width:350px;">
        <div class="box-log">
            <div>

                <img alt="image" class="img-logo" src="/files/new-event.svg">

            </div>
           
           
            <p style="padding:40px 10px 15px"><?= Yii::t('app', 'Zaloguj się, aby przejść dalej.') ?></p>
			<div class="formular">
                <?php $form = ActiveForm::begin(['id' => 'login-form', 'enableClientValidation' => false]); ?>

                <?= $form
                    ->field($model, 'username', $fieldOptions1)
                    ->label(false)
                    ->textInput(['placeholder' => $model->getAttributeLabel('username')]) ?>

                <?= $form
                    ->field($model, 'password', $fieldOptions2)
                    ->label(false)
                    ->passwordInput(['placeholder' => $model->getAttributeLabel('password')]) ?>
                <?php //echo $form->field($model, 'rememberMe')->checkbox(); ?>
                <?= Html::submitButton(Yii::t('app', 'Zaloguj'), ['class' => 'btn btn-primary btn-block btn-flat', 'name' => 'login-button']) ?>
                <?php ActiveForm::end(); ?>
			</div>
				<p class="m-t"> <small><?= Html::a(Yii::t('app', 'Zapomniałem hasła'), '/admin/site/forget-password') ?></small> </p>
           
        </div>    </div>
    </div>
	<div class="col-sm-6" id="top">
		
		<div class="news">
			<p class="tytul">Aktualizacje systemu</p>
			<p class="detail">Data ostatniej aktualizacji: 05.07.2022</p>
			<div class="margin">	</div>
				<a href="update-list" class="baton"  target="_blank">Zobacz, co się zmieniło!</a>
	
		
		
		
	</div></div></div>

	<div class="footer">
 <p class="m-t"> <small><?= Yii::t('app', 'New Management System by newsystems ©').date("Y") ?></small> </p>
</div>
	</div>	
	
	<div class="mobile">
		<div class="row">
				<div class="col-sm-12">
		
		<div class="news">
			<p class="tytul">Aktualizacje systemu</p>
			<p class="detail">Data ostatniej aktualizacji: 05.07.2022</p>
			<div class="margin">	</div>
				<a href="update-list" class="baton"  target="_blank">Zobacz, co się zmieniło!</a>
	
		
		
		
	</div></div></div>
		<div class="tlo">
			<div class="row">
	<div class="col-sm-12">
<div class="middle-box text-center loginscreen animated fadeInDown" style="width:350px;">
        <div class="box-log">
            <div>

                <img alt="image" class="img-logo" src="/files/new-event.svg">

            </div>
           
           
            <p style="padding:40px 10px 15px"><?= Yii::t('app', 'Zaloguj się, aby przejść dalej.') ?></p>
			<div class="formular">
                <?php $form = ActiveForm::begin(['id' => 'login-form', 'enableClientValidation' => false]); ?>

                <?= $form
                    ->field($model, 'username', $fieldOptions1)
                    ->label(false)
                    ->textInput(['placeholder' => $model->getAttributeLabel('username')]) ?>

                <?= $form
                    ->field($model, 'password', $fieldOptions2)
                    ->label(false)
                    ->passwordInput(['placeholder' => $model->getAttributeLabel('password')]) ?>
                <?php //echo $form->field($model, 'rememberMe')->checkbox(); ?>
                <?= Html::submitButton(Yii::t('app', 'Zaloguj'), ['class' => 'btn btn-primary btn-block btn-flat', 'name' => 'login-button']) ?>
                <?php ActiveForm::end(); ?>
			</div>
				<p class="m-t"> <small><?= Html::a(Yii::t('app', 'Zapomniałem hasła'), '/admin/site/forget-password') ?></small> </p>
           
        </div>    </div></div>
    </div> </div>
<div>
	  <img alt="image" class="img-logo" src="/files/programista2.svg">
		</div>
<div class="footerm">
 <p class="m-t"> <small><?= Yii::t('app', 'New Management System by newsystems ©').date("Y") ?></small> </p>
</div>
	</div>
	 <?php $this->registerCss('
	 .margin{
	 margin-top:100px;
	 
	 }
	 .baton{
	 padding:10px 30px;
	 font-size:20px;
	 color:white;
	 background-color:#43b7d6;
	 border-radius:20px;
	margin:20px;
	 }
	 .baton a:focus{
	 color:white;
	 }
	  .baton:focus{
	 color:white;
	 }
	 .baton:hover{
	 color:white;
	 background-color:#46b2cc;
	 }
	 .detail{
	 font-size:20px;
	 color:#444444;
	 font-weight:normal;
	 }
	 .news{
	 text-align:right;
	 }
	 .tytul{
	 text-transform:uppercase;
	 font-size:46px;
	 font-weight:bold;
	 color:#a0a0a0;
	 }
	 .btn-flat{
	margin:auto;
	 width:200px;
	 border-radius:15px;
	 background-color:#43b7d6;
	  border:1px solid #43b7d6;
	 }
	 .btn-flat:hover{
	 background-color:#46b2cc;
	 border:1px solid #46b2cc;
	 }
	 .formular{
	 padding:0 30px;
	 }
	 .footer{
	 background-color:#D1D3D4;
	 color:black;
	 font-weight:bold;
	 text-align:center;
	 }
	 .form-control{
	 background-color:white;
	 border-radius:15px;
	 color: #333;
	 border:1px solid #D1D3D4
	 }
	 h3{
	 text-transform:uppercase;
	 padding:10px 0;
	 }
	 body{
	
	 background-image: url("/files/bg-new.svg");
	 background-position:-60px 60px;
	 background-repeat:no-repeat;
	 background-attachment: scroll;
	 background-size: cover;
	 }
	  #top{
	 padding-top:20%;
	 margin-top:-200px;
	 margin-bottom:50px;
	 }
	 .mobile{
	 display:none;
	 }
 @media screen and (max-width: 1000px) {
	 body{
	  background-image:none;
	
	
	 }
	 .tlo{
	 margin-top:25px;
	  background-image: url("/files/mobile-bg2.svg");
	    background-repeat:no-repeat;
	 background-attachment: scroll;
	  background-position: top center ;
	 }
	 #top{
	  
	 margin-top:10px;
	 }
	  .box-log{
	 margin-bottom:50px;
	 
	 }
	  .detail{
	 font-size:14px;
	 color:#444444;
	 font-weight:normal;
	 }
	 .news{
	 text-align:center;
	 }
	 .tytul{
	 text-transform:uppercase;
	 font-size:26px;
	 font-weight:bold;
	 color:#a0a0a0;
	 }
	 .margin{
	 margin-top:20px;
	 }
	 .mobile{
	 display:inline;
	 }
	 .no-mobile{
	 display:none;
	 }
}
	 
	 
	 
	 .box-log{
	 
	 background-color:white;
	 border:1px solid #D1D3D460;
	 padding:20px 20px;
	 border-radius:10px;
	 box-shadow: 0 0 2em #D1D3D4;
	 }
	 .img-logo{
	 display: block;
    max-width: 60%;
    height: auto;
	 margin:auto;
	 }
	 .footerm{
	 background-color: #D1D3D4;
    color: black;
    font-weight: bold;
    text-align: center;
	 background: none repeat scroll 0 0 #D1D3D4;
    border-top: 1px solid #e7eaec;
   
    padding:0px;
 right:0;
    bottom:0;
	 
	 }
	 .footer{
	 padding:0;
	 
	 }
	 ') ?>