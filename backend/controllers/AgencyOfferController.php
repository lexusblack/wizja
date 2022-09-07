<?php

namespace backend\controllers;

use Yii;
use common\models\AgencyOffer;
use common\models\Event;
use common\models\EventLog;
use common\models\SettingAttachment;
use common\models\form\AgencyOfferForm;
use common\models\AgencyOfferSearch;
use common\models\AgencyOfferServiceCategory;
use common\models\AgencyOfferService;
use common\models\Settings;
use backend\components\Controller;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;
use yii\web\Response;
use kartik\mpdf\Pdf;
use yii\helpers\Inflector;

/**
 * AgencyOfferController implements the CRUD actions for AgencyOffer model.
 */
class AgencyOfferController extends Controller
{
    public function behaviors()
    {
        return [
            'verbs' => [
                'class' => VerbFilter::className(),
                'actions' => [
                    'delete' => ['post'],
                ],
            ],
        ];
    }

    /**
     * Lists all AgencyOffer models.
     * @return mixed
     */
    public function actionIndex()
    {
        $searchModel = new AgencyOfferSearch();
        $dataProvider = $searchModel->search(Yii::$app->request->queryParams);

        return $this->render('index', [
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
        ]);
    }

    /**
     * Displays a single AgencyOffer model.
     * @param integer $id
     * @return mixed
     */
    public function actionView($id)
    {
        $model = $this->findModel($id);
        $offerForm = new AgencyOfferForm([
            'offer'=>$model,
        ]);
        $settings = Settings::find()->indexBy('key')->where(['section'=>'main'])->all(); 

        return $this->render('view', [
            'model' => $model,
            'offerForm' => $offerForm,
            'settings' =>$settings
        ]);
    }

    public function actionStatus($id)
    {
        Yii::$app->response->format = Response::FORMAT_JSON;
        $model = $this->findModel($id);
        $old = $model->status;
        $post = Yii::$app->request->post();
        $status = $post['AgencyOffer'][$post['editableIndex']]['status'];
        $model->status = $status;
        $model->save();
        $list = \common\models\AgencyOffer::getStatusList();
        $output = ['output'=>$list[$model->status], 'message'=>''];
        return $output;
        exit;
    }

    public function actionAssignToEvent($event_id)
    {
        $event = Event::findOne($event_id);
        if(!$event){
            throw new NotFoundHttpException( Yii::t('app', 'Strona nie istnieje.'));
        }
        $query = AgencyOffer::find()->where(['event_id' => $event_id])->orWhere(['event_id' => null]);
        $searchModel = new AgencyOfferSearch();


        $dataProvider = $searchModel->search(Yii::$app->request->queryParams);
        return $this->render('assign-to-event', [
            'dataProvider' => $dataProvider,
            'event' => $event,
            'searchModel' => $searchModel,
        ]);
    }

    public function actionOfferEvent($event_id)
    {
        Yii::$app->response->format = Response::FORMAT_JSON;
        $this->enableCsrfValidation = false;
        if (Yii::$app->request->isAjax && Event::findOne($event_id))
        {
            $params = Yii::$app->request->post();
            $model = AgencyOffer::findOne($params['itemId']);
            if(!$model){return false;}
            if ($params['add'] == 1)
            {
                $model->event_id = $event_id;
                        $eventlog = new EventLog;
                        $eventlog->event_id = $model->event_id;
                        $eventlog->user_id = Yii::$app->user->identity->id;
                        $eventlog->content =  Yii::t('app', "Do eventu dodano ofertę").": ".$model->name;
                        $eventlog->save();
                return $model->save();
            }
            else
            {
                $model->event_id = null;
                return $model->save();
            }

        } else {
            return false;
        }
        
    }

    

    /**
     * Creates a new AgencyOffer model.
     * If creation is successful, the browser will be redirected to the 'view' page.
     * @return mixed
     */
    public function actionCreate($event_id=null)
    {
        $model = new AgencyOffer();
        $model->event_id = $event_id;
        date_default_timezone_set(Yii::$app->params['timeZone']);
        $model->offer_date = date('Y-m-d H:i:s');
        $event = Event::findOne($event_id);
        if ($event)
        {
            $model->customer_id = $event->customer_id;
            $model->manager_id = $event->manager_id;
            $model->contact_id = $event->contact_id;
            $model->location_id = $event->location_id;
            $model->event_start = $event->event_start;
            $model->event_end = $event->event_end;
            $model->name = $event->name;
        }
        if ($model->load(Yii::$app->request->post()) && $model->save()) {
            $model->createServices();
            return $this->redirect(['view', 'id' => $model->id]);
        } else {
            return $this->render('create', [
                'model' => $model,
            ]);
        }
    }

