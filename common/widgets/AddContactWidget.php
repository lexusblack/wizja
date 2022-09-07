<?php
namespace common\widgets;

use common\models\Contact;
use common\models\Customer;
use common\widgets\AddModelWidget;
use Yii;
use yii\bootstrap\Html;
use yii\web\JsExpression;


class AddContactWidget extends AddModelWidget
{
    public $owner;
    protected $permissionName = 'clientContactsAdd';

    public function init()
    {
        $this->_targetClassName = Contact::className();
        parent::init();
        $this->_modalEvents = [
            'show.bs.modal'=>new JsExpression('function(e){
                var customerSel = "#'.Html::getInputId($this->owner, 'customer_id').'";
                var contactSel = "#'.Html::getInputId($this->owner, 'contact_id').'";
                if($(contactSel).prop("disabled"))
                {
                    return false;
                }
                var cId = $(customerSel).val(); 
//                alert("#'.Html::getInputId($this->_model, 'customer_id').'");
                $("#'.Html::getInputId($this->_model, 'customer_id').'").val(cId);
//                $("#modal-contact_id").val(666);
            }'),
        ];


        $this->title = Yii::t('app', 'Dodaj Kontakt');
    }

    protected function _renderFormFields($form, $model)
    {
        echo Html::beginTag('div', ['class'=>'row']);
        echo Html::beginTag('div',  ['class'=>'col-md-6']);
        echo Html::activeHiddenInput($model, 'customer_id');
//        echo $form->field($model, 'customer_id');//->textInput(['id'=>'modal-customer_id']);
        echo $form->field($model, 'last_name')->textInput(['maxlength' => true]);
        echo  $form->field($model, 'first_name')->textInput(['maxlength' => true]);

        echo  $form->field($model, 'phone')->textInput(['maxlength' => true]);

        echo $form->field($model, 'email')->textInput(['maxlength' => true]);


        echo Html::endTag('div');
        echo Html::beginTag('div',  ['class'=>'col-md-6']);
        echo $form->field($model, 'position')->textInput(['maxlength' => true]);

        echo $form->field($model, 'info')->textarea(['rows' => 6]);
        echo Html::endTag('div');
        echo Html::endTag('div');
    }


}