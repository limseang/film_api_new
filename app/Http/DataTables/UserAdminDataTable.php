<?php

namespace App\Http\DataTables;

use Illuminate\Database\Eloquent\Builder as QueryBuilder;
use App\Models\User;
use Yajra\DataTables\EloquentDataTable;
use Yajra\DataTables\Html\Builder as HtmlBuilder;
use Yajra\DataTables\Html\Button;
use Yajra\DataTables\Html\Column;
use Yajra\DataTables\Services\DataTable;
// str
use Illuminate\Support\Str;
use App\Constant\RolePermissionConstant;

class UserAdminDataTable extends DataTable
{
    private $tableName = 'users';
    /**
     * Build the DataTable class.
     *
     * @param QueryBuilder $query Results from query() method.
     */
    public function dataTable(QueryBuilder $query): EloquentDataTable
    {
        return (new EloquentDataTable($query))
            ->addIndexColumn()
            ->setRowClass(function ($table) {
                return "row_reload_".$table->id;
            })
            ->addColumn('action', function ($table) {
                return view('user.action', ['table' => $table]);
            })
            ->editColumn('created_at', function ($table) {
                return dateTimeFormat($table->created_at);
            })
            ->editColumn('role_name', function ($table) {
                return '<span class="'.config('setup.badge_info').'">'.$table->role_name .'</span>';
            })
            ->editColumn('point', function($table){
                return '<span class="'.config('setup.badge_warning').'">'.number_format($table->point) .'</span>';
            })
            ->editColumn('', function($table){
                return '<span class="'.config('setup.badge_primary').'">'.Str::ucfirst($table->user_type_name) .'</span>';
            })
            ->editColumn('comeFrom', function($table){
                return '<span class="'.config('setup.badge_primary').'">'.Str::ucfirst($table->comeFrom) .'</span>';
            })
            ->editColumn('email', function ($table) {
                return hiddenPrivacy($table->email);
            })
            ->editColumn('icon', function ($table) {
                $pic = $table->avatar_url;
                return '<img src="'.$pic.'" class="img-preview rounded" style="cursor:pointer" onclick="showImage(this)">';
            })
            ->editColumn('status', function ($table) {
                $publish_status = ($table->status == '1') ? '<span class="'.config('setup.badge_success').'">'.trans('sma.publish_yes').'</span>' : '<span class="'.config('setup.badge_danger').'">'.trans('sma.publish_no').'</span>';
                return $publish_status;
            })
            ->rawColumns(['status', 'icon','role_name','point','user_type_name','comeFrom']) #allowed for using html code here
        ;
    }

    /**
     * Get the query source of dataTable.
     */
    public function query(User $model): QueryBuilder
    {
        $model = $model->newQuery();
        $model->select(['id','name', 'email', 'avatar', 'phone', 'role_id','comeFrom', 'status', 'point' , 'user_type','created_at', 'deleted_at' ]);
        if (request('name')) {
            $model->where(function ($query) {
                $query->orWhere('name', 'like', '%' . request('name') . '%');
            });
        }
        if (request('publish')) {
            $model->where('status', request('publish'));
        }

        if (request('soft_delete')) {
            if (request('soft_delete') == 'deleted') {
                $model->withTrashed();
                $model->where($this->tableName . '.deleted_at', '!=', null);
            }
            elseif (request('soft_delete') == 'all_records') {
                $model->withTrashed();
            }
        }
        $model->orderBy('updated_at', 'DESC');
        return $model;
    }

    /**
     * Optional method if you want to use the html builder.
     */
    public function html(): HtmlBuilder
    {
        return $this->builder()
            ->setTableId($this->tableName . '-table')
            ->columns($this->getColumns())
            ->ajax([
                'data' => 'function(d) {
                            d.name = $("#name").val();
                            d.publish = $("#publish").val();
                            d.soft_delete = $("#soft_delete").val();
                        }'
            ])
            ->parameters([
                'initComplete' => 'function() {
                            $("#filter").submit(function(event) {
                                event.preventDefault();
                                $("#' . $this->tableName . '-table").DataTable().ajax.reload();
                            });
                        }'
            ])
            ->orderBy([3, "ASC"])
        ;
    }

    /**
     * Get the dataTable columns definition.
     */
    public function getColumns(): array
    {
        if(authorize(RolePermissionConstant::PERMISSION_USER_EDIT) || authorize(RolePermissionConstant::PERMISSION_USER_DELETE) || authorize(RolePermissionConstant::PERMISSION_USER_CHANGE_STATUS)){
            $columns= [
                Column::computed('action', trans('global.action'))->exportable(false)->printable(false)->width(50)->addClass('text-center')
            ];
        }
        $columns[] = Column::make('icon')->title(trans('sma.icon'))->width(10)->addClass('text-center');
        $columns[] = Column::make('name', 'name')->title(trans('sma.name'))->width(30);
        $columns[] = Column::make('email')->title(trans('sma.email'))->width(10)->addClass('text-center');
        $columns[] = Column::make('phone')->title(trans('sma.phone'))->width(10)->addClass('text-center');
        $columns[] = Column::make('role_name')->title(trans('sma.role_name'))->width(10)->addClass('text-center');
        $columns[] = Column::make('point')->title(trans('sma.point'))->width(10)->addClass('text-center');
        $columns[] = Column::make('user_type_name')->title(trans('sma.user_type_name'))->width(10)->addClass('text-center');
        $columns[] = Column::make('comeFrom')->title(trans('sma.comeFrom'))->width(10)->addClass('text-center');
        $columns[] = Column::make('status', 'status')->title(trans('sma.status'))->width(10)->addClass('text-center');
        $columns[] = Column::make('created_at')->title(trans('sma.created_at'))->width(10)->addClass('text-center');

        return $columns;
    }

    /**
     * Get the filename for export.
     */
    protected function filename(): string
    {
        return $this->tableName.'_' . date('YmdHis');
    }
}
