<?php
namespace backend\controllers;

use backend\actions\UploadAction;
use backend\models\PasswordChange;
use backend\models\StatsForm;
use common\components\filters\AccessControl;
use common\models\EventUserWorkingTime;
use common\models\EventWorkingTimeRole;
use common\models\form\Dashboard;
use common\models\form\Stat;
use common\models\form\FinanceStat;
use common\models\form\FirstUse;
use common\models\Order;
use common\models\Request;
use common\models\EventOuterGear;
use Yii;
use backend\components\Controller;
use backend\models\LoginForm;
use yii\filters\VerbFilter;
use frontend\models\SignupForm;
use yii\web\ForbiddenHttpException;

/**
 * Site controller
 */
class StatController extends Controller
{
    public $enableCsrfValidation = false;
    /**
     * @inheritdoc
     */
    public function behaviors()
    {
        return [
            'access' => [
                'class' => AccessControl::className(),
                'rules' => [
                    [
                        'actions' => ['stats', 'chart1', 'chart2', 'chart3',  'chart4'],
                        'allow' => true,
                        'roles' => ['menuStats']
                    ],
                    [
                        'actions' => ['finance'],
                        'allow' => true,
                        'roles' => ['menuInvoicesAnalize']
                    ],
                ],
            ],
        ];
    }

    public function actionChart1($m=null, $y=null, $category_id=null)
    {
        $this->layout = 'main-panel';
        if ($m==null)
        {
            $m =0;
            $y = date('Y');
        }
        if (!$category_id)
        {
            $category_id = 1;
        }
        $date = new \DateTime();
        $date = \DateTime::createFromFormat('Yn', $y.$m);

        $dateInterval = new \DateInterval('P1M');
        $date->sub($dateInterval);
        $prev = [
            'y'=>$date->format('Y'),
            'm'=>$date->format('n'),
        ];
        $date->add($dateInterval);
        $date->add($dateInterval);
        $next = [
            'y'=>$date->format('Y'),
            'm'=>$date->format('n'),
        ];
        $stat = new Stat();
        $stats = $stat->getChart1($m, $y, $category_id);
        return $this->render('chart1', ['stats'=>$stats, 'm'=>$m, 'y'=>$y, 'prev'=>$prev, 'next'=>$next, 'category_id'=>$category_id]);
    }

    public function actionChart2($m=null, $y=null, $category_id=null)
    {
        $this->layout = 'main-panel';
        if ($m==null)
        {
            $m = date('m');
            $y = date('Y');
        }
        if ($category_id==null)
        {
            $category_id = 1;
        }

        $date = new \DateTime();
        $date = \DateTime::createFromFormat('Yn', $y.$m);

        $dateInterval = new \DateInterval('P1M');
        $date->sub($dateInterval);
        $prev = [
            'y'=>$date->format('Y'),
            'm'=>$date->format('n'),
        ];
        $date->add($dateInterval);
        $date->add($dateInterval);
        $next = [
            'y'=>$date->format('Y'),
            'm'=>$date->format('n'),
        ];
        $stat = new Stat();
        $stats = $stat->getChart2($m, $y, $category_id);
        return $this->render('chart2', ['stats'=>$stats, 'm'=>$m, 'y'=>$y, 'prev'=>$prev, 'next'=>$next, 'category_id'=>$category_id]);
    }

    public function actionChart3($m=null, $y=null)
    {
        $this->layout = 'main-panel';
        if (!$y)
            $y = date('Y');
        if (!$m)
            $m = date('n');
        $d = 1;
        $date = new \DateTime();
        $date = \DateTime::createFromFormat('Y-n-d', $y."-".$m."-".$d);
        $dateInterval = new \DateInterval('P1M');
        $date->sub($dateInterval);
        $prev = [
            'y'=>$date->format('Y'),
            'm'=>$date->format('n'),
        ];
        $date->add($dateInterval);
        $date->add($dateInterval);
        $next = [
            'y'=>$date->format('Y'),
            'm'=>$date->format('n'),
        ];
        $stat = new Stat();
        $stats = $stat->getChartVehicle($m, $y);
        return $this->render('chart-vehicle', ['stats'=>$stats, 'm'=>$m, 'y'=>$y, 'prev'=>$prev, 'next'=>$next]);
    }

    public function actionChart4($m=null, $y=null, $category_id=null)
    {
        $this->layout = 'main-panel';
        if ($m==null)
        {
            $m = date('m');
            $y = date('Y');
        }
        if ($category_id==null)
        {
            $category_id = 1;
        }
        $date = new \DateTime();
        $date = \DateTime::createFromFormat('Yn', $y.$m);

        $dateInterval = new \DateInterval('P1M');
        $date->sub($dateInterval);
        $prev = [
            'y'=>$date->format('Y'),
            'm'=>$date->format('n'),
        ];
        $date->add($dateInterval);
        $date->add($dateInterval);
        $next = [
            'y'=>$date->format('Y'),
            'm'=>$date->format('n'),
        ];
        $stat = new Stat();
        $stats = $stat->getChartCustomer($m, $y, $category_id);
        return $this->render('chart4', ['stats'=>$stats, 'm'=>$m, 'y'=>$y, 'prev'=>$prev, 'next'=>$next, 'category_id'=>$category_id]);
    }

    public function actionFinance($m=null, $y=null)
    {
        if (!$y)
            $y = date('Y');
        if (!$m)
            $m = date('n');
        $date = new \DateTime();
        $date = \DateTime::createFromFormat('Yn', $y.$m);

        $dateInterval = new \DateInterval('P1M');
        $date->sub($dateInterval);
        $prev = [
            'y'=>$date->format('Y'),
            'm'=>$date->format('n'),
        ];
        $date->add($dateInterval);
        $date->add($dateInterval);
        $next = [
            'y'=>$date->format('Y'),
            'm'=>$date->format('n'),
        ];
        $stats = new FinanceStat();
        $stats->y = $y;
        $stats->m = $m;
        return $this->render('finance', ['stats'=>$stats, 'm'=>$m, 'y'=>$y, 'prev'=>$prev, 'next'=>$next]);

    }
}
