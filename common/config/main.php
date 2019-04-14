<?php
return [
    'bootstrap' => ['bootstrap'],
    'aliases' => [
        '@bower' => '@vendor/bower-asset',
        '@npm'   => '@vendor/npm-asset',
    ],
    'vendorPath' => dirname(dirname(__DIR__)) . '/vendor',
    'components' => [
        'cache' => [
            'class' => 'yii\caching\FileCache',
        ],
        'bot' => [
            'class' => 'SonkoDmitry\Yii\TelegramBot\Component',
            'apiToken' => '721195595:AAH609acx9jLf3kDimeAJjoUygxoqyP0jO0',
        ],
        'bootstrap' => [
            'class' => common\components\BootstrapComponents::class,
        ]
    ],
];
