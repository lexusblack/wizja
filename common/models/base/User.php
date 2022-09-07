<?php
// This class was automatically generated by a giiant build task
// You should not change it manually as it will be overwritten on next build

namespace common\models\base;

use Yii;

/**
 * This is the base-model class for table "user".
 *
 * @property integer $id
 * @property string $username
 * @property string $auth_key
 * @property string $password_hash
 * @property string $password_reset_token
 * @property string $email
 * @property integer $role
 * @property integer $status
 * @property string $create_time
 * @property string $update_time
 * @property string $first_name
 * @property string $last_name
 * @property string $last_visit
 * @property string $photo
 * @property string $birth_date
 * @property string $pesel
 * @property string $id_card
 * @property string $phone
 * @property integer $type
 * @property integer $rate_type
 * @property string $rate_amount
 * @property string $overtime_amount
 * @property integer $base_hours
 * @property string $login_token
 *
 * @property \common\models\CalendarUserFilter[] $calendarUserFilters
 * @property \common\models\CalendarUserFilter[] $calendarUserFilters0
 * @property \common\models\Event[] $events
 * @property \common\models\Event[] $events0
 * @property \common\models\Event[] $events1
 * @property \common\models\Event[] $events2
 * @property \common\models\Event[] $events3
 * @property \common\models\EventBreaksUser[] $eventBreaksUsers
 * @property \common\models\EventBreaks[] $eventBreaks
 * @property \common\models\EventExpense[] $eventExpenses
 * @property \common\models\EventUser[] $eventUsers
 * @property \common\models\EventUser[] $eventUsers0
 * @property \common\models\Event[] $events4
 * @property \common\models\EventUserAddon[] $eventUserAddons
 * @property \common\models\EventUserAllowance[] $eventUserAllowances
 * @property \common\models\EventUserPlannedBreaks[] $eventUserPlannedBreaks
 * @property \common\models\EventUserPlannedWrokingTime[] $eventUserPlannedWrokingTimes
 * @property \common\models\EventUserWorkingTime[] $eventUserWorkingTimes
 * @property \common\models\Expense[] $expenses
 * @property \common\models\ExpensePaymentHistory[] $expensePaymentHistories
 * @property \common\models\IncomesWarehouse[] $incomesWarehouses
 * @property \common\models\Invoice[] $invoices
 * @property \common\models\InvoicePaymentHistory[] $invoicePaymentHistories
 * @property \common\models\Meeting[] $meetings
 * @property \common\models\MeetingUser[] $meetingUsers
 * @property \common\models\Meeting[] $meetings0
 * @property \common\models\Offer[] $offers
 * @property \common\models\OfferUserSkills[] $offerUserSkills
 * @property \common\models\OutcomesWarehouse[] $outcomesWarehouses
 * @property \common\models\Personal[] $personals
 * @property \common\models\PlanboardUserEventRoleOrder[] $planboardUserEventRoleOrders
 * @property \common\models\PlanboardUserEventRoleUsersOrder[] $planboardUserEventRoleUsersOrders
 * @property \common\models\PlanboardUserEventRoleUsersOrder[] $planboardUserEventRoleUsersOrders0
 * @property \common\models\PlanboardUserGeneralEventOrder[] $planboardUserGeneralEventOrders
 * @property \common\models\PlanboardUserGeneralEventOrder[] $planboardUserGeneralEventOrders0
 * @property \common\models\PlanboardUserOrder[] $planboardUserOrders
 * @property \common\models\PlanboardVehicleOrder[] $planboardVehicleOrders
 * @property \common\models\Rent[] $rents
 * @property \common\models\SettlementUser[] $settlementUsers
 * @property \common\models\Task[] $tasks
 * @property \common\models\Task[] $tasks0
 * @property \common\models\NotificationSms[] $notificationSmses
 * @property \common\models\NotificationMail[] $notificationMails
 * @property \common\models\UserAddonRate[] $userAddonRates
 * @property \common\models\UserDepartment[] $userDepartments
 * @property \common\models\Department[] $departments
 * @property \common\models\UserNotification[] $userNotifications
 * @property \common\models\UserSkill[] $userSkills
 * @property \common\models\Skill[] $skills
 * @property \common\models\Vacation[] $vacations
 * @property \common\models\VehicleUserRemind[] $vehicleUserReminds
 * @property \common\models\Vehicle[] $vehicles
 * @property string $aliasModel
 * @property integer $active
 */
