<?php

namespace backend\controllers;

use dosamigos\qrcode\lib\Enum;
use dosamigos\qrcode\QrCode;
use yii\web\Controller;

class QrCodeController extends Controller {

    public function actionGetImg($text) {
        return QrCode::png($text);
    }

    public function actionGetBigImg($text) {
        return QrCode::png($text, false, Enum::QR_ECLEVEL_L, 30);
    }

}