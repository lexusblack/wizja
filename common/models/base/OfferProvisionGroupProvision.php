<?php

namespace common\models\base;

use Yii;

/**
 * This is the base model class for table "offer_provision_group_provision".
 *
 * @property integer $id
 * @property integer $offer_provision_group_id
 * @property string $section
 * @property string $value
 * @property integer $type
 */
class OfferProvisionGroupProvision extends \yii\db\ActiveRecord
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
            [['offer_provision_group_id', 'type'], 'integer'],
            [['value'], 'number'],
            [['section'], 'string', 'max' => 255]
        ];
    }

    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'offer_provision_group_provision';
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'offer_provision_group_id' => 'Offer Provision Group ID',
            'section' => 'Section',
            'value' => 'Value',
            'type' => 'Type',
        ];
    }
}
