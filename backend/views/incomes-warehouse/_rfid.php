<?php

use common\models\MobileQrScan;

$this->registerJs( '

    var added = [];
    var added_mobile = [];
	added_mobile[' . MobileQrScan::TYPE_GEAR_OUR . '] = []
	added_mobile[' . MobileQrScan::TYPE_GEAR_OUTER . '] = []
	added_mobile[' . MobileQrScan::TYPE_CASE . '] = [];

    setInterval(function() {
        
        $.ajax({
           url: "/admin/rfid/last-readings",
            method: "get",
            success: function(items) {
                for (var i = 0; i < items.length; i++) {
                    if (items[i][0] == "gear") {
                        if (items[i][2] == "code") {
                            if ($.inArray(items[i][3], added) == -1) {
                                added[added.length] = items[i][3];
                                addRowGearItem(items[i][1]);
                            }
                        }
                    }
                    
                    if (items[i][0] == "case") {
                        addCaseRfid(items[i][1], true);
                    }
                }
               console.log(items);
            }
        });
        $.ajax({
            url: "/admin/mobile-scan/last-readings?seconds=5",
            method: "get",
            success: function(items) {
                for (var i = 0; i < items.length; i++) {
                
                    if (items[i][0] == ' . MobileQrScan::TYPE_GEAR_OUR . ') {
                        if ($.inArray(items[i][1], added_mobile[' . MobileQrScan::TYPE_GEAR_OUR . ']) == -1) {
                            added_mobile[' . MobileQrScan::TYPE_GEAR_OUR . '][added_mobile[' . MobileQrScan::TYPE_GEAR_OUR . '].length] = items[i][1];
                            addRowGearItem(items[i][1]);
                        }
                    }
                    
                    if (items[i][0] == ' . MobileQrScan::TYPE_GEAR_OUTER . ') {
                        if ($.inArray(items[i][1], added_mobile[' . MobileQrScan::TYPE_GEAR_OUTER . ']) == -1) {
                            added_mobile[' . MobileQrScan::TYPE_GEAR_OUTER . '][added_mobile[' . MobileQrScan::TYPE_GEAR_OUTER . '].length] = items[i][1];
                            addOuterGearItem(items[i][1], 1);
                        }
                    }
                    
                    if (items[i][0] == ' . MobileQrScan::TYPE_CASE . ') {
                        if ($.inArray(items[i][1], added_mobile[' . MobileQrScan::TYPE_CASE . ']) == -1) {
                           added_mobile[' . MobileQrScan::TYPE_CASE . '][added_mobile[' . MobileQrScan::TYPE_CASE . '].length] = items[i][1];
                           addGearGroup(items[i][1]);

                        }
                    }
                }
//                console.log(added_mobile[' . MobileQrScan::TYPE_GEAR_OUR . ']);
//                console.log(added_mobile[' . MobileQrScan::TYPE_GEAR_OUTER . ']);
//                console.log(added_mobile[' . MobileQrScan::TYPE_CASE . ']);
            }
        });
    
    }, 5000);

');