abstract class User extends \common\components\BaseActiveRecord
{



    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'user';
    }


    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['username', 'auth_key', 'password_hash', 'email', 'first_name', 'last_name'], 'required'],
            [['role', 'status', 'type', 'rate_type', 'base_hours', 'active', 'visible_in_offer', 'gear_category_id'], 'integer'],
            [['create_time', 'update_time', 'last_visit', 'birth_date'], 'safe'],
            [['rate_amount', 'overtime_amount', 'tax_rate', 'zus_rate', 'nfz_rate', 'vacation_days', 'vacation_rate', 'vat_rate'], 'number'],
            [['username', 'password_hash', 'password_reset_token', 'email', 'first_name', 'last_name', 'photo', 'id_card', 'phone', 'login_token'], 'string', 'max' => 255],
            [['auth_key'], 'string', 'max' => 32],
            [['pesel'], 'string', 'max' => 11]
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => Yii::t('app', 'ID'),
            'username' => Yii::t('app', 'Nazwa użytkownika'),
            'auth_key' => Yii::t('app', 'Auth Key'),
            'password_hash' => Yii::t('app', 'Password Hash'),
            'password_reset_token' => Yii::t('app', 'Token resetowania hasła'),
            'email' => Yii::t('app', 'Email'),
            'role' => Yii::t('app', 'Role'),
            'status' => Yii::t('app', 'Status'),
            'create_time' => Yii::t('app', 'Stworzono'),
            'update_time' => Yii::t('app', 'Zaktualizowano'),
            'first_name' => Yii::t('app', 'Imie'),
            'last_name' => Yii::t('app', 'Nazwisko'),
            'last_visit' => Yii::t('app', 'Ostatnia wizyta'),
            'photo' => Yii::t('app', 'Zdjęcie'),
            'birth_date' => Yii::t('app', 'Data urodzenia'),
            'pesel' => Yii::t('app', 'Pesel'),
            'id_card' => Yii::t('app', 'Nr dowodu'),
            'phone' => Yii::t('app', 'Telefon'),
            'type' => Yii::t('app', 'Typ'),
            'rate_type' => Yii::t('app', 'Typ stawki'),
            'rate_amount' => Yii::t('app', 'Stawka'),
            'overtime_amount' => Yii::t('app', 'Nadgodziny stawka'),
            'base_hours' => Yii::t('app', 'Ilość godzin w etacie'),
            'active' => Yii::t('app', 'Aktywny'),
            'visible_in_offer'=> Yii::t('app', 'Widoczny w planowaniu'),
            'tax_rate' => Yii::t('app', 'Stawka podatku'),
            'zus_rate' => Yii::t('app', 'Stawka ZUS (bez zdrowotnego)'),
            'nfz_rate' => Yii::t('app', 'Stawka ZUS zdrowotne'),
            'vacation_rate'=> Yii::t('app', 'Stawka za płatny dzień urlopowy'),
            'vacation_days'=> Yii::t('app', 'Liczba dni urlopu płatnego'),
            'gear_category_id'=> Yii::t('app', 'Filtr na kategorię w magazynie')
        ];
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getCalendarUserFilters()
    {
        return $this->hasMany(\common\models\CalendarUserFilter::className(), ['manager_id' => 'id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getCalendarUserFilters0()
    {
        return $this->hasMany(\common\models\CalendarUserFilter::className(), ['user_id' => 'id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getEvents()
    {
        return $this->hasMany(\common\models\Event::className(), ['manager_id' => 'id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getUserNotes()
    {
        return $this->hasMany(\common\models\UserNote::className(), ['user_id' => 'id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getEvents0()
    {
        return $this->hasMany(\common\models\Event::className(), ['creator_id' => 'id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getEvents1()
    {
        return $this->hasMany(\common\models\Event::className(), ['expense_entered_user_id' => 'id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getEvents2()
    {
        return $this->hasMany(\common\models\Event::className(), ['ready_to_invoice_user_id' => 'id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getEvents3()
    {
        return $this->hasMany(\common\models\Event::className(), ['offer_sent_user_id' => 'id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getEventBreaksUsers()
    {
        return $this->hasMany(\common\models\EventBreaksUser::className(), ['user_id' => 'id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getEventBreaks()
    {
        return $this->hasMany(\common\models\EventBreaks::className(), ['id' => 'event_break_id'])->viaTable('event_breaks_user', ['user_id' => 'id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getEventExpenses()
    {
        return $this->hasMany(\common\models\EventExpense::className(), ['creator_id' => 'id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getEventUsers()
    {
        return $this->hasMany(\common\models\EventUser::className(), ['user_id' => 'id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getEventUsers0()
    {
        return $this->hasMany(\common\models\EventUser::className(), ['creator_id' => 'id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getEvents4()
    {
        return $this->hasMany(\common\models\Event::className(), ['id' => 'event_id'])->viaTable('event_user', ['user_id' => 'id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getEventUserAddons()
    {
        return $this->hasMany(\common\models\EventUserAddon::className(), ['user_id' => 'id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getEventUserAllowances()
    {
        return $this->hasMany(\common\models\EventUserAllowance::className(), ['user_id' => 'id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getEventUserPlannedBreaks()
    {
        return $this->hasMany(\common\models\EventUserPlannedBreaks::className(), ['user_id' => 'id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getEventUserPlannedWrokingTimes()
    {
        return $this->hasMany(\common\models\EventUserPlannedWrokingTime::className(), ['user_id' => 'id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getEventUserWorkingTimes()
    {
        return $this->hasMany(\common\models\EventUserWorkingTime::className(), ['user_id' => 'id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getExpenses()
    {
        return $this->hasMany(\common\models\Expense::className(), ['creator_id' => 'id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getExpensePaymentHistories()
    {
        return $this->hasMany(\common\models\ExpensePaymentHistory::className(), ['creator_id' => 'id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getIncomesWarehouses()
    {
        return $this->hasMany(\common\models\IncomesWarehouse::className(), ['user' => 'id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getInvoices()
    {
        return $this->hasMany(\common\models\Invoice::className(), ['creator_id' => 'id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getInvoicePaymentHistories()
    {
        return $this->hasMany(\common\models\InvoicePaymentHistory::className(), ['creator_id' => 'id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getMeetings()
    {
        return $this->hasMany(\common\models\Meeting::className(), ['created_by' => 'id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getMeetingUsers()
    {
        return $this->hasMany(\common\models\MeetingUser::className(), ['user_id' => 'id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getMeetings0()
    {
        return $this->hasMany(\common\models\Meeting::className(), ['id' => 'meeting_id'])->viaTable('meeting_user', ['user_id' => 'id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getOffers()
    {
        return $this->hasMany(\common\models\Offer::className(), ['manager_id' => 'id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getOfferUserSkills()
    {
        return $this->hasMany(\common\models\OfferUserSkills::className(), ['user_id' => 'id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getOutcomesWarehouses()
    {
        return $this->hasMany(\common\models\OutcomesWarehouse::className(), ['user' => 'id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getPersonals()
    {
        return $this->hasMany(\common\models\Personal::className(), ['user_id' => 'id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getPlanboardUserEventRoleOrders()
    {
        return $this->hasMany(\common\models\PlanboardUserEventRoleOrder::className(), ['user_id' => 'id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getPlanboardUserEventRoleUsersOrders()
    {
        return $this->hasMany(\common\models\PlanboardUserEventRoleUsersOrder::className(), ['user_id' => 'id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getPlanboardUserEventRoleUsersOrders0()
    {
        return $this->hasMany(\common\models\PlanboardUserEventRoleUsersOrder::className(), ['event_user' => 'id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getPlanboardUserGeneralEventOrders()
    {
        return $this->hasMany(\common\models\PlanboardUserGeneralEventOrder::className(), ['user_id' => 'id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getPlanboardUserGeneralEventOrders0()
    {
        return $this->hasMany(\common\models\PlanboardUserGeneralEventOrder::className(), ['user_event' => 'id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getPlanboardUserOrders()
    {
        return $this->hasMany(\common\models\PlanboardUserOrder::className(), ['user_id' => 'id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getPlanboardVehicleOrders()
    {
        return $this->hasMany(\common\models\PlanboardVehicleOrder::className(), ['user_id' => 'id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getRents()
    {
        return $this->hasMany(\common\models\Rent::className(), ['created_by' => 'id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getSettlementUsers()
    {
        return $this->hasMany(\common\models\SettlementUser::className(), ['user_id' => 'id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getTasks()
    {
        return $this->hasMany(\common\models\Task::className(), ['id' => 'task_id'])->viaTable('user_task', ['user_id' => 'id']);
    }

    public function getChecklist()
    {
        return $this->hasMany(\common\models\Checklist::className(), ['user_id' => 'id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getTasks0()
    {
        return $this->hasMany(\common\models\Task::className(), ['creator_id' => 'id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getUserAddonRates()
    {
        return $this->hasMany(\common\models\UserAddonRate::className(), ['user_id' => 'id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getUserDepartments()
    {
        return $this->hasMany(\common\models\UserDepartment::className(), ['user_id' => 'id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getDepartments()
    {
        return $this->hasMany(\common\models\Department::className(), ['id' => 'department_id'])->viaTable('user_department', ['user_id' => 'id']);
    }
    /**
     * @return \yii\db\ActiveQuery
     */
    public function getAuths()
    {
        return $this->hasMany(\common\models\AuthItem::className(), ['name' => 'item_name'])->viaTable('auth_assignment', ['user_id' => 'id']);
    }
    /**
     * @return \yii\db\ActiveQuery
     */
    public function getUserNotifications()
    {
        return $this->hasMany(\common\models\UserNotification::className(), ['user_id' => 'id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getUserSkills()
    {
        return $this->hasMany(\common\models\UserSkill::className(), ['user_id' => 'id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getSkills()
    {
        return $this->hasMany(\common\models\Skill::className(), ['id' => 'skill_id'])->viaTable('user_skill', ['user_id' => 'id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getVacations()
    {
        return $this->hasMany(\common\models\Vacation::className(), ['user_id' => 'id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getVehicleUserReminds()
    {
        return $this->hasMany(\common\models\VehicleUserRemind::className(), ['user_id' => 'id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getVehicles()
    {
        return $this->hasMany(\common\models\Vehicle::className(), ['id' => 'vehicle_id'])->viaTable('vehicle_user_remind', ['user_id' => 'id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getNotificationSmses()
    {
        return $this->hasMany(\common\models\NotificationSms::className(), ['user_id' => 'id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getNotificationMails()
    {
        return $this->hasMany(\common\models\NotificationMail::className(), ['user_id' => 'id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getUTasks()
    {
        return $this->hasMany(\common\models\Task::className(), ['user_id' => 'id']);
    }
}
