<?php

return [
        'class' => 'yii\swiftmailer\Mailer',
        'viewPath' => '@app/views/mail',
        // send all mails to a file by default. You have to set
        // 'useFileTransport' to false and configure a transport
        // for the mailer to send real emails.
        'useFileTransport' => false,
        'transport' => [
            'class' => 'Swift_SmtpTransport',
       //     'constructArgs'=>['softwebo.pl', 587],
            // // 'host' => 'localhost',
            // // 'username' => 'username',
            // // 'password' => 'password',
            // // 'port' => '587',
            // 'encryption' => 'tls',
            'host'=>'serwer1684510.home.pl',
            'username' => 'notifications@newsystems.pl',
            'password' => 'Maciek8430',
            'port' => 587,
		]
	];
