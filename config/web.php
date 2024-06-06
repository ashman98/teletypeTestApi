<?php

$params = require __DIR__ . '/params.php';
$db = require __DIR__ . '/db.php';

$config = [
    'id' => 'basic',
    'basePath' => dirname(__DIR__),
    'bootstrap' => ['log'],
    'aliases' => [
        '@bower' => '@vendor/bower-asset',
        '@npm'   => '@vendor/npm-asset',
    ],
    'components' => [
        'request' => [
            // !!! insert a secret key in the following (if it is empty) - this is required by cookie validation
            'cookieValidationKey' => 'RlqMj-O6ylsdX1S3Xba9aj_XeIfBbOg-',
        ],
        'cache' => [
            'class' => 'yii\caching\FileCache',
        ],
        'errorHandler' => [
            'errorAction' => 'site/error',
        ],
        'mailer' => [
            'class' => \yii\symfonymailer\Mailer::class,
            'viewPath' => '@app/mail',
            // send all mails to a file by default.
            'useFileTransport' => true,
        ],
        'log' => [
            'traceLevel' => YII_DEBUG ? 3 : 0,
            'targets' => [
                [
                    'class' => 'yii\log\FileTarget',
                    'levels' => ['error', 'warning'],
                ],
                [
                    'class' => 'yii\log\MyFileTarget',
                    'levels' => ['info'],
                    'categories' => [
                        'clients'
                    ],
                    'except' => [
                        'yii\web\HttpException:404',
                    ],
                    'logFile' => '@app/runtime/logs/Clients.log',
                    'logVars' => [],
                ],
                [
                    'class' => 'yii\log\MyFileTarget',
                    'exportInterval' => 1,
                    'levels' => ['info'],
                    'categories' => [
                        'operators'
                    ],
                    'except' => [
                        'yii\web\HttpException:404',
                    ],
                    'logFile' => '@app/runtime/logs/Operators.log',
                    'logVars' => [],
                ],
                [
                    'class' => 'yii\log\MyFileTarget',
                    'levels' => ['info'],
                    'categories' => [
                        'teletype_errors'
                    ],
                    'except' => [
                        'yii\web\HttpException:404',
                    ],
                    'logFile' => '@app/runtime/logs/TeletypeErrors.log',
                    'logVars' => [],
                ],
            ],
        ],
        'db' => $db,

        'urlManager' => [
            'enablePrettyUrl' => true,
            'showScriptName' => false,
            'rules' => [
                'POST webhook' => 'site/webhook',
            ],
        ],
    ],
    'params' => $params,
];

if (YII_ENV_DEV) {
    // configuration adjustments for 'dev' environment
    $config['bootstrap'][] = 'debug';
    $config['modules']['debug'] = [
        'class' => 'yii\debug\Module',
        // uncomment the following to add your IP if you are not connecting from localhost.
        'allowedIPs' => ['5.159.100.5', '185.10.187.220'],
    ];

    $config['bootstrap'][] = 'gii';
    $config['modules']['gii'] = [
        'class' => 'yii\gii\Module',
        // uncomment the following to add your IP if you are not connecting from localhost.
//        'allowedIPs' => ['185.10.187.220'],
    ];
}

return $config;
