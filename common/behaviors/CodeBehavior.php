<?php
namespace common\behaviors;

use yii\base\Behavior;
use yii\db\ActiveRecord;

class CodeBehavior extends Behavior
{
	public $attributes = ['code'];
	public $prefix;

	protected $_code;
	
	public function events()
	{
		return [
			ActiveRecord::EVENT_BEFORE_INSERT=>'beforeSave',
			ActiveRecord::EVENT_BEFORE_UPDATE=>'beforeSave',
		];
	}
	
	public function beforeSave()
	{
		foreach ($this->attributes as $key=>$attribute)
		{
			if ($this->prefix=='E')
			{
				if ($this->owner->type==2)
				{
					//$this->prefix = 'P';
				}
			}
			if ($this->owner->$attribute == null)
			{
                
                if ($this->prefix == 'E')
                {
                	$sett = \common\models\Settings::find()->where(['key'=>'eventNumber'])->one();
                	if (($sett)&&($sett->value!=""))
                	{
						$this->_code = $this->getFullNumber($sett->value,  date('d'), date('m'), date('Y'), date('y'));
                	}else{
                		 $this->_code = $this->prefix.date('Y').'/';
                         $number = $this->_getNumber($attribute);
                		 
                		 $this->_code .= $number;
                	}
                }

                if ($this->prefix == 'W')
                {
                	$sett = \common\models\Settings::find()->where(['key'=>'rentNumber'])->one();
                	if (($sett)&&($sett->value!=""))
                	{
						$this->_code = $this->getFullNumber($sett->value, date('d'), date('m'), date('Y'), date('y'));
                	}else{
                		 $this->_code = $this->prefix.date('Y').'/';
                         $number = $this->_getNumber($attribute);
                		 
                		 $this->_code .= $number;
                	}
                }
                if ($this->prefix == 'O')
                {
                	$sett = \common\models\Settings::find()->where(['key'=>'offerNumber'])->one();
                	if (($sett)&&($sett->value!=""))
                	{
						$this->_code = $this->getFullNumber($sett->value, date('d'), date('m'), date('Y'), date('y'));
                	}else{
                        $this->_code = $this->prefix.date('Y').'/';
                		 $number = $this->_getNumber($attribute);
                		 
                		 $this->_code .= $number;
                	}
                }
               

                

                



				$this->owner->$attribute = $this->_code;

			}
		}
	}

	protected function _getNumber($attribute)
    {
        $number = 1;

        $query = $this->owner->find();
        $query->where(['like', 'code', $this->_code.'%', false]);
        $query->orderby(['CAST(SUBSTR(code, 7) AS UNSIGNED)'=>SORT_DESC]);
        $result = $query->one();
        if ($result !== null)
        {
            $pattern = '@'.addslashes($this->_code).'(\d+)@i';
            if (preg_match($pattern, $result->code, $match))
            {
                $number = $match[1]+1;
            }
        }



        return $number;
    }

    public function getN()
    {

    }

    public function getFullNumber($pattern, $day, $month, $year, $year2)
    {
        $e = $this->owner->find()->where(['YEAR(create_time)'=>$year])->orderBy(['number'=>SORT_DESC])->one();

        if ($e)
        	$number = $e->number+1;
        else
        	$number = 1;
        $this->owner->number = $number;
        $search = [
            '@\[numer\]@',
            '@\[dzień\]@',
            '@\[miesiąc\]@',
            '@\[rok\]@',
            '@\[rok\:format_dwucyfrowy\]@',
        ];
        $replace = [
            $number,
            $day,
            $month,
            $year,
            $year2,
        ];

        $number = preg_replace($search, $replace, $pattern);



        return $number;
    }
}
