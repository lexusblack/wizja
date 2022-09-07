<?php

namespace common\models;

use Yii;
use yii\helpers\ArrayHelper;
use \common\models\base\EnNote as BaseEnNote;

/**
 * This is the model class for table "en_note".
 */
class EnNote extends BaseEnNote
{
    /**
     * @inheritdoc
     */
    public function rules()
    {
        return array_replace_recursive(parent::rules(),
	    [
            [['company_id'], 'required'],
            [['company_id'], 'integer'],
            [['text'], 'string'],
            [['datetime'], 'safe'],
            [['link'], 'string', 'max' => 255]
        ]);
    }

    public static function getLabels()
    {
        $ids = ArrayHelper::map(EnNoteAlert::find()->where(['user_id'=>Yii::$app->user->id])->asArray()->all(), 'en_note_id', 'en_note_id');
        $d2 = date('Y-m-d', strtotime('-100 days'));
        $count = EnNote::find()->where(['NOT IN', 'id', $ids])->andWhere(['>', 'datetime', $d2])->count();
        return '<span class="label label-success">'.$count.'</span>';
    } 

    public function setAllRead()
    {
        $ids = ArrayHelper::map(EnNoteAlert::find()->where(['user_id'=>Yii::$app->user->id])->asArray()->all(), 'en_note_id', 'en_note_id');
        $notes = EnNote::find()->where(['NOT IN', 'id', $ids])->all();
        foreach ($notes as $note)
        {
            $alert = new EnNoteAlert(['user_id'=>Yii::$app->user->id, 'en_note_id'=>$note->id]);
            $alert->save();
        }
    }

    public static function createNote($type, $object)
    {
        if ($type=='CRN')
        {
            $company = Company::findOne(['code'=>$object->owner]);
            if ($company)
            {
                $note = new EnNote();
                $note->company_id = $company->id;
                $note->link = '/admin/cross-rental/index?CrossRentalSearch%5Bid%5D='.$object->id;
                $note->text = Yii::t('app', 'Dodaliśmy sprzęt do Cross Rental Network. ').$object->gearModel->name." - ".$object->quantity.Yii::t('app', 'szt.');
                $note->datetime = $object->update_time;
                $note->save();
            }
            
        }
        if ($type=='Plan')
        {
            $company = Company::findOne(['code'=>$object->owner]);
            if ($company)
            {
                $note = new EnNote();
                $note->company_id = $company->id;
                $note->link = '/admin/location/view?id='.$object->location_id;
                $note->text = Yii::t('app', 'Dodaliśmy plany techniczne do obiektu ').$object->location->name;
                $note->datetime = $object->update_time;
                $note->save();
            }
            
        }
        if ($type=='Panorama')
        {
            $company = Company::findOne(['code'=>$object->owner]);
            if ($company)
            {
                $note = new EnNote();
                $note->company_id = $company->id;
                $note->link = '/admin/location/view?id='.$object->location_id;
                $note->text = Yii::t('app', 'Dodaliśmy panoramę do obiektu ').$object->location->name;
                $note->datetime = $object->update_time;
                $note->save();
            }
            
        }
    }
	
}
