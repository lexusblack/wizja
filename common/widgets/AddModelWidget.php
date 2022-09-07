<?php
namespace common\widgets;

use common\helpers\ArrayHelper;
use demogorgorn\ajax\AjaxSubmitButton;
use kartik\alert\Alert;
use Yii;
use yii\base\InvalidConfigException;
use yii\base\Widget;
use yii\bootstrap\Html;
use kartik\form\ActiveForm;
use yii\helpers\Inflector;
use yii\bootstrap\Modal;
use yii\web\View;
use common\models\Customer;

class AddModelWidget extends Widget
{
    public $widgetId;
    public $title;

    protected $_targetClassName;

    protected $_model;
    protected $_saved_model_id = null;
    protected $_saved = false;

    protected $_modalEvents = [];
    protected $_modalOptions = [];
    protected $permissionName = null;


    public function init()
    {
        $this->widgetId = Inflector::underscore((new \ReflectionClass($this))->getShortName());
        if ($this->_targetClassName === null)
        {
            throw new InvalidConfigException(Yii::t('app', 'Nie podano klasy modelu'));
        }
        $this->_initModel();
        parent::init();
    }

    protected function _initModel()
    {
        $this->_model = \Yii::createObject($this->_targetClassName);
    }

    public function run()
    {
        parent::run();
        return $this->_saveModel();
    }


    protected function _saveModel()
    {
        $model = $this->_model;
        if ($this->_model->load(\Yii::$app->request->post()) && $this->_model->save())
        {
            $this->_saved_model_id = $this->_model->id;
            $this->_initModel();
            $this->_saved = true;
        }else{
            if (($this->_targetClassName==Customer::className())&&(\Yii::$app->request->post()))
            {
                $customer = Customer::find()->where(['nip'=>\Yii::$app->request->post('Customer')['nip']])->one();
                $this->title = $customer->name;
                if ($customer){
                if ($customer->active){
                    $this->_model = $customer;
                        $this->_saved_model_id = $customer->id;
                        $this->_saved = true; 
                    }else{
                $customer->active = 1;
                if ($customer->save())
                {
                        $this->_model = $customer;
                        $this->_saved_model_id = $customer->id;
                        $this->_saved = true;   
                }else{
                    $this->_saved = false;
                    
                }
                }
   

                }else{
                    $this->_saved = false;
                    
                }

            }else{
                $this->_saved = false;
            }
            
            
        }

        return $this->_renderContent();
    }

    protected function _renderFormFields($form, $model)
    {
        echo Yii::t('app', 'Musisz usupełnić').': '.__FUNCTION__;
    }



    protected function _renderForm()
    {
        $content = '';
        $model = $this->_model;

        $blockId = $this->widgetId.'-form';
        $this->view->beginBlock($blockId);
        $form = ActiveForm::begin(['id'=>'ccc',
            'fieldConfig'=>[
                'showLabels'=>false,
                'autoPlaceholder'=> true,
            ]]);

            echo Html::beginTag('div', ['class'=>'add-model-widget-content']);
            if ($this->_saved == true)
            {
                 echo "XXX".$this->widgetId.$this->_saved_model_id."PPP".$this->widgetId;
            }
            
            $this->_renderFormFields($form, $model);
        echo Html::endTag('div');

        echo AjaxSubmitButton::widget([
                'label' => Yii::t('app', 'Zapisz'),
                'ajaxOptions' => [
                    'type'=>'POST',
                    'url'=>[''],
                    'success' => new \yii\web\JsExpression('function(html){
                        if (html.indexOf("XXX'.$this->widgetId.'") >= 0)
                        {
                            var i = html.indexOf("XXX'.$this->widgetId.'")+3+"'.$this->widgetId.'".length;

                            var j = html.indexOf("PPP'.$this->widgetId.'");
                            var id = html.substring(i, j); 
                            if (!isNaN(id))
                            {
                                id = parseInt(id);
                                var sel = "#'.$this->widgetId.'";

                                name = $(sel).find("input[type=text]").filter(":visible:first").val();
                                name2 = $(sel).find("input[type=text]").filter(":visible:eq( 1 )").val();
                                target = "'.strtolower($this->_targetClassName).'";
                                target = target.substring(12, target.length);
                                target = "[id$=-"+target+"_id]";
                                $(target).append($("<option>", {
                                    value: id,
                                    text: name+" "+name2
                                }));
                                $(target).val(id).trigger("change");
                                $("#'.$this->widgetId.'").modal("hide");
                            }else{
                                alert("Błąd zapisu! Sprawdź czy obiekt o tych danych już istnieje.");
                            }


                        }
                       }'),
                    ],
                    'options' => ['class' => 'btn btn-primary btn-block', 'type' => 'submit'],
                    ]);

        ActiveForm::end();
        $this->view->endBlock();
        $content = $this->view->blocks[$blockId];
        unset($this->view->blocks[$blockId]);
        return $content;
    }

    protected function _renderContent()
    {
        $content = null;
        if ($this->permissionName == null || $this->permissionName != null && Yii::$app->user->can($this->permissionName)) {
            $content = Html::a(Html::icon('plus'), '#', ['class' => 'btn btn-xs btn-success pull-right',
                'data' => ['toggle' => 'modal', 'target' => '#' . $this->widgetId,]]);
        }

        $this->view->beginBlock($this->widgetId);
        Modal::begin([
            'id' => $this->widgetId,
            'header' => Html::tag('h3', $this->title),
            'toggleButton' => false,
            'clientOptions' => $this->_modalOptions,
            'clientEvents' => $this->_modalEvents,
        ]);

        echo $this->_renderForm();

        Modal::end();
        $this->view->endBlock();



        \Yii::$app->view->on(View::EVENT_AFTER_RENDER, function () {
            $content = ArrayHelper::getValue($this->view->blocks, $this->widgetId, '');
             unset($this->view->blocks[$this->widgetId]);
             echo $content;
        });

        return $content;
    }

}