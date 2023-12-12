<?php

namespace Dcat\Admin\OperationLog;

use Dcat\Admin\Enums\ExtensionType;
use Dcat\Admin\Extend\ServiceProvider;
use Dcat\Admin\OperationLog\Http\Middleware\LogOperation;

class OperationLogServiceProvider extends ServiceProvider
{
    public function getExtensionType(): ExtensionType
    {
        return ExtensionType::ADDON;
    }
    
    protected $middleware = [
        'middle' => [
            LogOperation::class,
        ],
    ];

    protected $menu = [
        [
            'title' => 'Operation Log',
            'uri'   => 'auth/operation-logs',
        ],
    ];

    public function settingForm()
    {
        return new Setting($this);
    }
}
