<?php

namespace common\models;

use Yii;
use \common\models\base\FreeOffer as BaseFreeOffer;

/**
 * This is the model class for table "offer".
 */
class FreeOffer extends BaseFreeOffer
{
    /**
     * @inheritdoc
     */

    public $skillIds;
    public $deviceIds;
    public function rules()
    {
        return array_replace_recursive(parent::rules(),
	    [
            [['city_id', 'name', 'start_time', 'end_time', 'description', 'deal_type', 'skillIds'], 'required'],
            [['skillIds', 'deviceIds'], 'each', 'rule' => ['string']],
        ]);
    }

        public function setLinkedObj()
    {
        $skills = explode(';', $this->skills);
        $skills = explode(';', $this->skills);
        $this->skillIds = [];
        $this->deviceIds= [];
            foreach ($skills as $skill)
            {
                if ($skill!="")
                {
                    $this->skillIds[$skill] = $skill;
                }
                
            }
        $devices = explode(';', $this->devices);
        foreach ($devices as $device)
            {
                if ($device!="")
                {
                    $this->deviceIds[$device] = $device;
                }
                
            }
        $this->skills = "";
        $this->devices = "";
    }

    public function beforeSave($insert)
    {
            $skills = explode(';', $this->skills);
            foreach ($skills as $skill)
            {
                if ($skill!="")
                {
                    $s = FreeSkill::findOne(['name'=>$skill]);
                    if (!$s)
                    {
                        $s = new FreeSkill();
                        $s->name = $skill;
                        $s->active = 0;
                        $s->save();
                    }
                }
                
            }
            $devices = explode(';', $this->devices);
            foreach ($devices as $device)
            {
                if ($device!="")
                {
                    $s = FreeDevice::findOne(['name'=>$device]);
                    if (!$s)
                    {
                        $s = new FreeDevice();
                        $s->name = $device;
                        $s->active = 0;
                        $s->save();
                    }
                }

                
            }
            if (sizeof($this->skillIds) > 0)
            {
                if ($this->skillIds)
                    {
                        if ($this->skills!="")
                            $this->skills .= ";";
                        $this->skills .= implode(';', $this->skillIds);
                    }
            }
            if (sizeof($this->deviceIds) > 0)
            {
                if ($this->deviceIds){
                    if ($this->devices!="")
                        $this->devices .= ";";
                    $this->devices .= implode(';', $this->deviceIds);
                }
            }

            if ($insert)
            {
                $this->company = \Yii::$app->params['companyID'];
                $this->company_name = Yii::$app->settings->get('companyName', 'main');
                $this->user_id = Yii::$app->user->id;
                $this->user_mail = Yii::$app->user->identity->email;
            }



        return parent::beforeSave($insert);
    }
	
}
