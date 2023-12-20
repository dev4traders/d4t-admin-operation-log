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

    const URL_OP_LOG = 'operation-logs';

    const PERMISSION_OP_LOG = 'mng.opertion_logs';

    protected $middleware = [
        'middle' => [
            LogOperation::class,
        ],
    ];

    protected $menu = [
        [
            'title' => 'Operation Log',
            'icon' => 'fa-folder-open',
            'uri'   => 'operation-logs',
            'permission_slug' => self::PERMISSION_OP_LOG
        ],
    ];

    protected array $permissions = [
        [
            'slug' => self::PERMISSION_OP_LOG,
            'name' => 'Manager Opeartions Logs',
            'path' => self::URL_OP_LOG,
        ],
    ];

    public function settingForm()
    {
        return new Setting($this);
    }
}
