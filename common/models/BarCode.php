<?php


namespace common\models;


class BarCode {

    // ean13 barcode == 13 digits

    // First two numbers
    const SINGEL_PRODUCT = '00';
    const ITEMS_GROUP = '01';
    const MODEL = '02';
    // next two numbers
    const OUR_WAREHOUSE = '00';
    const OUTER_WAREHOUSE = '01';

    // next 9 are product id
}