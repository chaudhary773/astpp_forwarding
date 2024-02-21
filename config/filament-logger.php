<?php

use App\Filament\Resources\ActivityResource;
use App\Models\CallBlock;
use App\Models\Campaign;
use App\Models\Did;
use App\Models\Target;
use App\Models\User;
use Z3d0X\FilamentLogger\Loggers\AccessLogger;
use Z3d0X\FilamentLogger\Loggers\ModelLogger;
use Z3d0X\FilamentLogger\Loggers\NotificationLogger;
use Z3d0X\FilamentLogger\Loggers\ResourceLogger;

return [
    'datetime_format' => 'd/m/Y H:i:s',
    'date_format' => 'd/m/Y',

    'activity_resource' =>  ActivityResource::class,

    'resources' => [
        'enabled' => true,
        'log_name' => 'Resource',
        'logger' => ResourceLogger::class,
        'color' => 'success',
        'exclude' => [
            //App\Filament\Resources\UserResource::class,
        ],
    ],

    'access' => [
        'enabled' => true,
        'logger' => AccessLogger::class,
        'color' => 'danger',
        'log_name' => 'Access',
    ],

    'notifications' => [
        'enabled' => true,
        'logger' => NotificationLogger::class,
        'color' => null,
        'log_name' => 'Notification',
    ],

    'models' => [
        'enabled' => true,
        'log_name' => 'Model',
        'color' => 'warning',
        'logger' => ModelLogger::class,
        'register' => [
            User::class,
            Campaign::class,
            Target::class,
            Did::class,
            CallBlock::class
        ],
    ],

    'custom' => [
        // [
        //     'log_name' => 'Custom',
        //     'color' => 'primary',
        // ]
    ],
];
