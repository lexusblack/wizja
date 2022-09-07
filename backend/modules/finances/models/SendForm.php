<?php
namespace backend\modules\finances\models;

use common\helpers\ArrayHelper;
use kartik\mpdf\Pdf;
use Yii;
use yii\base\Model;

class SendForm extends Model
{
    public $invoices = [];
    public $to = [];
    public $toString;

    public $files = [];

    public function rules()
    {
        $rules = [
            [['to'], 'required'],
            [['to'],'each', 'rule'=>['email']],
        ];
        return array_merge(parent::rules(), $rules);
    }

    public function arrayTo()
    {
        $to = preg_replace('@\s+@', '', $this->to);
        $this->to = array_unique(array_filter(explode(';', $to)));
    }

    public function stringTo()
    {
        $to = array_filter($this->to);
        $this->to = implode(';', $to);
    }

    public function attributeLabels()
    {
        return [
            'to'=>Yii::t('app', 'Adresy email'),
        ];
    }

    public function send()
    {
        $this->generateFiles();

        $mail = \Yii::$app->mailer->compose('@app/modules/finances/views/default/mail', [
            'text'=>Yii::t('app', 'W załączniku znajdują się faktury.')
        ])
            ->setFrom(Yii::$app->params['mailingEmail'])
            ->setTo($this->to)
            ->setSubject(Yii::t('app', 'Faktury'));

        foreach ($this->files as $file)
        {
            $mail->attach($file);
        }



        if ($mail->send())
        {
            foreach ($this->invoices as $invoice)
            {
                $send = new \common\models\InvoiceSend();
                $send->invoice_id = $invoice->id;
                $send->filename = \common\helpers\Inflector::slug($invoice->fullnumber).'.pdf';
                $send->datetime = date('Y-m-d H:i:s');
                $send->user_id = Yii::$app->user->id;
                $send->recipient =implode(';', $this->to);
                $send->save();
            }
            $this->updateInvoices();
            return true;
        } else {
            return false;
        }
    }

    public function getRecipients()
    {
        $data = [];

        foreach ($this->invoices as $model)
        {
            /* @var $model \common\models\Invoice; */
            if ($model->customer !== null)
            {
                $contacts = ArrayHelper::map($model->customer->contacts,'id', 'email');
                if (empty($model->customer->email) == false)
                {
                    $contacts[] = $model->customer->email;
                }
                $data = array_merge($data, $contacts);
            }

        }
        $data = ArrayHelper::cleanData($data);
        $data = array_combine($data, $data);
        return $data;
    }

    public function loadEmails()
    {

        $this->to = $this->getRecipients();
        $this->stringTo();
    }

    public function generateFiles()
    {
        if (!file_exists(Yii::getAlias('@runtime/invoice')))
        {
            mkdir(Yii::getAlias('@runtime/invoice/'));
        }
        foreach ($this->invoices as $model)
        {
            /* @var $model \common\models\Invoice; */
           $pdf = $model->loadPdf();
           $pdf->destination = Pdf::DEST_FILE;
           $pdf->filename = Yii::getAlias('@runtime/invoice/'.$pdf->filename);
           $pdf->render();
           $this->files[$model->id] = $pdf->filename;
        }

    }

    protected function updateInvoices()
    {
        foreach ($this->invoices as $invoice)
        {
            /* @var $invoice \common\models\Invoice */
            if ($invoice->hasAttribute('event_id'))
                if ($invoice->event_id !== null)
                {
                    $invoice->event->updateAttributes([
                        'invoice_sent' => 1
                        ]);
                }
        }
    }
}