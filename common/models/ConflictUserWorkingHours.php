<?php


namespace common\models;


use Yii;

class ConflictUserWorkingHours {

    const CUSTOM_WORKING_TIME = 1;
    const PLANNED_VACATIONS = 2;
    const VACATIONS = 3;
    const ALREADY_WORKING = 4;
    const WORKING_IN_CLOSE_RANGE = 5;

    const OPTIONS = [
        self::CUSTOM_WORKING_TIME => [
            0 => 'Pozostawić godziny jak są',
            1 => 'Usunąć ten czas pracy'
        ],
        self::PLANNED_VACATIONS => [
            0 => 'Usunąć pracownika z wydarzenia',
            1 => 'Usunąć urlop pracownikowi'
        ],
        self::VACATIONS => [
            0 => 'Usunąć pracownika z wydarzenia',
            1 => 'Usunąć urlop pracownikowi'
        ],
    ];

    public $type;
    public $eventUserPlannedWorkingTime;
    public $vacation;
    public $first_event;
    public $second_event;

    public $selected_value;

    public function __construct($type, EventUserPlannedWrokingTime $workingTime = null, Event $firstEvent, Event $secondEvent = null, Vacation $vacation = null) {
        $this->type = $type;
        $this->first_event = $firstEvent;
        $this->second_event = $secondEvent;
        $this->eventUserPlannedWorkingTime = $workingTime;
        $this->vacation = $vacation;
    }

    public function resolveConflict() {
        switch ($this->type) {
            case self::CUSTOM_WORKING_TIME:
                return $this->resolveCustomWorkingTime();
                break;
            case self::PLANNED_VACATIONS:
                return $this->resolvePlannedVacations();
                break;
            case self::VACATIONS:
                return $this->resolveVacations();
                break;
            case self::ALREADY_WORKING:
                return $this->resolveAlreadyWorking();
                break;
            case self::WORKING_IN_CLOSE_RANGE:
                return $this->resolveWorkingInCloseRange();
                break;
        }
        return false;
    }

    private function resolveCustomWorkingTime() {
        if ($this->selected_value == 0) {
            return true;
        }
        if ($this->selected_value == 1) {
            return $this->eventUserPlannedWorkingTime->delete();
        }
        return false;
    }

    private function resolvePlannedVacations() {
        if ($this->selected_value == 0) {
            Yii::$app->runAction('crew/assign-user', [
                'id' => $this->first_event->id,
                'user_id' => $this->vacation->user_id
            ]);
            return true;
        }
        if ($this->selected_value == 1) {
            return $this->vacation->delete();
        }
        return false;
    }

    private function resolveVacations() {
        if ($this->selected_value == 0) {
            Yii::$app->runAction('crew/assign-user', [
                'id' => $this->first_event->id,
                'user_id' => $this->vacation->user_id
            ]);
            return true;
        }
        if ($this->selected_value == 1) {
            return $this->vacation->delete();
        }
        return false;
    }

    private function resolveAlreadyWorking() {
        if ($this->selected_value == 0) {
            Yii::$app->runAction('crew/assign-user', [
                'id' => $this->first_event->id,
                'user_id' => $this->eventUserPlannedWorkingTime->user_id
            ]);
            return true;
        }
        if ($this->selected_value == 1) {
            Yii::$app->runAction('crew/assign-user', [
                'id' => $this->second_event->id,
                'user_id' => $this->eventUserPlannedWorkingTime->user_id
            ]);
            return true;
        }
        return false;
    }

    private function resolveWorkingInCloseRange() {
        if ($this->selected_value == 0) {
            Yii::$app->runAction('crew/assign-user', [
                'id' => $this->first_event->id,
                'user_id' => $this->eventUserPlannedWorkingTime->user_id
            ]);
            return true;
        }
        if ($this->selected_value == 1) {
            Yii::$app->runAction('crew/assign-user', [
                'id' => $this->second_event->id,
                'user_id' => $this->eventUserPlannedWorkingTime->user_id
            ]);
            return true;
        }
        if ($this->selected_value == 2) {
            return true;
        }
        return false;
    }
}