<?php

namespace common\models;

use Yii;
use \common\models\base\CustomerNote as BaseCustomerNote;
use common\helpers\ArrayHelper;

/**
 * This is the model class for table "customer_note".
 */
class CustomerNote extends BaseCustomerNote
{
    /**
     * @inheritdoc
     */

    public $permissions;

    public function rules()
    {
        return array_replace_recursive(parent::rules(),
	    [
            [['name'], 'string'],
            [['datetime', 'next_date'], 'safe'],
            [['customer_id', 'contact_id', 'event_id', 'rent_id', 'offer_id'], 'integer'],
            [['type'], 'string', 'max' => 255]
        ]);
    }

    public function getPermissionIds()
    {
        return ArrayHelper::map(CustomerNotePermission::find()->where(['customer_note_id'=>$this->id])->asArray()->all(), 'permission', 'permission');
    }

    public function linkPermissions($ids)
    {
        CustomerNotePermission::deleteAll(['customer_note_id'=>$this->id]);

        if ($ids)
        {
            foreach ($ids as $id)
            {
                $p = new CustomerNotePermission;
                $p->permission = $id;
                $p->customer_note_id = $this->id;
                $p->save();
            }           
        }

    }

    public function toShow()
    {
        if ($this->user_id==Yii::$app->user->id)
            return true;
        if ($this->getPermissionIds())
        {
            foreach ($this->getPermissionIds() as $p)
            {
                $auth = AuthAssignment::find()->where(['user_id'=>Yii::$app->user->id, 'item_name'=>$p])->one();
                
                if ($auth)
                    return true;
            }
            return false;   
        }else{
            return true;
        }
    }



    public function afterSave($insert, $changedAttributes)
    {
        parent::afterSave($insert, $changedAttributes);
            if ($insert)
            {
            
                $customer = Customer::findOne($this->customer_id);     
                $customer->createLog('note_create', $this->id); 
                $customer->last_date = date("Y-m-d H:i:s");
                if ((!$this->event_id)||($this->next_date))
                {
                    $customer->next_date = $this->next_date;
                }
                
                $customer->save();  
            }else{
                if (isset($changedAttributes['next_date']))
                {
                    $customer = Customer::findOne($this->customer_id);     
                    $customer->save(); 
                }
            }
         
    }
	
}
