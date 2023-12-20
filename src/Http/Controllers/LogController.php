<?php

namespace Dcat\Admin\OperationLog\Http\Controllers;

use Dcat\Admin\Admin;
use Dcat\Admin\Enums\RouteAuth;
use Dcat\Admin\Grid;
use Dcat\Admin\Http\JsonResponse;
use Dcat\Admin\Layout\Content;
use Dcat\Admin\OperationLog\Models\OperationLog;
use Dcat\Admin\OperationLog\OperationLogServiceProvider;
use Dcat\Admin\Support\Helper;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Arr;

class LogController
{
    public function index(Content $content)
    {
        return $content
            ->title(OperationLogServiceProvider::trans('log.title'))
            ->description(trans('admin.list'))
            ->body($this->grid());
    }

    protected function grid()
    {
        return new Grid(OperationLog::with('user'), function (Grid $grid) {

            if(!Admin::user()->isAdministrator()) {
                $grid->model()
                    ->where('user_id', Admin::id())
                    ->orWhereHas('user', function (Builder $query) {
                        $query->where('manager_id', Admin::id());
                    });
            }

            $grid->column('id', 'ID')->sortable();
            $grid->column('user', trans('admin.user'))
                ->display(function ($user) {
                    if (! $user) {
                        return;
                    }

                    $user = Helper::array($user);

                    return $user['name'] ?? ($user['username'] ?? $user['id']);
                })
                ->link(function () {
                    if ($this->user) {
                        return admin_route(RouteAuth::USERS()).'/'.$this->user['id'];
                    }
                }, '');

            $grid->column('method', trans('admin.method'))
                //->label(OperationLog::$methodColors)
                ->filterByValue();

            $grid->column('path', trans('admin.uri'))->display(function ($v) {
                return "<code>$v</code>";
            })->filterByValue();

            $grid->column('ip', 'IP')->filterByValue();

            $grid->column('input')->display(function ($input) {
                $input = json_decode($input, true);

                if (empty($input)) {
                    return;
                }

                $input = Arr::except($input, ['_pjax', '_token', '_method', '_previous_']);

                if (empty($input)) {
                    return;
                }

                return '<pre class="dump" style="max-width: 500px">'.json_encode($input, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE).'</pre>';
            });

            $grid->column('created_at', trans('admin.created_at'));

            $grid->model()->orderBy('id', 'DESC');

            $grid->disableCreateButton();

            $grid->disableActions();
            $grid->showColumnSelector();
            $grid->setActionClass(Grid\Displayers\Actions::class);

            //todo::fix
            //Dcat\Admin\Layout\Asset::css(): Argument #1 ($css) must be of type array|string, null given, called in /var/www/html/funded3/vendor/mikha-dev/dcat-admin/src/Layout/Asset.php on line 428
            // $grid->filter(function (Grid\Filter $filter) {
            //     $userModel = config('admin.database.users_model');

            //     $filter->in('user_id', trans('admin.user'))
            //         ->multipleSelect($userModel::pluck('name', 'id'));

            //     $filter->equal('method', trans('admin.method'))
            //         ->select(
            //             array_combine(OperationLog::$methods, OperationLog::$methods)
            //         );

            //     $filter->like('path', trans('admin.uri'));
            //     $filter->equal('ip', 'IP');
            //     $filter->between('created_at')->datetime();
            // });
        });
    }

    public function destroy($id)
    {
        $ids = explode(',', $id);

        OperationLog::destroy(array_filter($ids));

        return JsonResponse::make()
            ->success(trans('admin.delete_succeeded'))
            ->refresh()
            ->send();
    }
}
