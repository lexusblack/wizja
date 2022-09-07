<?php
// This class was automatically generated by a giiant build task
// You should not change it manually as it will be overwritten on next build

namespace common\models\base;

use Yii;

/**
 * This is the base-model class for table "outcomes_warehouse".
 *
 * @property integer $id
 * @property integer $user
 * @property string $start_datetime
 * @property string $comments
 *
 * @property \common\models\OutcomesForCustomer[] $outcomesForCustomers
 * @property \common\models\OutcomesForEvent[] $outcomesForEvents
 * @property \common\models\OutcomesForRent[] $outcomesForRents
 * @property \common\models\OutcomesGearOur[] $outcomesGearOurs
 * @property \common\models\OutcomesGearOuter[] $outcomesGearOuters
 * @property \common\models\User $user0
 * @property string $aliasModel
 */
abstract class OutcomesWarehouse extends \common\components\BaseActiveRecord
{



    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'outcomes_warehouse';
    }


    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['user', 'start_datetime', 'comments'], 'required'],
            [['user'], 'integer'],
            [['start_datetime', 'warehouse_id'], 'safe'],
            [['comments'], 'string'],
            [['user'], 'exist', 'skipOnError' => true, 'targetClass' => User::className(), 'targetAttribute' => ['user' => 'id']]
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => Yii::t('app', 'ID'),
            'user' => Yii::t('app', 'Użytkownik'),
            'start_datetime' => Yii::t('app', 'Początek'),
            'comments' => Yii::t('app', 'Komentarz'),
        ];
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getOutcomesForCustomers()
    {
        return $this->hasMany(\common\models\OutcomesForCustomer::className(), ['outcome_id' => 'id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getOutcomesForEvents()
    {
        return $this->hasMany(\common\models\OutcomesForEvent::className(), ['outcome_id' => 'id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getOutcomesForRents()
    {
        return $this->hasMany(\common\models\OutcomesForRent::className(), ['outcome_id' => 'id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getOutcomesGearOurs()
    {
        return $this->hasMany(\common\models\OutcomesGearOur::className(), ['outcome_id' => 'id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getOutcomesGearOuters()
    {
        return $this->hasMany(\common\models\OutcomesGearOuter::className(), ['outcome_id' => 'id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getUser0()
    {
        return $this->hasOne(\common\models\User::className(), ['id' => 'user']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getWarehouse()
    {
        return $this->hasOne(\common\models\Warehouse::className(), ['id' => 'warehouse_id']);
    }


}