    public function actionCreateEvent($id)
    {
        $model = new Event;
        $offer = $this->findModel($id);
        $model->name = $offer->name;
        $model->customer_id = $offer->customer_id;
        $model->contact_id = $offer->contact_id;
        $model->location_id = $offer->location_id;
        $model->manager_id = $offer->manager_id;
        $model->event_start = $offer->event_start;
        $model->event_end = $offer->event_end;
        $model->save();
        $offer->event_id = $model->id;
        $offer->save();
        return $this->redirect(['/event/update', 'id'=>$model->id]);

    }

    /**
     * Updates an existing AgencyOffer model.
     * If update is successful, the browser will be redirected to the 'view' page.
     * @param integer $id
     * @return mixed
     */
    public function actionUpdate($id)
    {
        $model = $this->findModel($id);

        if ($model->load(Yii::$app->request->post()) && $model->save()) {
            return $this->redirect(['view', 'id' => $model->id]);
        } else {
            return $this->render('update', [
                'model' => $model,
            ]);
        }
    }

    /**
     * Deletes an existing AgencyOffer model.
     * If deletion is successful, the browser will be redirected to the 'index' page.
     * @param integer $id
     * @return mixed
     */
    public function actionDelete($id)
    {
        $this->findModel($id)->delete();

        return $this->redirect(['index']);
    }

    
    /**
     * Finds the AgencyOffer model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     * @param integer $id
     * @return AgencyOffer the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function findModel($id)
    {
        if (($model = AgencyOffer::findOne($id)) !== null) {
            return $model;
        } else {
            throw new NotFoundHttpException('The requested page does not exist.');
        }
    }


    public function actionCategoryOrder()
    {
        $i = 0;
        foreach (Yii::$app->request->post('bigitem') as $value) {
            $model = AgencyOfferServiceCategory::findOne($value);
            $model->position = $i;
            $model->save();
            $i++;
        }
        exit;
    }
    public function actionServiceOrder()
    {
        $i = 0;
        foreach (Yii::$app->request->post('item') as $value) {
            $model = AgencyOfferService::findOne($value);
            $model->position = $i;
            $model->save();
            $i++;
        }
        exit;
    }

    public function actionServiceDelete($id)
    {
            $model = AgencyOfferService::findOne($id);
            $model->delete();
            Yii::$app->response->format = Response::FORMAT_JSON;
            $response = ['id'=>$id];
            return $response;
    }

    public function actionCategoryDelete($id)
    {
            $model = AgencyOfferServiceCategory::findOne($id);
            $model->delete();
            Yii::$app->response->format = Response::FORMAT_JSON;
            $response = ['id'=>$id];
            return $response;
    }


    public function actionServiceCreate($id, $category)
    {
        $model = new AgencyOfferService();
        $model->category_id = $category;
        $model->agency_offer_id = $id;
        $model->count = 1;
        $model->position = AgencyOfferService::find()->where(['category_id'=>$category])->count()+1;
        $model->name = "";
        $model->save();
        $model = AgencyOfferService::find()->where(['id'=>$model->id])->asArray()->one();
        Yii::$app->response->format = Response::FORMAT_JSON;
        $response = $model;
        return $response;
    }
    public function actionEditService($id)
    {
        $services = Yii::$app->request->post();
        $services = $services['AgencyOfferForm']['services'];
        foreach ($services as $key=>$val)
        {
            $model = AgencyOfferService::findOne($key);
            if (!intval($val['count']))
                $val['count'] = 0;
            if (!floatval($val['price']))
                $val['price'] = 0;
            if (!floatval($val['client_price']))
                $val['client_price'] = 0;
            $model->name = $val['name'];
            $model->count = intval($val['count']);
            $model->price = floatval($val['price']);
            $model->client_price= floatval($val['client_price']);
            $model->info=$val['info'];
            $model->total_price = $val['client_price']*$val['count'];
            $model->total_profit = $model->total_price-$val['price']*$val['count'];
            $model->save();
        }
        Yii::$app->response->format = Response::FORMAT_JSON;
        $response = AgencyOfferService::find()->where(['id'=>$model->id])->asArray()->one();
        return $response;
    }

    public function actionCreateCategory($id)
    {
        $model = new AgencyOfferServiceCategory();
        $model->position = AgencyOfferServiceCategory::find(['agency_offer_id'=>$id])->count()+1;
        $model->provizion = 1;
        $model->color = "#273a4a";
        $model->agency_offer_id = $id;

        if ($model->load(Yii::$app->request->post())) {
            $model->save();
            $model = AgencyOfferServiceCategory::find()->where(['id'=>$model->id])->asArray()->one();
            Yii::$app->response->format = Response::FORMAT_JSON;
            $response = $model;
            return $response;
        }else if (Yii::$app->request->isAjax) {
            return $this->renderAjax('_formCategory', [
                        'model' => $model
            ]);
        }
    }

    public function actionEditOffer($id, $provision)
    {
        $model = $this->findModel($id);
        $model->provision = $provision;
        $model->save();
             Yii::$app->response->format = Response::FORMAT_JSON;
            $response = $model;
            return $response;       
    }

    public function actionServiceCategoryUpdate($id)
    {
        $model = AgencyOfferServiceCategory::findOne($id);

        if ($model->load(Yii::$app->request->post())) {
            $model->save();
            $model = AgencyOfferServiceCategory::find()->where(['id'=>$model->id])->asArray()->one();
            Yii::$app->response->format = Response::FORMAT_JSON;
            $response = $model;
            return $response;
        }else if (Yii::$app->request->isAjax) {
            return $this->renderAjax('_formCategory', [
                        'model' => $model
            ]);
        }
    }

    public function actionDuplicate($id)
    {
        $model = $this->findModel($id);
        $clone = new AgencyOffer;
        $clone->attributes = $model->attributes;
        
        $clone->offer_date = date('Y-m-d');
        $clone->name = $model->name." (". Yii::t('app', "nowa").")";
        if ($clone->save()) {
            $categories = AgencyOfferServiceCategory::find()->where(['agency_offer_id'=>$id])->all();
            foreach ($categories as $category)
            {
                $clone_cat = new AgencyOfferServiceCategory;
                $clone_cat->attributes = $category->attributes;
                $clone_cat->agency_offer_id = $clone->id;
                $clone_cat->save();
                foreach ($category->agencyOfferServices as $service)
                {
                    $clone_service = new AgencyOfferService;
                    $clone_service->attributes = $service->attributes;
                    $clone_service->category_id = $clone_cat->id;
                    $clone_service->agency_offer_id = $clone->id;
                    $clone_service->save(); 
                }
            }
            return $this->redirect(['view', 'id' => $clone->id]);
        } else {
           return $this->redirect(['index']);
        }
    }

    public function actionExcel($id)
    {
        $view = $this->findModel($id);
        $data = $this->prepareExcelData($view);
        $file = $this->createExcel($data, $view);

        // Save on disk
        $file->send($view->name.'.xlsx');
    }

    protected function createExcel($data, $view)
    {
         $file = \Yii::createObject([
            'class' => 'codemix\excelexport\ExcelFile',
            'sheets' => [

                mb_substr($view->name, 0, 31) => [   // Name of the excel sheet
                    'data' => $data['data'],

                    // Set to `false` to suppress the title row
                    'titles' => false
                ],
            ]
        ]);
        foreach(range('A','F') as $columnID) {
            $file->getWorkbook()->getSheet(0)->getColumnDimension($columnID)
                ->setAutoSize(true);
        }
        foreach ($data['bold'] as $i)
        {
            $file->getWorkbook()->getSheet(0)->getStyle("A".$i.":E".$i)->getFont()->setBold(true);
        }
        foreach ($data['underline'] as $i)
        {
            $file->getWorkbook()->getSheet(0)->getStyle("A".$i.":E".$i)->getFont()->setUnderline(true);
        }
        foreach ($data['border'] as $i)
        {
            $file->getWorkbook()->getSheet(0)->getStyle("A".$i.":E".$i)->getBorders()->getBottom()->setBorderStyle(\PHPExcel_Style_Border::BORDER_THIN);
        }
        return $file;       
    }

    public function actionPdf($id)
    {
        $pdf = $this->preparePDF($id);
        return $pdf->render();
    }

    protected function preparePDF($id, $distination=null){
        $dist = isset($distination) ? $distination : Pdf::DEST_BROWSER;
        
        $model = $this->findModel($id);
        $offerForm = new AgencyOfferForm([
            'offer'=>$model,
        ]);
        $settings = Settings::find()->indexBy('key')->where(['section'=>'main'])->all(); 

        $content = $this->renderPartial('pdf', [
            'model' =>  $model,
            'settings' => $settings,
            'offerForm'=>$offerForm
        ]);

        $header = $this->renderPartial('pdf-header', [
            'model' =>  $model,
            'settings' => $settings
        ]);

        $footer = $this->renderPartial('pdf-footer', [
            'model' =>  $model,
            'settings' => $settings
        ]);

        // setup kartik\mpdf\Pdf component
        $pdf = new Pdf([
            // set to use core fonts only
                'mode' => Pdf::MODE_UTF8,
            // A4 paper format
                'format' => Pdf::FORMAT_A4,
            // portrait orientation
                'orientation' => Pdf::ORIENT_PORTRAIT,
            // stream to browser inline
                'destination' => $dist,
            // your html content input
                'content' => $content,
            // format content from your own css file if needed or use the
            // enhanced bootstrap css built by Krajee for mPDF formatting
                'marginTop' => 45,
                'marginBottom' => 30,
                'cssFile' => '@webroot/themes/e4e/css/pdf_offer.css',
            // any css to be embedded if required
                'cssInline' => '.pdf_box .offer_table {
                    border: 2px solid #000;
                    width: 100%;
                }

                .pdf_box .logo img {
                    max-width: 100%;
                    max-height: 200px;
                    height: 200px;
                    width: auto;
                }',
            // set mPDF properties on the fly
                'options' => ['title' => $model->name],
                'filename' => Yii::getAlias('@uploadroot').'/offer/'.Inflector::slug($model->name).'.pdf',
            // call mPDF methods on the fly
                'methods' => [
                        'SetHeader'=>$header,
                        'SetFooter'=>$footer,
                ]
        ]);
        
        $pdf->options = [
            'defaultheaderline' => 0,
            'defaultfooterline' => 0        ];
        return $pdf;
    }

    public function actionSendMail($id)
    {
        $model = new \backend\models\SendAgencyOfferMail();
        $model->offerId = $id;
        $offer = $this->findModel($id);
        if ($model->load(Yii::$app->request->post()) && $model->validate()){

//          if  (empty($model->email) == false)
//          {
//              $model->recipients[$model->email] = $model->email;
//          }

            $mail = \Yii::$app->mailer->compose('@app/modules/offers/views/default/mail', [
                'model' =>  $model,
            ])
            ->setFrom([Yii::$app->params['mailingEmail']=>Yii::$app->user->identity->email])
            ->setTo($model->recipients)
            ->setSubject($model->subject)
            ->setReplyTo(Yii::$app->user->identity->email);

            if($model->attachPDF){
                $pdf = $this->preparePDF($id,Pdf::DEST_FILE);
                $pdf->render();
                $filename = Inflector::slug($offer->name);
                $mail->attach($pdf->filename);
                $atts = SettingAttachment::find()->where(['type'=>SettingAttachment::TYPE_OFFER])->all();
                foreach ($atts as $a)
                {
                    $mail->attach($a->getFilePath());
                }
            }
            if($model->attachExcel){
                $data = $this->prepareExcelData($offer);
                $file = $this->createExcel($data, $offer);

                $file->saveAs(Yii::getAlias('@uploadroot/xls/'.$offer->name.'.xlsx'));
                $mail->attach(Yii::getAlias('@uploadroot/xls/'.$offer->name.'.xlsx'));
            }             
            if ($mail->send())
            {
                $model->updateEvent();
                Yii::$app->session->setFlash('success',  Yii::t('app', 'Email wysłany!'));
            } else {
                Yii::$app->session->setFlash('danger',  Yii::t('app', 'Błąd!'));
            }

            return $this->redirect(['view', 'id'=>$id]);
        } 
        $model->attachExcel = true;
        return $this->render('send-mail', [
            'model' => $model,
        ]);
    }


protected function prepareExcelData($view)
    {
        $bold_row = [5,6,11,16];
        $underline_row = [11, 16];
        $border_row =[];
        $formatter = Yii::$app->formatter;
        $offerForm = new AgencyOfferForm([
            'offer'=>$view,
        ]);
        $model = $view;
        $data[] = ["", "", "", "",  Yii::t('app', "Nazwa projektu:"), $model->name];
        $data[] = ["", "", "", "",  Yii::t('app', "Numer:"), $model->id];
        $data[] = ["", "", "", "",  Yii::t('app', "Termin:"), $model->event_start."do ".$model->event_end];
        $data[] = ["", "", "", "",  Yii::t('app', "Data oferty:"),         $model->offer_date];
        $data[] = [ Yii::t('app', "Zamawiajacy:")];
        $data[] = [$model->customer->name, "", "","",  Yii::t('app', "Kierownik projektu:"), $model->manager->first_name." ".$model->manager->last_name];
        $data[] = [$model->customer->address." ".$model->customer->zip." ".$model->customer->city, "", "", "", "",  Yii::t('app', "tel:").$model->manager->phone];
        $data[] = [ Yii::t('app', "tel:").$model->customer->phone, "", "", "", "",  Yii::t('app', "e-mail:").$model->manager->email];
        $data[] = [ Yii::t('app', "e-mail:").$model->customer->email];
        $data[] = [""];
        $data[] = [ Yii::t('app', "Miejsce i adres:")];
        $data[] = [$model->location->name];
        $data[] = [$model->location->address];
        $data[] = [$model->location->city." ".$model->location->zip];
        $row=14;
        $total_summ_of_cats = 0;
        $total_summ_of_services_provision = 0;
        foreach($offerForm->serviceCategories as $category): 
                $summ_of_one_cat = 0;
                $data[] = [""];
                $row++;
                $data[] = [$category['name']];
                $row++;
                $bold_row[]=$row;
                $underline_row[]=$row;
                $data[] = [ Yii::t('app', "Nazwa"),  Yii::t('app', "Cena"),  Yii::t('app', "Liczba"), Yii::t('app', "Razem netto"), Yii::t('app', "Uwagi")];
                $row++;
                $bold_row[]=$row;
                $border_row[] =$row;
                foreach ($category['items'] as $service):
                            $data[] = [$service['name'], $formatter->asCurrency($service['client_price']), $service['count'], $formatter->asCurrency($service['total_price']), $service['info']];
                            $row++;
                            $border_row[] =$row;
                            $summ_of_one_cat += $service['total_price'];
                endforeach;
                $data[] = [Yii::t('app', "Łącznie")." ".$category['name'], $formatter->asCurrency($summ_of_one_cat)];
                $row++;
                $bold_row[]=$row;
                $total_summ_of_cats += $summ_of_one_cat;
                if ($category['provizion'])
                        $total_summ_of_services_provision +=$summ_of_one_cat; 
        endforeach;
        $data[] = [""];
        $row++;
                    $sum_netto = $total_summ_of_cats;
                    $provision = $total_summ_of_services_provision*$model->provision/100;
                    $vat = ($sum_netto+$provision)*0.23;
                    $sum_brutto = $sum_netto+$provision+$vat;
        $data[] = [Yii::t('app', "Podsumowanie")];
        $row++;
        $data[] = [""];
        $bold_row[]=$row;
        $data[] = [Yii::t('app', "Suma netto:"), $formatter->asCurrency($sum_netto)];
        $data[] = ["Wynagrodzenie agencji [".$view->provision."%]:" , $formatter->asCurrency($provision)];
        $data[] = [Yii::t('app', "VAT:"), $formatter->asCurrency($vat)];
        $data[] = [Yii::t('app', "Suma brutto:"), $formatter->asCurrency($sum_brutto)];
        return ['data'=>$data, 'bold'=>$bold_row, 'underline'=>$underline_row, 'border'=>$border_row];        
    }
}
