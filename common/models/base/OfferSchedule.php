<?php

namespace common\models\base;

use Yii;

/**
 * This is the base model class for table "offer_schedule".
 *
 * @property integer $id
 * @property integer $offer_id
 * @property string $name
 * @property integer $position
 * @property string $start
 * @property string $end
 * @property string $prefix
 * @property integer $is_required
 * @property integer $book_gears
 */
class OfferSchedule extends \yii\db\ActiveRecord
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
            [['offer_id', 'position', 'is_required', 'book_gears'], 'integer'],
            [['start_time', 'end_time'], 'safe'],
            [['name', 'prefix', 'translate'], 'string', 'max' => 45]
        ];
    }

    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'offer_schedule';
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'name' => Yii::t('app', 'Nazwa'),
            'translate' => Yii::t('app', 'Tłumaczenie na PDF'),
            'is_required' => Yii::t('app', 'Pole obowiązkowe'),
            'book_gears' => Yii::t('app', 'Domyślnie rezerwuj sprzęt na ten etap'),
            'dateRange' => Yii::t('app', 'Czas trwania'),
            'prefix' => Yii::t('app', 'Prefix - wyświetlany w kalendarzu')
        ];
    }

        /**
     * @return \yii\db\ActiveQuery
     */
    public function getOffer()
    {
        return $this->hasOne(\common\models\Offer::className(), ['id' => 'offer_id']);
    }
}
