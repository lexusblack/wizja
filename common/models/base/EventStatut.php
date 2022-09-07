<?php

namespace common\models\base;

use Yii;

/**
 * This is the base model class for table "event_statut".
 *
 * @property integer $id
 * @property string $name
 * @property string $color
 * @property integer $position
 * @property integer $active
 * @property integer $blocks_costs
 * @property integer $blocks_working_times
 * @property integer $blocks_status_revert
 * @property integer $blocks_gear
 * @property integer $blocks_event
 * @property integer $reminder
 * @property string $reminder_text
 * @property string $reminder_roles
 * @property string $reminder_users
 * @property integer $type
 */
class EventStatut extends \yii\db\ActiveRecord
{


    /**
    * This function helps \mootensai\relation\RelationTrait runs faster
    * @return array relation names of this model
    */
    public function relationNames()
    {
        return [
            ''
        ];
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['position', 'active', 'blocks_costs', 'blocks_working_times', 'blocks_status_revert', 'blocks_gear', 'blocks_event', 'reminder', 'type', 'reminder_pm', 'reminder_mail', 'reminder_sms', 'delete_gear', 'delete_crew','delete_task', 'button', 'show_in_dashboard', 'show_in_plantimeline', 'border', 'button', 'hall_reserved'], 'integer'],
            [['reminder_text'], 'string'],
            [['name', 'reminder_roles', 'reminder_users'], 'string', 'max' => 255],
            [['color', 'icon'], 'string', 'max' => 45]
        ];
    }

    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'event_statut';
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'name' => Yii::t('app', 'Nazwa'),
            'color' => Yii::t('app', 'Kolor'),
            'position' => 'Position',
            'active' => 'Active',
            'blocks_costs' => Yii::t('app', 'Blokuje możliwość dodania kosztów'),
            'blocks_working_times' => Yii::t('app', 'Blokuje możliwość dodania czasów pracy'),
            'blocks_status_revert' => Yii::t('app', 'Blokuje możliwość zmiany statusu na wcześniejszy'),
            'blocks_gear' => Yii::t('app', 'Blokuje możliwość zmian w sprzęcie'),
            'blocks_event' => Yii::t('app', 'Blokuje możliwość edycji wydarzenia/wypożyczenia'),
            'reminder' => Yii::t('app', 'Powiadomienie PUSH'),
            'reminder_sms' => Yii::t('app', 'Powiadomienie SMS'),
            'reminder_mail' => Yii::t('app', 'Powiadomienie Mail'),
            'reminder_text' => Yii::t('app', 'Tekst powiadomienia'),
            'roles' => Yii::t('app', 'Odbiorcy powiadomienia role'),
            'reminder_pm' => Yii::t('app', 'Powiadomienie dla PM'),
            'users' => Yii::t('app', 'Odbiorcy powiadomienia'),
            'type' => 'Type',
            'border' => Yii::t('app', 'Szerokość ramki w kalendarzu      [px]'),
            'button' => Yii::t('app', 'Przycisk w podglądzie wydarzenia/wypożyczenia'),
            'icon' => Yii::t('app', 'Ikona'),
            'count_provision' => Yii::t('app', 'Licz prowizję Project Managera'),
            'permissions'=> Yii::t('app', 'Ogranicz możliwość dnadawania statusu dla wybranych użytkowników'),
            'delete_gear'=> Yii::t('app', 'Usuń rezerwację sprzętu'),
            'delete_crew'=> Yii::t('app', 'Usuń rezerwację ekipy'),
            'delete_task'=> Yii::t('app', 'Usuń zadania'),
            'show_in_dashboard'=> Yii::t('app', 'Pokazuj w kokpicie'),
            'show_in_plantimeline'=> Yii::t('app', 'Pokazuj w Plan Timeline'),
            'hall_reserved' => Yii::t('app', "Rezerwacja potwierdzona [dotyczy wydarzeń z wynajęciem powierzchni]")
        ];
    }
}
