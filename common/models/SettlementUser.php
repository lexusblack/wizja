<?php

namespace common\models;

use common\helpers\ArrayHelper;
use kartik\helpers\Html;
use Yii;
use \common\models\base\SettlementUser as BaseSettlementUser;
use yii\caching\ChainedDependency;
use yii\caching\DbDependency;
use yii\db\Expression;
use yii\db\Query;

/**
 * This is the model class for table "settlement_user".
 */
class SettlementUser extends BaseSettlementUser
{
    const STATUS_SETTLED = 1;
    const STATUS_UNSETTLED = 0;

    public static function store($user, $event, $year, $month, $section = false)
    {
	    /** @var \common\models\event $event */
	    /** @var \common\models\user $user */
        $userId = $user->id;
        $date = new \DateTime($event->getTimeStart());

        $params = [
            'user_id'=>$user->id,
            'event_id' => $event->id,
            'year'=>$year,
            'month'=>$month,
        ];

        $model = static::loadByParams($params);
        if ($model->status == static::STATUS_SETTLED)
        {
            //Nie można zmieniać
            //return false;
        }
		if ($model->month != $month || $model->year != $year) {
            //return false;
		}

        $summary = $event->getWorkingTimeSummary($userId, false, $year, $month);
        $addons = $event->getUserAddons($userId, $year, $month);
        $allowances = $event->getUserAllowances($userId, $year, $month);
        $roleAddons = $event->getRolesAddons($userId, true, $year, $month);

        $departmets = Department::find()->innerJoinWith(['eventUserWorkingTimes'])->where([
            'user_id'=>$user->id,
            'event_id'=>$event->id,
        ])->all();

        $departmentData = [];
        foreach ($departmets as $k=>$v) {
            $departmentData[$v->id] = $v->name;
        }

        $addonData = [];
        foreach ($addons as $k=>$v) {
            $addonData[] = [
                'label'=>$v->name,
                'amount'=>$v->amount,
                'type'=>'addon',
                'id'=>$v->id
            ];
        }
        foreach ($allowances as $k=>$v) {
            $addonData[] = [
                'label'=>Yii::t('app', 'Dieta'),
                'amount'=>$v->amount,
                'type'=>'allowance',
                'id'=>$v->id
            ];
        }

        $workingHours = EventUserWorkingTime::getMonth($user->id, $event->id, $month, $year
        );
        $workingHoursData = [];
        foreach ($workingHours as $k=>$v) {
            $workingHoursData[$k] = $v->attributes;
        }

        $rolesData =[];
        foreach ($roleAddons as $k=>$v) {
            $rolesData[$v['role_id']] = [
                'label'=>$v['name'],
                'amount'=>$v['amount'],
            ];
        }

        $model->addon_data = serialize($addonData);
        $model->department_data = serialize($departmentData);
        $model->role_data = serialize($rolesData);
        $model->working_hours_data = serialize($workingHoursData);
        $model->sum = $summary['sum'];
        if (($section)&&($summary['sum']==0))
        {
            return false;
        }

        if ($model->save()) {
            return true;
        }else{

           // echo var_dump($model->errors);
        }
        return false;
    }

    public function getDepartmentsString($separator = '<br />')
    {
        $data = unserialize($this->department_data);

        return implode($separator, $data);
    }

    public function getRolesString()
    {
        return \common\models\UserEventRole::getRolesString($this->user_id, $this->event_id);
    }

    public function getRolesAddonsString($separator = '<br />')
    {
        $formatter = Yii::$app->formatter;
        $data = unserialize($this->role_data);
        $list = [];
        foreach ($data as $k=>$v)
        {
            $list[] = $v['label'].'/'.$formatter->asCurrency($v['amount']);
        }

        return implode($separator, $list);
    }

    public function getAddonsString($separator = '<br />')
    {
        $formatter = Yii::$app->formatter;
        $data = unserialize($this->addon_data);
        $list = [];
        foreach ($data as $k=>$v)
        {
            $list[] = $v['label'].'/'.$formatter->asCurrency($v['amount']);
        }

        return implode($separator, $list);
    }

    public function getWorkingHoursString($separator = '<br />')
    {
        $data = unserialize($this->working_hours_data);
        $formatter = Yii::$app->formatter;

        $content = Html::beginTag('table');
        $content .= Html::tag('tr', Html::tag('th', Yii::t('app', 'Data')).Html::tag('th', Yii::t('app', 'Czas')));
        foreach ($data as $d)
        {
            $content .= Html::tag('tr',
                Html::tag('td', "<div class='nowrap'>od: " . $formatter->asDatetime($d['start_time'], 'short') . "</div><div class='nowrap'>do: " . $formatter->asDatetime($d['end_time'], 'short') . "</div>", ['class' => 'nowrap'])
                .Html::tag('td', str_replace(['dni', 'dzień', 'gidzin', 'godzina', 'godziny', 'minuty', 'minut', 'sekund', 'sekundy'], ['d', 'd', 'h', 'h', 'h', 'min', 'min', 's', 's'], $formatter->asDuration($d['duration'])), ['class' => 'nowrap']));
        }
        $content .= Html::endTag('table');


        return $content;
    }

