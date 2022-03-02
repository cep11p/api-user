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
            'cookieValidationKey' => '-VAkoFLzpLw8ya3ysPo77PA21O8bth9G',
            'parsers' => [
                'application/json' => 'yii\web\JsonParser',
            ]
        ],
        
        'i18n' => [
            'translations' => [
                '*' => [
                    'class' => 'yii\i18n\PhpMessageSource',
                    'basePath' => '@app/messages', // if advanced application, set @frontend/messages
                    'sourceLanguage' => 'es_ES',
                    'fileMap' => [
                        //'main' => 'main.php',
                    ],
                ],
            ],
            
        ],
        
        'cache' => [
            'class' => 'yii\caching\FileCache',
        ],
        'errorHandler' => [
            'errorAction' => 'site/error',
        ],
        'mailer' => [
            'class' => 'yii\swiftmailer\Mailer',
            // send all mails to a file by default. You have to set
            // 'useFileTransport' to false and configure a transport
            // for the mailer to send real emails.
            'useFileTransport' => true,
        ],
        'log' => [
            'traceLevel' => YII_DEBUG ? 3 : 0,
            'targets' => [
                [
                    'class' => 'yii\log\FileTarget',
                    'levels' => ['error', 'warning'],
                ],
            ],
        ],
        'db' => $db,
        
        'urlManager' => [
            'enablePrettyUrl' => true,
            'showScriptName' => false,
            'enableStrictParsing' => false,
            'rules' => [
                [   #Modulo
                    'class' => 'yii\rest\UrlRule',
                    'controller' => 'api/modulo'  
                ],
                [   #UsuarioModulo
                    'class' => 'yii\rest\UrlRule',
                    'controller' => 'api/usuario-modulo'  
                ],
                /****** USUARIOS *******/
                [   #Usuario
                    'class' => 'yii\rest\UrlRule',
                    'controller' => 'api/usuario',   
                    'extraPatterns' => [
                        'POST login' => 'login',
                        'OPTIONS login' => 'options',
                        'OPTIONS listar-asignacion/{id}' => 'listar-asignacion',
                        'GET listar-asignacion/{id}' => 'listar-asignacion',
                        'OPTIONS asignar-modulo' => 'asignar-modulo',
                        'POST asignar-modulo' => 'asignar-modulo',
                        'OPTIONS desasignar-modulo' => 'desasignar-modulo',
                        'POST desasignar-modulo' => 'desasignar-modulo',
                        'OPTIONS borrar-asignacion' => 'borrar-asignacion',
                        'POST borrar-asignacion' => 'borrar-asignacion',
                        'OPTIONS baja/{id}' => 'baja',
                        'PUT baja/{id}' => 'baja',
                        'OPTIONS buscar-persona-por-cuil/{cuil}' => 'buscar-persona-por-cuil',
                        'GET buscar-persona-por-cuil/{cuil}' => 'buscar-persona-por-cuil',
                    ],
                    'tokens' => ['{id}'=>'<id:\\w+>', '{cuil}'=>'<cuil:\\w+>'],                       
                ]
            ],
        ],
        
    ],
    
    'modules' => [
        'user' => [
            'class' => 'dektrium\user\Module',
            'enableConfirmation'=>false,
            'admins'=>['admin']
        ],
        'api' => [
            'class' => 'app\modules\api\Api',
        ],
        "audit"=>[
            "class"=>"bedezign\yii2\audit\Audit",
            "ignoreActions" =>['audit/*', 'debug/*'],
            'userIdentifierCallback' => ['app\components\ServicioUsuarios', 'userIdentifierCallback'],
            'userFilterCallback' => ['app\components\ServicioUsuarios', 'userFilterCallback'],
            'accessIps'=>null,
            'accessUsers'=>null,
            'accessRoles'=>['admin']
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
        //'allowedIPs' => ['127.0.0.1', '::1'],
    ];

    $config['bootstrap'][] = 'gii';
    $config['modules']['gii'] = [
        'class' => 'yii\gii\Module',
        // uncomment the following to add your IP if you are not connecting from localhost.
        'allowedIPs' => ['*'],
    ];
}

return $config;
