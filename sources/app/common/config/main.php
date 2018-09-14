<?php
return [
    'aliases' => [
        '@bower' => '@vendor/bower-asset',
        '@npm'   => '@vendor/npm-asset',
    ],
    'vendorPath' => dirname(__DIR__, 2) . '/vendor',
    'components' => [
        'cache' => [
            'class' => 'yii\caching\FileCache',
        ],
        'gifter' => [
            'class' => common\gifter\Gifter::class,
            'types' => [
                [
                    'class' => \common\gifter\form\Money::class,
                    'deliveries' => [
                        \common\delivery\Bank::class,
                        \common\delivery\Convert::class
                    ],
                ],
                [
                    'class' => \common\gifter\form\Points::class,
                    'deliveries' => [
                        \common\delivery\Account::class,
                    ],
                ],
                [
                    'class' => \common\gifter\form\PhysItem::class,
                    'deliveries' => [
                        \common\delivery\PostMail::class,
                    ],
                ],
            ],
        ],
        \common\delivery\Bank::class => [
            'class' => \common\delivery\Bank::class,
            'url' => getenv('BANK_ACCOUNT_URL'),
        ],
        \common\delivery\Account::class => [
            'class' => \common\delivery\Account::class,
        ],
        \common\delivery\Convert::class => [
            'class' => \common\delivery\Convert::class,
            'ratio' => getenv('CONVERT_RATIO') ?? 1,
        ],
        \common\delivery\PostMail::class => [
            'class' => \common\delivery\PostMail::class,
        ],
    ],
];
