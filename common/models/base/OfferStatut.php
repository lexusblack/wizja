<?php

namespace common\models\base;

use Yii;
use yii\behaviors\TimestampBehavior;

/**
 * This is the base model class for table "offer_statut".
 *
 * @property integer $id
 * @property string $name
 * @property integer $is_send
 * @property integer $is_accepted
 */
class OfferStatut extends \yii\db\ActiveRecord
{
    use \mootensai\relation\RelationTrait;


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
             [['is_send', 'is_accepted', 'visible_in_planning', 'visible_in_finances', 'blocked'], 'integer'],
            [['name', 'color'], 'string', 'max' => 255]
        ];
    }

    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'offer_statut';
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'name' => Yii::t('app', 'Nazwa'),
            'is_send' => Yii::t('app', 'Status "ofera wysłana" lub późniejszy'),
            'is_accepted' => Yii::t('app', 'Licz budżet produkcyjny'),
            'visible_in_finances' => Yii::t('app', 'Widoczna w finansach'),
            'visible_in_planning' => Yii::t('app', 'Widoczna w planowaniu'),
            'reminder_sms' => Yii::t('app', 'Powiadomienie SMS'),
            'reminder_mail' => Yii::t('app', 'Powiadomienie Mail'),
            'reminder_text' => Yii::t('app', 'Tekst powiadomienia'),
            'groups' => Yii::t('app', 'Odbiorcy powiadomienia grupy'),
            'reminder_pm' => Yii::t('app', 'Powiadomienie dla PM'),
            'users' => Yii::t('app', 'Odbiorcy powiadomienia'),
            'blocked' => Yii::t('app', 'Blokuje możliwość edycji'),
        ];
    }


}
