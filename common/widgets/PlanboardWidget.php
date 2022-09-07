<?php
namespace common\widgets;

use common\helpers\ArrayHelper;
use common\models\Event;
use common\models\EventSearch;
use common\models\form\PlanboardSearch;
use common\models\Meeting;
use common\models\Personal;
use common\models\Rent;
use common\models\Vacation;
use common\models\User;
use yii\base\Widget;
use yii\bootstrap\Html;
use yii\helpers\Url;
use Yii;


/**
 * Class PlanboardWidget
 * @package common\widgets
 *
 * Długość paska powinna być uwarunkowana datą i godziną montażu oraz datą i godziną demontażu.
 * Jeśli uzupełnione jest pole pakowanie to pasek powinien być wydłużony do pakowania ale odróżniać się od pozostałej części paska
 * (np może być wyszarzony i zakreskowany z napisem pakowanie)
 * Dodatkowo na paskach powinien widnieć inicjał Project Managera,
 * żeby każdy wiedział z kim się kontaktować odnośnie danego eventu.

 */
class PlanboardWidget extends Widget
{
    const HOUR_RATIO = 10;

    public $userId;
	public $height = '"auto"';
	public $defaultDate;
	public $currentModelId;

    /**
     * @var PlanboardSearch
     */
    protected $_searchModel;

    public $events = [];

    public $event_colors = [];

    public function init() 
    {
        parent::init();
		if (!$this->defaultDate)
		{
			$this->defaultDate = date('Y-m-d');
		}
        $this->_searchModel = new PlanboardSearch();
    }
    
    public function run() 
    {
        parent::run();

        return $this->render('planboardWidget', [
            // 'events'=>  $this->events,
            'defaultDate'=>$this->defaultDate,
            'height'=>$this->height,
            'model'=>$this->_searchModel,
            'user_types' =>$this->_searchModel->getUserTypes(),
            'skills' =>$this->_searchModel->getSkills(),
            'departments' => $this->_searchModel->getDepartments(),
        ]);
        
    }   
    
}