    public function getWorkingHoursCostString() {
        if ($this->status == static::STATUS_SETTLED) {
            $payment = (float) $this->sum;
            $roleAddon = unserialize($this->role_data);
            $addonData = unserialize($this->addon_data);
            foreach ($roleAddon as $addon) {
                $payment -= (float) $addon['amount'];
            }
            foreach ($addonData as $addon) {
                $payment -= (float) $addon['amount'];
            }
            return Yii::$app->formatter->asCurrency($payment);
        }
        return Yii::$app->formatter->asCurrency($this->event->getUserWorkingTimeSalary($this->user_id, $this->year, $this->month));
    }

    public static function storeAll($userId=null, $year, $month) {

        if (isset($userId))
        {
            $event_ids = ArrayHelper::map(EventUser::find()->where(['user_id'=>$userId])->asArray()->all(), 'event_id', 'event_id');
            $events_ids2 = ArrayHelper::map(EventUserWorkingTime::find()->where(['user_id'=>$userId])->andWhere(['MONTH(start_time)'=>$month, 'YEAR(start_time)'=>$year])->asArray()->all(), 'event_id', 'event_id');
            $events_ids3 = ArrayHelper::map(EventUserAddon::find()->where(['user_id'=>$userId])->andWhere(['MONTH(start_time)'=>$month, 'YEAR(start_time)'=>$year])->asArray()->all(), 'event_id', 'event_id');
            $events_ids4 = ArrayHelper::map(EventUserAllowance::find()->where(['user_id'=>$userId])->andWhere(['MONTH(start_time)'=>$month, 'YEAR(start_time)'=>$year])->asArray()->all(), 'event_id', 'event_id');
            $event_ids5 = ArrayHelper::map(SettlementUser::find()->where(['user_id'=>$userId])->andWhere(['month'=>$month, 'year'=>$year])->asArray()->all(), 'event_id', 'event_id');
            $models = Event::find()->where(['MONTH(event_start)'=>$month, 'YEAR(event_start)'=>$year])->orWhere(['MONTH(montage_start)'=>$month, 'YEAR(montage_start)'=>$year])->orWhere(['MONTH(packing_start)'=>$month, 'YEAR(packing_start)'=>$year])->orWhere(['MONTH(disassembly_start)'=>$month, 'YEAR(disassembly_start)'=>$year])->orWhere(['MONTH(montage_end)'=>$month, 'YEAR(montage_end)'=>$year])->orWhere(['MONTH(packing_end)'=>$month, 'YEAR(packing_end)'=>$year])->orWhere(['MONTH(disassembly_end)'=>$month, 'YEAR(disassembly_end)'=>$year])->orWhere(['MONTH(event_end)'=>$month, 'YEAR(event_end)'=>$year])->orWhere(['id'=>$events_ids2])->orWhere(['id'=>$events_ids3])->orWhere(['id'=>$events_ids4])->orWhere(['id'=>$event_ids5])->all();
            $userWithProvision = UserProvision::find()->where(['user_id'=>$userId])->andWhere(['event_type'=>2])->count();
            $user = User::findOne($userId);
            foreach ($models as $event)
            {
                if ((in_array($event->id, $event_ids5))||(in_array($event->id, $event_ids))||(in_array($event->id, $events_ids2))||(in_array($event->id, $events_ids3))||(in_array($event->id, $events_ids4))||($event->manager_id == $userId)||($userWithProvision)){

                    SettlementUser::store($user, $event, $year, $month);
                }
            }
        }else{
            $models = Event::find()->where(['MONTH(event_start)'=>$month, 'YEAR(event_start)'=>$year])->orWhere(['MONTH(montage_start)'=>$month, 'YEAR(montage_start)'=>$year])->orWhere(['MONTH(packing_start)'=>$month, 'YEAR(packing_start)'=>$year])->orWhere(['MONTH(disassembly_start)'=>$month, 'YEAR(disassembly_start)'=>$year])->orWhere(['MONTH(montage_end)'=>$month, 'YEAR(montage_end)'=>$year])->orWhere(['MONTH(packing_end)'=>$month, 'YEAR(packing_end)'=>$year])->orWhere(['MONTH(disassembly_end)'=>$month, 'YEAR(disassembly_end)'=>$year])->orWhere(['MONTH(event_end)'=>$month, 'YEAR(event_end)'=>$year])->all();
            foreach ($models as $event) {
                foreach ($event->users as $user) {
                    SettlementUser::store($user, $event, $year, $month);
                }
            }
        }

    }

    public static function setSettled($userId, $year, $month, $status=self::STATUS_SETTLED)
    {
        Note::createNote(2, 'workerMonth', ['month'=>$month,'year'=>$year, 'status'=>$status], $userId);
        $params = [
            'user_id'=>$userId,
            'year'=>$year,
            'month'=>$month,
        ];

        $models = static::findAll($params);

        $transaction = static::getDb()->beginTransaction();
        try
        {
            foreach ($models as $model)
            {
                /* @var $model static */
                $model->updateAttributes([
                    'status'=>$status,
                    'update_time'=>new Expression('NOW()'),
                ]); //nie odpala eventów, ani walidacji
            }
            $transaction->commit();
        }
        catch (\Throwable $e)
        {
            $transaction->rollBack();
            throw $e;
        }

        return true;
    }
}
