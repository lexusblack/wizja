<?php
namespace common\actions;
use Yii;
use Closure;
use yii\db\ActiveRecord;
use yii\helpers\ArrayHelper;
use yii\helpers\Html;
use yii\rest\Action;
use yii\web\NotFoundHttpException;
use yii\web\Response;
use yii\web\BadRequestHttpException;
use yii\helpers\Json;


class EditableColumnAction extends \kartik\grid\EditableColumnAction
{
    public function init()
    {
        $this->findModel = function($id, $action)
        {
            $modelClass = $this->modelClass;
            $keys = $modelClass::primaryKey();
            if (count($keys) > 1) {
                if (json_decode($id)===false)
                {
                    $values = explode(',', $id);
                }
                else
                {
                    $values = Json::decode($id);
                }

                if (count($keys) === count($values)) {
                    $model = $modelClass::findOne(array_combine($keys, $values));
                }
            } elseif ($id !== null) {
                $model = $modelClass::findOne($id);
            }

            if (isset($model)) {
                return $model;
            } else {
                throw new NotFoundHttpException(Yii::t('app', "Nie znaleziono modelu").": $id");
            }
        };
        parent::init();
    }



    protected function validateEditable()
    {
        $request = Yii::$app->request;
        if ($this->postOnly && !$request->isPost || $this->ajaxOnly && !$request->isAjax) {
            throw new BadRequestHttpException(Yii::t('app', 'Brak uprawnień'));
        }
        $this->initErrorMessages();
        $post = $request->post();
        if (!isset($post['hasEditable'])) {
            return ['output' => '', 'message' => $this->errorMessages['invalidEditable']];
        }
        /**
         * @var ActiveRecord $model
         */
        $key = ArrayHelper::getValue($post, 'editableKey');
        $model = $this->findModel($key);
        if (!$model) {
            return ['output' => '', 'message' => $this->errorMessages['invalidModel']];
        }
        if ($this->checkAccess && is_callable($this->checkAccess)) {
            call_user_func($this->checkAccess, $this->id, $model);
        }
        $model->scenario = $this->scenario;
        $index = ArrayHelper::getValue($post, 'editableIndex');
        $attribute = ArrayHelper::getValue($post, 'editableAttribute');
        $formName = isset($this->formName) ? $this->formName: $model->formName();
        if (!$formName || is_null($index) || !isset($post[$formName][$index])) {
            return ['output' => '', 'message' => $this->errorMessages['editableException']];
        }
        $postData = [$model->formName() => $post[$formName][$index]];
        if ($model->load($postData)) {
            $params = [$model, $attribute, $key, $index];
            $message = static::parseValue($this->outputMessage, $params);
            if (!$model->save()) {
                if (!$model->hasErrors()) {
                    return ['output' => '', 'message' => $this->errorMessages['saveException']];
                }
                if (empty($message) && $this->showModelErrors) {
                    $message = Html::errorSummary($model, $this->errorOptions);
                }
            }
            $value = static::parseValue($this->outputValue, $params);
            return ['output' => $value, 'message' => $message];
        }
        return ['output' => '', 'message' => ''];
    }
}