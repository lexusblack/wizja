<?php
return [
    'components' => [
        'db' => [
            'class' => 'yii\db\Connection',
            'dsn' => 'mysql:host=localhost;dbname=07641946_0000048',
            'username' => '07641946_0000048',
            'password' => 'ok0WcZ6vqxqo',
            'charset' => 'utf8',
        ],
        'mailer' => [
            'class' => 'yii\swiftmailer\Mailer',
            'viewPath' => '@common/mail',
             // 'transport' => [
	            // 'class' => 'Swift_SmtpTransport',
	            // // 'host' => 'localhost',
	            // // 'username' => 'username',
	            // // 'password' => 'password',
	            // // 'port' => '587',
	            // // 'encryption' => 'tls',
	            // 'host'=>'softwebo.pl',
	    		// 'username' => 'no-reply@softwebo.pl',
				// 'password' => 'Z0JdUn4kjk.',
				// 'port' => 587,
	        // ],
        ],
    ],
];
