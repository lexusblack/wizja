<?php
namespace common\widgets;

use common\models\Customer;
use common\widgets\AddModelWidget;
use Yii;
use yii\bootstrap\Html;


class AddCustomerWidget extends AddModelWidget
{

    protected $permissionName = 'clientClientsAdd';

    public function init()
    {
        $this->title = Yii::t('app', 'Dodaj klienta');
        $this->_targetClassName = Customer::className();
        parent::init();
    }

    protected function _renderFormFields($form, $model)
    {
        $model->supplier = 1;
        $model->customer = 1;
        echo Html::beginTag('div', ['class'=>'row', 'style'=>'margin-bottom:20px']);
        echo Html::beginTag('div',  ['class'=>'col-md-12']);

        echo $form->field($model, 'nip')->textInput(['maxlength' => true]);
        echo Html::a( Yii::t('app', 'Pobierz dane z GUS'), '#', ['class' => 'btn btn-success', 'onclick'=>'getGus(); return false;']);
        echo Html::endTag('div');
        echo Html::endTag('div');
        echo Html::beginTag('div', ['class'=>'row']);
        echo Html::beginTag('div',  ['class'=>'col-md-6']);

        echo $form->field($model, 'name')->textInput(['maxlength' => true]);

        echo $form->field($model, 'address')->textInput(['maxlength' => true]);

        echo $form->field($model, 'city')->textInput(['maxlength' => true]);
        echo $form->field($model, 'country')->textInput(['maxlength' => true]);
        echo Html::endTag('div');
        echo Html::beginTag('div',  ['class'=>'col-md-6']);

        echo $form->field($model, 'zip')->textInput(['maxlength' => true]);

        echo $form->field($model, 'phone')->textInput(['maxlength' => true]);

        echo $form->field($model, 'email')->textInput(['maxlength' => true]);
         echo $form->field($model, 'supplier')->checkbox();

         echo $form->field($model, 'customer')->checkbox();
        echo '<script>
            function getGus(){
                var nip = $("#customer-nip").val();
                $.get("/admin/customer/gus?nip="+nip, function(data){
                        var customer = JSON.parse(data);
                        if (customer.error=="ok")
                        {
                            customer = customer.gus;
                            $("#customer-name").val(customer.name);
                            $("#customer-address").val(customer.address);
                            $("#customer-city").val(customer.city);
                            $("#customer-zip").val(customer.zip);
                        }else{
                            toastr.error(customer.error);
                        }
                    });
                return false;
            }
        </script>';
        echo Html::endTag('div');
        echo Html::endTag('div');
    }


}