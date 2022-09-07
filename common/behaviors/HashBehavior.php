<?php
namespace common\behaviors;

use yii\base\Behavior;
use yii\db\ActiveRecord;

class HashBehavior extends Behavior
{
	public $attributes = ['hash'];
	public $values = [];
	
	public function events()
	{
		return [
			ActiveRecord::EVENT_BEFORE_VALIDATE=>'beforeSave',
			ActiveRecord::EVENT_BEFORE_INSERT=>'beforeSave',
			ActiveRecord::EVENT_BEFORE_UPDATE=>'beforeSave',
		];
	}
	
	public function beforeSave()
	{
		foreach ($this->attributes as $key=>$attribute)
		{
			if ($this->owner->$attribute==null)
			{
				if( !isset($this->values[$key]) )
				{
					$this->values[$key] = $this->owner->id;
				}
				
				$this->owner->$attribute = sha1($this->values[$key].time().mt_rand());
			}
		}
	}
}
