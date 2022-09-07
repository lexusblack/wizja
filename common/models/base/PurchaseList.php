<?php

namespace common\models\base;

use Yii;


/**
 * This is the base model class for table "purchase_list".
 *
 * @property integer $id
 * @property string $name
 * @property string $datetime
 * @property integer $status
 *
 * @property \common\models\PurchaseListItem[] $purchaseListItems
 */
class PurchaseList extends \yii\db\ActiveRecord
{
    use \mootensai\relation\RelationTrait;


    /**
    * This function helps \mootensai\relation\RelationTrait runs faster
    * @return array relation names of this model
    */
    public function relationNames()
    {
        return [
            'purchaseListItems'
        ];
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['datetime'], 'safe'],
            [['status'], 'integer'],
            [['name'], 'string', 'max' => 45]
        ];
    }

    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'purchase_list';
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'name' => Yii::t('app', 'Nazwa'),
            'datetime' => Yii::t('app', 'Data utworzenia'),
            'status' => 'Status',
        ];
    }
    
    /**
     * @return \yii\db\ActiveQuery
     */
    public function getPurchaseListItems()
    {
        return $this->hasMany(\common\models\PurchaseListItem::className(), ['purchase_list_id' => 'id'])->orderBy(['position'=>SORT_ASC]);
    }

        /**
     * @return \yii\db\ActiveQuery
     */
    public function getUser()
    {
        return $this->hasOne(\common\models\User::className(), ['id' => 'user_id']);
    }


    }
